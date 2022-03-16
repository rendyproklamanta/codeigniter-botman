<?php

// Required PHP >= 7.1

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\Cache\CodeIgniterCache;
use BotMan\Drivers\Telegram\TelegramDriver;
use BotMan\Drivers\Telegram\Extensions\Keyboard;
use BotMan\Drivers\Telegram\Extensions\KeyboardButton;

class BotmanCore
{
    public function __construct()
    {
        DriverManager::loadDriver(TelegramDriver::class);

        $this->config = [
            //Your driver-specific configuration
            "telegram" => [
                "token" => "INSERT_YOUR_TELEGRAM_TOKEN_HERE",
            ]
        ];

        // Create an instance
        $CI = &get_instance();
        $CI->load->driver('cache');
        $this->botman = BotManFactory::create($this->config, new CodeIgniterCache($CI->cache->file));
    }

    public function run()
    {
        $this->commands();
    }

    /* -------------------------------------------------------------------------- */
    /*                              List of commands                              */
    /* -------------------------------------------------------------------------- */
    public function commands()
    {

        /* -------------------------------- fallback -------------------------------- */
        $this->botman->fallback(function (BotMan $bot) {
            $this->chatType('private');
            if ($this->myLanguage() == 'id') {
                $bot->reply('Maaf, perintah yang anda masukkan tidak diketahui.');
                $this->sayStart();
            } else if ($this->myLanguage() == 'en') {
                $bot->reply('Sorry, I did not understand these commands.');
                $this->sayStart();
            }
        });

        /* --------------------------------- /stop ----------------------------------- */
        $this->botman->hears('/stop', function (BotMan $bot) {
            $this->chatType('private');
            if ($this->myLanguage() == 'id') {
                $this->botman->reply('Proses berhasil di hentikan!');
            } else if ($this->myLanguage() == 'en') {
                $this->botman->reply('Proccess is stopped!');
            }
            $this->sayStart();
        })->stopsConversation();

        /* --------------------------------- /stopregister -------------------------------- */
        $this->botman->hears('/stopregister', function (BotMan $bot) {
            $this->chatType('private');
            if ($this->myLanguage() == 'id') {
                $this->botman->reply('Proses Pendaftaran dibatalkan.' . PHP_EOL . 'Anda bisa mendaftar kembali dengan :' . PHP_EOL . 'Ketik atau klik => /register');
            } else if ($this->myLanguage() == 'en') {
                $this->botman->reply('Registration proccess is cancelled.' . PHP_EOL . 'You can re-register by typing or click => /register');
            }
            $this->sayStart();
        })->stopsConversation();

        /* ------------------------------- /stoplogin ---------------------------- */
        $this->botman->hears('/stoplogin', function (BotMan $bot) {
            $this->chatType('private');
            if ($this->myLanguage() == 'id') {
                $this->botman->reply('Proses Login dibatalkan.' . PHP_EOL . 'Anda bisa login kembali ke akun anda dengan' . PHP_EOL . 'Ketik atau klik => /login');
            } else if ($this->myLanguage() == 'en') {
                $this->botman->reply('Login proccess is cancelled.' . PHP_EOL . 'You can login again to your account ' . PHP_EOL . ' Typing or click => /login');
            }
            $this->sayStart();
        })->stopsConversation();

        /* ------------------------------- /stopmenu ------------------------------ */
        $this->botman->hears('/stopmenu', function (BotMan $bot) {
            $this->chatType('private');
            if ($this->myLanguage() == 'id') {
                $this->botman->reply('Anda berhasil keluar dari pilihan menu.' . PHP_EOL . 'Untuk kembali menampilkan menu :' . PHP_EOL . 'Ketik atau klik => /menu');
            } else if ($this->myLanguage() == 'en') {
                $this->botman->reply('You have successfully exited the menu selection.' . PHP_EOL . 'To return to display the menu:' . PHP_EOL . 'Type or click => /menu');
            }
            $this->sayStart();
        })->stopsConversation();


        /* --------------------------------- /reset -------------------------------- */
        $this->botman->hears('/reset|/clear', function (BotMan $bot) {
            $this->chatType('private');
            $this->deleteStorage();
        })->stopsConversation();

        /* -------------------------------- /indonesia ------------------------------- */
        $this->botman->hears('/indonesia', function (BotMan $bot) {
            $this->chatType('private');
            $bot->userStorage()->save(['language' => 'id']);

            $language = $bot->userStorage()->find()->get('language');
            $bot->reply('Bahasa berhasil di set ke : ' . $language . ' 🇮🇩');
            $bot->reply('Untuk memulai Telegram Bot' . PHP_EOL . 'Anda bisa ketik atau klik => /start');
        });

        /* -------------------------------- /english -------------------------------- */
        $this->botman->hears('/english', function (BotMan $bot) {
            $this->chatType('private');
            $bot->userStorage()->save(['language' => 'en']);

            $language = $bot->userStorage()->find()->get('language');
            $bot->reply('Language successfully set to : ' . $language . ' 🇺🇸');
            $bot->reply('To get started type or click => /start');
        });

        /* --------------------------------- /mylang -------------------------------- */
        $this->botman->hears('/mylang', function (BotMan $bot) {
            $this->chatType('private');
            $bot->reply('Your language : ' . $this->myLanguage());
        });

        /* -------------------------------- /language ------------------------------- */
        $this->botman->hears('/language|/bahasa', function (BotMan $bot) {
            $this->chatType('private');
            $this->setLanguage();
        });

        $this->botman->hears('/showgif', function (BotMan $bot) {
            $this->chatType('private');
            $bot->sendRequest('sendAnimation', [
                'animation' => 'https://c.tenor.com/JrY5vHW30h4AAAAC/congrats-congratulations.gif'
            ]);
        });

        $this->botman->hears('/sendsticker', function (BotMan $bot) {
            $this->chatType('private');
            $bot->sendRequest('sendSticker', [
                'sticker' => 'http://localhost/images/clippy_congrat.tgs'
            ]);
        });

        /* --------------------------------- /start --------------------------------- */
        $this->botman->hears('/start|/run|/mulai', function (BotMan $bot) {
            $this->chatType('private');
            $this->start();
        });

        /* -------------------------------- /register ------------------------------- */
        $this->botman->hears('/register', function (BotMan $bot) {
            $this->chatType('private');
            $token = $this->getUserData('token');
            $name = $this->getUserData('name');
            $username = $this->getUserData('username');
            $id = $this->getUserData('id');
            $bot->startConversation(new ConversationRegister($this->myLanguage(), $token, $name, $username, $id));
        });

        /* --------------------------------- /saytogroup ------------------------- */
        $this->botman->hears('/saytogroup', function (BotMan $bot) {

            $ids = array(
                '-62123456', // group 1
                //'-629876543', // group 2
            );
            //$message = 'halo group ' . $this->getMessage('chat', 'title');
            $message = 'Halo.. from ' . $this->getMessage('from', 'first_name') . $this->getMessage('from', 'last_name');
            $this->sayToRecipient('chat_id', $ids, $message);
        });



        /* -------------------------------------------------------------------------- */
        /*                   Member / Authentication Commands                         */
        /* -------------------------------------------------------------------------- */

        /* -------------------------------- /userdata ------------------------------- */
        $this->botman->hears('/userdata', function (BotMan $bot) {
            $this->chatType('private');
            $user = $bot->getUser();
            $bot->reply('Hello ' . $user->getFirstName() . ' ' . $user->getLastName());
            $bot->reply('Your username is: ' . $user->getUsername());
            $bot->reply('Your ID is: ' . $user->getId());
            // var_dump($this->getMessage());
        });

        /* --------------------------------- /stopregister -------------------------------- */
        $this->botman->hears('/stopactivation', function (BotMan $bot) {
            $this->chatType('private');
            if ($this->myLanguage() == 'id') {
                $this->botman->reply('Proses aktivasi dibatalkan.' . PHP_EOL . 'Anda bisa aktivasi kembali dengan :' . PHP_EOL . 'Ketik atau klik => /activation');
            } else if ($this->myLanguage() == 'en') {
                $this->botman->reply('Activation proccess is cancelled.' . PHP_EOL . 'You can re-activation by typing or click => /activation');
            }
            $this->sayStart();
        })->stopsConversation();

        /* -------------------------------- /login ------------------------------- */
        $this->botman->hears('/login', function (BotMan $bot) {
            $this->chatType('private');
            $this->checkLoggedIn('say');
        });

        /* --------------------------------- /logout ----------------------------- */
        $this->botman->hears('/logout', function (BotMan $bot) {
            $this->chatType('private');
            if ($this->isLoggedIn()) {
                $this->logout('say');
            }
        });

        /* ---------------------------------- /menu ------------------------------- */
        $this->botman->hears('/menu', function (BotMan $bot) {
            $this->chatType('private');
            $bot->startConversation(new ConversationMenu($this->myLanguage()));
        });

        /* ------------------------------- /menumember ---------------------------- */
        $this->botman->hears('/menumember', function (BotMan $bot) {
            $this->chatType('private');
            if (!$this->isLoggedIn()) {
                $this->checkLoggedIn('say');
            }

            if ($this->myLanguage() == 'id') {
                $bot->reply('Untuk registrasi member lain menggunakan sponsor anda sendiri' . PHP_EOL . 'Ketik atau klik => /register');
            } else if ($this->myLanguage() == 'en') {
                $bot->reply('To register another member using your own sponsor' . PHP_EOL . 'Type or click => /register');
            }
        });

        /* --------------------------------- /mytoken ---------------------------- */
        $this->botman->hears('/mydatalogin', function (BotMan $bot) {
            $this->chatType('private');
            $bot->reply('Token : ' . $this->getUserData('token'));
            $bot->reply('Name : ' . $this->getUserData('name'));
            $bot->reply('Username : ' . $this->getUserData('username'));
            $bot->reply('Id : ' . $this->getUserData('id'));
        });

        /* listen  */
        $this->botman->listen();
    }


