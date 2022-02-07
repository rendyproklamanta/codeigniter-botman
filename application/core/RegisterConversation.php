<?php

use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;

class RegisterConversation extends Conversation
{

    protected $firstName;
    protected $lastName;

    /**
     * @return mixed
     */
    public function run()
    {
        $this->askFirstName();
    }

    public function askFirstName()
    {
        $this->ask(Question::create("Masukkan Nama Depan Anda :"), function (Answer $answer) {
            if ($answer->getText()) {
                $this->firstName = $answer->getText();

                $register = new Register();
                $response = $register->cekSponsor($this->firstName);

                if ($response) {
                    $this->say($response);
                    $this->askFirstName();
                } else {
                    $this->askLastName();
                }
            } else {
                // go back and ask for the name again
                $this->askFirstName();
            }
        });
    }

    public function askLastName()
    {
        $this->ask(Question::create("Masukkan Nama Belakang Anda :"), function (Answer $answer) {
            if ($answer->getText()) {
                $this->lastName = $answer->getText();
                $this->askConfirm();
            } else {
                $this->askLastName();
            }
        });
    }

    public function askConfirm()
    {
        $question = Question::create('Apakah anda yakin untuk memproses data pendaftaran ini?')
            ->addButtons([
                Button::create('Ya')->value('yes'),
                Button::create('Tidak')->value('no'),
            ]);

        $this->ask($question, function (Answer $answer) {
            // Detect if button was clicked:
            if ($answer->isInteractiveMessageReply()) {
                if ($answer->getValue() == 'yes') {
                    $request = $this->apiRequest();
                    if ($request['status']) {
                        $this->say('Selamat.. Pendaftaran anda sukses diproses');
                        $this->sendFinalMessage();
                    } else {
                        $this->say($request['message']);
                    }
                } else {
                    $this->say('Baik.. saya akan mengulangi proses pendaftaran anda');
                    $this->askFirstName();
                }
            }
        });
    }

    public function askConfirmText()
    {
        $this->ask('Shall we proceed? Say YES or NO', [
            [
                'pattern' => 'yes|yep',
                'callback' => function () {
                    $request = $this->apiRequest();
                    if ($request['status']) {
                        $this->say('Selamat.. Pendaftaran anda sukses diproses');
                        $this->sendFinalMessage();
                    } else {
                        $this->say($request['message']);
                    }
                }
            ],
            [
                'pattern' => 'nah|no|nope',
                'callback' => function () {
                    $this->say('Baik.. saya akan mengulangi proses pendaftaran anda');
                    $this->askFirstName();
                }
            ]
        ]);
    }

    public function sendFinalMessage()
    {
        $this->say('Berikut data yang kami dapatkan:');
        $this->say("Sponsor: {$this->firstName}\nName: {$this->lastName}");
    }

    public function apiRequest()
    {
        //$authorization = "Authorization: Bearer " . $token;
        $authorization = '';

        $data = array(
            'first_name'  => $this->firstName,
            'last_name'  => $this->lastName,
        );
        $data_string = json_encode($data);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL             => base_url('api/endpoint'),
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_SSL_VERIFYHOST  => false,
            CURLOPT_SSL_VERIFYPEER  => false,
            CURLOPT_ENCODING        => "",
            CURLOPT_MAXREDIRS       => 10,
            CURLOPT_TIMEOUT         => 30,
            CURLOPT_CUSTOMREQUEST   => "POST",
            CURLOPT_POSTFIELDS      => $data_string,
            CURLOPT_HTTPHEADER      => array(
                "content-type: application/x-www-form-urlencoded",
                $authorization,
            ),
        ));

        // Get response from API
        $response   = curl_exec($curl);
        $err        = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return json_encode($err);
        } else {
            return json_encode($response);
        }
    }
}

class Register extends CI_Model
{
    function cekSponsor($firstName)
    {
        // Check Data Sponsor
        return TRUE;
    }
}
