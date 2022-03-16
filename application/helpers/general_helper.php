<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

use \Curl\Curl;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\BadResponseException;

if (!function_exists('api_request')) {

    function api_request($method, $credential, $api_endpoint, $data = '')
    {
        $CI = &get_instance();
        $client = new Client();

        // Credentials !(Required in every transaction)!
        $endpointAuth  = base_url() . 'api/member/auth';
        $endpointApi  = base_url() . $api_endpoint;
        $key = $CI->db->get_where('keys', array('name' => 'TELEGRAM_API_ACCESS'))->row();

        $responseLogin = '';
        if (isset($credential['token'])) {
            $isLoggedIn = TRUE;
            $responseLogin = array(
                'token' =>  $credential['token']
            );
        } else {
            $isLoggedIn = FALSE;
        }

        if (!$credential || empty($credential)) {
            $response['message'] = 'No Credential Set!';
            return $response;
        }

        // Check login first to get api_token to pass in next data json sending
        if (!$isLoggedIn) {
            try {

                $checkLogin = $client->request(
                    $method,
                    $endpointAuth,
                    [
                        'form_params' => $credential, // Sending data credentials
                        //'debug' => TRUE, 
                        'headers' => [
                            'Content-type' => 'application/x-www-form-urlencoded',
                            'MB-KEY' => $key->key,
                        ]
                    ]
                );

                $responseLogin = $checkLogin->getBody()->getContents();
                $responseLogin = json_decode($responseLogin, true);

                // echo '<pre>', print_r($responseLogin, 1), '</pre>';
                // die;

            } catch (ClientException $e) { // Show login error
                $responseLogin = $e->getResponse()->getBody();
                $responseLogin = json_decode($responseLogin, true);

                //print_r($response);
                return $responseLogin;
            }
        }

        if (isset($responseLogin['status']) && !$responseLogin['status']) {
            return $responseLogin;
        }

        //print_r($credential['token']);die;

        // If any data to send
        // No data will return token (for logged in only)
        if (!$data) {
            return $responseLogin;
        } else {
            if ($isLoggedIn) {
                $token = $credential['token'];
            } else {
                $token = $responseLogin['token'];
            }
            try {
                $client = $client->request(
                    $method,
                    $endpointApi,
                    [
                        'form_params' => $data,
                        'headers' => [
                            'Content-type' => 'application/x-www-form-urlencoded',
                            'Authorization' => 'Bearer ' . $token
                        ],
                    ]
                );

                $response = $client->getBody()->getContents();
                $response = json_decode($response, true);

                //print_r($response);
                return $response;
            } catch (ClientException $e) {
                $response = $e->getResponse()->getBody();
                $response = json_decode($response, true);

                //print_r($response);
                return $response;
            }
        }
    }
}
