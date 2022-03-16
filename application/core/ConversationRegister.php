<?php

use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;

class ConversationRegister extends Conversation
{

    protected $varOne;
    protected $varTwo;
    protected $varThree;
    protected $varFour;
    protected $varFive;

    public function __construct($lang, $token = '', $memberName = '', $memberUsername = '', $memberId = '')
    {
        $this->language = $lang;
        $this->token = $token; # if any token set isLoggedIn
        $this->memberName = $memberName;
        $this->memberUsername = $memberUsername;
        $this->memberId = $memberId;
    }

    /**
     * @return mixed
     */
    public function run()
    {
        //var_dump($this->memberName);
        $this->checkUserLogin();
    }

    /* -------------------------------------------------------------------------- */
    /*                               checkUserLogin                               */
    /* -------------------------------------------------------------------------- */
    public function checkUserLogin()
    {
        if ($this->token) {
            if ($this->language == 'id') {
                $this->say('Anda terdeteksi login sebagai : ' . $this->memberUsername);
                $this->say('Nama Sponsor : ' . $this->memberName);
            } else if ($this->language == 'en') {
                $this->say('You login detected as : ' . $this->memberUsername);
                $this->say('Sponsor Name : ' . $this->memberName);
            }
            // Show Typing...
            $this->bot->typesAndWaits(1);
        }

        if ($this->language == 'id') {
            $this->say('Untuk membatalkan atau memulai ulang proses pendaftaran.' . PHP_EOL . 'Ketik atau klik => /stopregister ');
        } else if ($this->language == 'en') {
            $this->say('To cancel or restart the registration process' . PHP_EOL . 'Type or click => /stopregister ');
        }

        $this->varOne();
    }

    /* -------------------------------------------------------------------------- */
    /*                                 varOne                                 */
    /* -------------------------------------------------------------------------- */
    public function varOne()
    {
        if ($this->token) {
            $this->varOneName = $this->memberUsername;
            $this->askUpline();
        } else {
            if ($this->language == 'id') {
                $questionText = "Masukkan varOne : ...";
            } else if ($this->language == 'en') {
                $questionText = "Enter your varOne : ...";
            }
            $this->ask(Question::create($questionText), function (Answer $answer) {
                if ($answer->getText()) {
                    $this->sponsor = strtolower($answer->getText());

                    //$register = new Register();
                    $response = $this->apiRequestSearchMember('varOne', $this->varOne);
                    if ($response['status']) {
                        $varOneName = $response['data']['name'];
                        if ($this->language == 'id') {
                            $this->say('Nama varOne : ' . $varOneName);
                        } else if ($this->language == 'en') {
                            $this->say('varOne Name : ' . $varOneName);
                        }
                        $this->varTwo();
                    } else {
                        $this->say($response['message']);
                        $this->varOne();
                    }
                } else {
                    $this->varOne();
                }
            });
        }
    }

    /* -------------------------------------------------------------------------- */
    /*                                  varTwo                                 */
    /* -------------------------------------------------------------------------- */
    public function varTwo()
    {
        if ($this->language == 'id') {
            $questionText = "Masukkan varTwo anda : ...";
        } else if ($this->language == 'en') {
            $questionText = "Enter your varTwo : ...";
        }
        $this->ask(Question::create($questionText), function (Answer $answer) {
            if ($answer->getText()) {
                $this->varTwo = strtolower($answer->getText());

                $response = $this->apiRequestSearchMember('varTwo', $this->varTwo);
                if ($response['status']) {
                    //$this->say($response['message']);
                    $this->varThree();
                } else {
                    $this->say($response['message']);
                    $this->varTwo();
                }
            } else {
                $this->varTwo();
            }
        });
    }

    /* -------------------------------------------------------------------------- */
    /*                                 varThree                                 */
    /* -------------------------------------------------------------------------- */
    public function varThree()
    {
        if ($this->language == 'id') {
            $questionText = "Silahkan pilih yang diinginkan : ";
            $buttonRight = "Kanan";
            $buttonLeft = "Kiri";
        } else if ($this->language == 'en') {
            $questionText = "Please select :";
            $buttonRight = "Right";
            $buttonLeft = "Left";
        }
        $question = Question::create($questionText)
            ->addButtons([
                Button::create($buttonRight)->value('right'),
                Button::create($buttonLeft)->value('left'),
            ]);

        $this->ask($question, function (Answer $answer) {
            $this->position = $answer->getText();

            // Detect if button was clicked:
            if ($answer->isInteractiveMessageReply()) {
                if ($answer->getValue()) {
                    if ($this->language == 'id') {
                        $this->say('Pilihan yang anda pilih : ' . $answer->getValue());
                    } else if ($this->language == 'en') {
                        $this->say('You choose : ' . $answer->getValue());
                    }
                    $this->varFour();
                }
            } else {
                if ($this->language == 'id') {
                    $this->say('Anda belum memilih pilihan');
                } else if ($this->language == 'en') {
                    $this->say('You have not selected an options');
                }
                $this->varThree();
            }
        });
    }


