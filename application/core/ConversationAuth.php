<?php

use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;

class ConversationAuth extends Conversation
{

    protected $username;
    protected $password;

    public function __construct($lang)
    {
        $this->language = $lang;
    }

    /**
     * @return mixed
     */
    public function run()
    {
        $this->askUsername();
    }

    /* -------------------------------------------------------------------------- */
    /*                                 askUsername                                */
    /* -------------------------------------------------------------------------- */
    public function askUsername()
    {
        if ($this->language == 'id') {
            $questionText = "Masukkan Username anda : ...";
        } else if ($this->language == 'en') {
            $questionText = 'Enter your Username : ...';
        }
        $this->ask(Question::create($questionText), function (Answer $answer) {
            if ($answer->getText()) {
                $this->username = strtolower($answer->getText());
                $this->askPassword();
            } else {
                $this->askUsername();
            };
        });
    }

    /* -------------------------------------------------------------------------- */
    /*                                   askPassword                              */
    /* -------------------------------------------------------------------------- */
    public function askPassword()
    {
        if ($this->language == 'id') {
            $questionText = "Masukkan Password anda : ...";
        } else if ($this->language == 'en') {
            $questionText = "Enter your Password : ...";
        }
        $this->ask(Question::create($questionText), function (Answer $answer) {
            if ($answer->getText()) {
                $this->password = $answer->getText();
                $this->callApi();
            } else {
                $this->askUsername();
            }
        });
    }

    /* -------------------------------------------------------------------------- */
    /*                                 callApi                                 */
    /* -------------------------------------------------------------------------- */
    public function callApi()
    {
        $response = $this->apiRequestLogin();
        if (isset($response['status']) && $response['status'] == TRUE) {
            // Save Auth Token & Telegram ID
            $this->saveToken($response);
            $this->saveTelegramId();
            if ($this->language == 'id') {
                $this->say('Selamat.. Anda berhasil login sebagai : <b>' . $response['data']['username'] . '</b>', ['parse_mode' => 'HTML']);
                $this->say('Untuk menampilkan menu-menu member' . PHP_EOL . 'Ketik atau klik => /menumember');
            } else if ($this->language == 'en') {
                $this->say('Congratulations.. You successfuly logged in as : <b>' . $response['data']['username'] . '</b>', ['parse_mode' => 'HTML']);
                $this->say('To show member menu' . PHP_EOL . 'Type or click => /menumember');
            }
        } else {
            if ($this->language == 'id') {
                $this->say('Mohon maaf login anda tidak berhasil dengan alasan :');
            } else if ($this->language == 'en') {
                $this->say('Sorry, your login was not successfully for the following reasons :');
            }
            if ($response['message']) {
                $this->say('<b>' . $response['message'] . '</b>', ['parse_mode' => 'HTML']);
            } else {
                $this->say('<b>Unknown Response!</b>', ['parse_mode' => 'HTML']);
            }

            if ($this->language == 'id') {
                $this->say('Silahkan ulangi proses input login anda');
                $this->say('Untuk berhenti dari proses login ini' . PHP_EOL . 'Ketik atau klik => /stoplogin');
            } else if ($this->language == 'en') {
                $this->say('Please repeat your input login proccess');
                $this->say('To stop this login process' . PHP_EOL . 'Type or click => /stoplogin');
            }
            // back to first ask
            $this->askUsername();
        }
    }

    /* -------------------------------------------------------------------------- */
    /*                                 apiRequest                                 */
    /* -------------------------------------------------------------------------- */
    public function apiRequestLogin()
    {
        // 'username' => 'testapi',
        // 'password' => '',

        $data = array(
            'username' => $this->username,
            'password' => $this->password,
        );

        $apiRequest = api_request('POST', $data, 'api/member/auth');
        return $apiRequest;
    }

    /* -------------------------------------------------------------------------- */
    /*                                 saveToken                                  */
    /* -------------------------------------------------------------------------- */
    public function saveToken($response)
    {
        $this->bot->userStorage()->save([
            'token' => $response['token'],
            'name' => $response['data']['name'],
            'username' => $response['data']['username'],
            'id' => $response['data']['id'],
        ]);
        return TRUE;
    }

    /* -------------------------------------------------------------------------- */
    /*                                saveTelegramId                              */
    /* -------------------------------------------------------------------------- */
    public function saveTelegramId()
    {
        $CI = &get_instance();

        $user       = $this->bot->getUser();
        $telegramId = $user->getId();
        $username   = $this->bot->userStorage()->find()->get('username');

        $data = array(
            'telegram_id' => $telegramId
        );

        $CI->db->where('username', $username);
        $CI->db->update('tbl_user', $data);
    }
}