    /* -------------------------------------------------------------------------- */
    /*                                 getMessage                                 */
    /* -------------------------------------------------------------------------- */
    public function getMessage($key = '', $value = '')
    {
        $rawMessage = json_encode($this->botman->getMessage()->getPayload());
        $decodeMessage = json_decode($rawMessage);
        if ($key && $value) {
            return ($decodeMessage->$key->$value);
        } else {
            return ($decodeMessage);
        }
    }

    /**
     * chat type : private/group
     */
    public function chatType($param)
    {
        if ($this->getMessage('chat', 'type') == $param) {
            return TRUE;
        } else {
            $this->botman->reply('Command available only on private message :' . PHP_EOL . 'https://t.me/{username}_bot');
            exit;
        }
    }

    /* -------------------------------------------------------------------------- */
    /*                                  start                                     */
    /* -------------------------------------------------------------------------- */
    public function start()
    {
        if (!$this->checkLanguage()) {
            $this->botman->sendRequest('sendSticker', [
                'sticker' => 'CAACAgIAAxkBAAIMhGIGq-zLUB1QVN1MaJ_CZnLsh6iSAALGAQACFkJrCkoj1PTJ23lHIwQ'
            ]);
            $this->botman->reply('Welcome to Telegram 😊');
            $this->setLanguage();
        } else {
            $this->sayStartList();
        }
    }