    /* -------------------------------------------------------------------------- */
    /*                                 askConfirm                                 */
    /* -------------------------------------------------------------------------- */
    public function askConfirm()
    {
        if ($this->language == 'id') {
            $yes = 'Ya';
            $no = 'Tidak';
            $questionText = "Apakah anda yakin untuk memproses data pendaftaran ini?\nPilih " . $yes . " / " . $no;
        } else if ($this->language == 'en') {
            $yes = 'Yes';
            $no = 'No';
            $questionText = "Are you sure to process this registration data?\nChoose " . $yes . " / " . $no;
        }
        $question = Question::create($questionText)
            ->addButtons([
                Button::create($yes)->value('yes'),
                Button::create($no)->value('no'),
            ]);

        if ($this->token) {
            $this->say("varOne: <b>{$this->varOne}</b>\nvarTwo: <b>{$this->varTwo}</b>\nvarThree: <b>{$this->varThree}</b>", ['parse_mode' => 'HTML']);
        } else {
            $this->say("varOne: <b>{$this->varOne}</b>\nvarTwo: <b>{$this->varTwo}</b>", ['parse_mode' => 'HTML']);
        }

        $this->ask($question, function (Answer $answer) {

            // Detect if button was clicked:
            if ($answer->isInteractiveMessageReply()) {
                if ($answer->getValue() == 'yes') {

                    if ($this->language == 'id') {
                        $this->say('Mohon menunggu.. Pendaftaran anda sedang diproses..');
                    } else if ($this->language == 'en') {
                        $this->say('Please Wait.. Your Registration is Processing..');
                    }

                    $request = $this->apiRequestRegister();
                    if (isset($request['status']) && $request['status'] == TRUE) {
                        if ($this->language == 'id') {
                            $this->say('<b>Selamat.. Pendaftaran anda sukses diproses</b>', ['parse_mode' => 'HTML']);
                        } else if ($this->language == 'en') {
                            $this->say('<b>Congratulations.. Your registration has been successfully processed</b>', ['parse_mode' => 'HTML']);
                        }
                        $this->sendFinalMessage();
                    } else {
                        if ($this->language == 'id') {
                            $this->say('Mohon maaf pendaftaran anda tidak berhasil diproses dengan alasan :');
                        } else if ($this->language == 'en') {
                            $this->say('Sorry, your registration was not successfully processed for the following reasons :');
                        }

                        if ($request['message']) {
                            $this->say('<b>' . $request['message'] . '</b>', ['parse_mode' => 'HTML']);
                        } else {
                            $this->say('<b>Unknown Response!</b>', ['parse_mode' => 'HTML']);
                        }
                        if (isset($request['validation_errors']) && $request['validation_errors']) {
                            $this->say('<b>' . $request['validation_errors'] . '</b>', ['parse_mode' => 'HTML']);
                        }

                        if ($this->language == 'id') {
                            $this->say('Silahkan ulangi pendaftaran anda dan perbaiki data yg salah');
                            $this->say('Untuk berhenti dari proses pendaftaran ini' . PHP_EOL . 'Ketik atau klik => /stopregister');
                        } else if ($this->language == 'en') {
                            $this->say('Please repeat your registration and correct the wrong data');
                            $this->say('To stop this registration process' . PHP_EOL . 'Type or click => /stopregister');
                        }
                        $this->varOne();
                    }
                } else {
                    if ($this->language == 'id') {
                        $this->say('Baik.. saya akan mengulangi proses pendaftaran anda');
                    } else if ($this->language == 'en') {
                        $this->say('Ok.. I will undo and repeat your registration process');
                    }
                    $this->varOne();
                }
            } else {
                if ($this->language == 'id') {
                    $this->say('Silahkan pilih Ya/Tidak pilihan dibawah ini');
                } else if ($this->language == 'en') {
                    $this->say('Please choose Yes/No options below');
                }
                $this->askConfirm();
            }
        });
    }

    /* -------------------------------------------------------------------------- */
    /*                              sendFinalMessage                              */
    /* -------------------------------------------------------------------------- */
    public function sendFinalMessage()
    {
        if ($this->language == 'id') {
            $this->say('Silahkan cek email anda untuk informasi lebih lanjut.');
            $this->bot->sendRequest('sendSticker', [
                'sticker' => 'CAACAgIAAxkBAAILiWIGpHpbq4iTTd1kuuRWCIy8x5YRAALhCwAC6FQ4SkshVu6ShcugIwQ'
            ]);
        } else if ($this->language == 'en') {
            $this->say('Please check your email for more information.');
        }
    }

    /* -------------------------------------------------------------------------- */
    /*                            apiRequestSearchMember                          */
    /* -------------------------------------------------------------------------- */
    public function apiRequestSearchMember($param)
    {
        if ($param == 'member') {
            if ($this->token) {
                $endpoint = 'api/member/searchusername';
            } else {
                $endpoint = 'api/member/searchusername/noauth';
            }

            $data = array(
                'member' => $this->varOne,
            );
        }

        if ($param == 'staff') {
            $endpoint = 'api/member/searchstaff';
            $data = array(
                'staff' => $this->varTwo,
            );
        }

        if ($this->token) {
            $credential = array(
                'token' => $this->token,
            );
            $apiRequest = api_request('POST', $credential, $endpoint, $data);
        }

        return $apiRequest;
    }

    /* -------------------------------------------------------------------------- */
    /*                                 apiRequest                                 */
    /* -------------------------------------------------------------------------- */
    public function apiRequestRegister()
    {

        $data = array(
            'varOne' => $this->varOne,
            'varTwo' => $this->varTwo,
            'varThree' => $this->varThree,
        );

        if ($this->token) {
            $credential = array(
                'token' => $this->token,
            );
            $apiRequest = api_request('POST', $credential, 'api/member/reg', $data);
            if ($apiRequest['message'] == 'Token Time Expire.') {
                $this->botman->reply($apiRequest['message']);
                if ($this->language == 'id') {
                    $this->say('Silahkan stop proccess ini :' . PHP_EOL . 'Klik /stop dan klik /login');
                } else if ($this->language == 'en') {
                    $this->say('Please stop this process :' . PHP_EOL . 'Click /stop and click /login');
                }
                exit;
            }
        } else {
            //$credential = credential('member');
            //$apiRequest = api_request_not_credential('POST', 'api/member/referralreg/' . $this->sponsor, $data);
        }

        return $apiRequest;
    }
}