    /* -------------------------------------------------------------------------- */
    /*                               sayToRecipient                               */
    /* -------------------------------------------------------------------------- */
    /**
     * Recipient type : username / chat_id
     * @param array $user
     */
    public function sayToRecipient($type, $user, $message)
    {
        $CI = &get_instance();

        // $message = 'haii';
        // $telegramId = 123456789;
        $telegramId = '';
        if ($type == 'username') {
            $member = $CI->db->get_where('tbl_user', array('username' => $user))->row();
            if ($member->telegram_id) {
                $telegramId = $member->telegram_id;
            }
        } else if ($type == 'chat_id') {
            $telegramId = $user;
        }
        if ($telegramId && $message) {
            $this->botman->say($message, $telegramId, TelegramDriver::class);
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /* -------------------------------------------------------------------------- */
    /*                                  myLanguage                                */
    /* -------------------------------------------------------------------------- */
    public function myLanguage()
    {
        $lang = $this->botman->userStorage()->find()->get('language');
        if ($lang) {
            return $lang;
        } else {
            $this->setLanguage();
        }
    }


    /* -------------------------------------------------------------------------- */
    /*                                  checkLanguage                             */
    /* -------------------------------------------------------------------------- */
    /**
     *
     * @return bool
     */
    public function checkLanguage(): bool
    {
        $lang = $this->botman->userStorage()->find()->get('language');
        if ($lang) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /* -------------------------------------------------------------------------- */
    /*                                  noLanguage                                */
    /* -------------------------------------------------------------------------- */
    public function noLanguage()
    {
        if (!$this->checkLanguage()) {
            $this->botman->reply('No default language set!');
        }
    }

    /* -------------------------------------------------------------------------- */
    /*                                  setLanguage                               */
    /* -------------------------------------------------------------------------- */
    public function setLanguage()
    {
        $this->noLanguage();
        $this->botman->reply('Untuk set ke bahasa Indonesia.' . PHP_EOL . 'Ketik atau klik => /indonesia');
        $this->botman->reply('To set language to English.' . PHP_EOL . 'Type or click => /english');
        exit;
    }

    /* ------------------------------- getUserData ------------------------------ */
    public function getUserData($param = '')
    {
        $data = array(
            'token' => $this->botman->userStorage()->find()->get('token'),
            'name' => $this->botman->userStorage()->find()->get('name'),
            'username' => $this->botman->userStorage()->find()->get('username'),
            'id' => $this->botman->userStorage()->find()->get('id'),
        );
        if ($data['token']) {
            if ($param) {
                return $data[$param];
            } else {
                return $data;
            }
        } else {
            return FALSE;
        }
    }

    /**
     * isLoggedIn
     *
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        if ($this->getUserData()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }


    /* ------------------------------- checkLoggedIn ------------------------------- */
    public function checkLoggedIn($say = '')
    {
        $this->apiRequest('POST', 'api/checklogin', ['null' => 'null']);

        if ($this->isLoggedIn()) {
            if ($say) {
                if ($this->myLanguage() == 'id') {
                    $this->botman->reply('Anda sudah login sebagai : <b>' . $this->getUserData('username') . '</b>', ['parse_mode' => 'HTML']);
                    $this->botman->reply('Untuk menampilkan menu-menu member' . PHP_EOL . 'Ketik atau klik => /menumember');
                    $this->botman->reply('Jika ingin logout dari akun anda.' . PHP_EOL . 'Ketik atau klik => /logout');
                } else if ($this->myLanguage() == 'en') {
                    $this->botman->reply('You already logged in as : <b>' . $this->getUserData('username') . '</b>', ['parse_mode' => 'HTML']);
                    $this->botman->reply('To show member menu' . PHP_EOL . 'Type or click => /menumember');
                    $this->botman->reply('If you want to log out of your account.' . PHP_EOL . 'Type or click => /logout');
                }
            } else {
                return TRUE;
            }
        } else {
            if ($say) {
                if ($this->myLanguage() == 'id') {
                    $this->botman->reply('Anda belum login!');
                } else if ($this->myLanguage() == 'en') {
                    $this->botman->reply('You not logged in!');
                }
                $this->conversationAuth();
                exit;
            } else {
                return FALSE;
            }
        }
    }

    /* -------------------------------------------------------------------------- */
    /*                                  logout                                    */
    /* -------------------------------------------------------------------------- */
    public function logout($say = '')
    {
        $this->botman->userStorage()->save([
            'token' => '',
            'name' => '',
            'username' => '',
            'id' => '',
        ]);
        if ($say) {
            if ($this->myLanguage() == 'id') {
                $this->botman->reply('Logout berhasil.. Anda bisa login kembali dengan ' . PHP_EOL . 'ketik atau klik => /login');
            } else if ($this->myLanguage() == 'en') {
                $this->botman->reply('Logout success.. You can login again by ' . PHP_EOL . 'typing or click => /login');
            }
        }
    }


    /* -------------------------------------------------------------------------- */
    /*                                  conversationAuth                          */
    /* -------------------------------------------------------------------------- */
    public function conversationAuth()
    {
        $this->botman->startConversation(new ConversationAuth($this->myLanguage()));
    }

    /* -------------------------------------------------------------------------- */
    /*                                  sayStart                                  */
    /* -------------------------------------------------------------------------- */
    public function sayStart()
    {
        if ($this->myLanguage() == 'id') {
            $this->botman->reply('Untuk memulai bisa ketik atau klik => /start');
        } else if ($this->myLanguage() == 'en') {
            $this->botman->reply('To get started type or click => /start');
        }
    }

    /* -------------------------------------------------------------------------- */
    /*                                sayStartList                                */
    /* -------------------------------------------------------------------------- */
    public function sayStartList()
    {
        $this->botman->sendRequest('sendSticker', [
            'sticker' => 'CAACAgIAAxkBAAIMb2IGqvouJ1NB-FNzaG1R7zd9Ac0SAAJuAANBtVYM0KWEcubcWogjBA'
        ]);

        if ($this->myLanguage() == 'id') {
            $this->botman->reply('Hello ' . $this->botman->getUser()->getFirstName() . ' ' . $this->botman->getUser()->getLastName() . ' 😊');
            $this->botman->reply('Untuk mengubah bahasa.' . PHP_EOL . 'Ketik atau klik => /language');
            $this->botman->reply('Untuk menampilkan menu.' . PHP_EOL . 'Ketik atau klik => /menu');
            $this->botman->reply('Untuk memulai pendaftaran ' . PHP_EOL . 'Ketik atau klik => /register');

            if ($this->isLoggedIn()) {
                $this->botman->reply('Untuk logout dari akun anda.' . PHP_EOL . 'Ketik atau klik => /logout ');
            } else {
                $this->botman->reply('Untuk login menggunakan akun anda.' . PHP_EOL . 'Ketik atau klik => /login ');
            }
        } else if ($this->myLanguage() == 'en') {
            $this->botman->reply('Hello ' . $this->botman->getUser()->getFirstName() . ' ' . $this->botman->getUser()->getLastName() . ' 😊');
            $this->botman->reply('To change the language.' . PHP_EOL . 'Type or click => /language');
            $this->botman->reply('To show menus.' . PHP_EOL . 'Type or click => /menu');
            $this->botman->reply('To start registration ' . PHP_EOL . 'Type or click => /register');

            if ($this->isLoggedIn()) {
                $this->botman->reply('To logout your account.' . PHP_EOL . 'Type or click => /logout ');
            } else {
                $this->botman->reply('To login using your account.' . PHP_EOL . 'Type or click => /login ');
            }
        }
    }

    /* -------------------------------------------------------------------------- */
    /*                                  deleteStorage                             */
    /* -------------------------------------------------------------------------- */
    public function deleteStorage()
    {
        $this->botman->userStorage()->delete();
        $this->botman->reply('Deleted all session storage! ✅');
        $this->botman->reply('Removed all session conversation! ✅');
        $this->botman->reply('To get started type or click => /start');
    }


    /* -------------------------------------------------------------------------- */
    /*                             apiRequest                                     */
    /* -------------------------------------------------------------------------- */
    public function apiRequest($method, $endpoint, $data = '')
    {
        // 'username' => 'testapi',
        // 'password' => '',

        $credential = array(
            'token' => $this->getUserData('token'),
        );

        $apiRequest = api_request($method, $credential, $endpoint, $data);

        if ($apiRequest['message'] == 'Token Time Expire.') {
            $this->botman->reply($apiRequest['message']);
            $this->logout();
            $this->conversationAuth();
            exit;
        } else {
            return $apiRequest;
        }
    }
}
