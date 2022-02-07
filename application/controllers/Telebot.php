<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Required PHP >= 7.1

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Cache\CodeIgniterCache;
use BotMan\BotMan\Drivers\DriverManager;

class Telebot extends CI_Controller
{

    public function index()
    {
        DriverManager::loadDriver(\BotMan\Drivers\Telegram\TelegramDriver::class);

        $config = [
            //Your driver-specific configuration
            "telegram" => [
                "token" => "<TOKEN_FROM_TELEGRAM>"
            ]
        ];

        // Create an instance
        $this->load->driver('cache');
        $botman = BotManFactory::create($config, new CodeIgniterCache($this->cache->file));

        // commands
        $botman->hears('/start|/run', function (BotMan $bot) {
            $bot->reply('Halo ' . $bot->getUser()->getFirstName() . ' ' . $bot->getUser()->getLastName());
            $bot->reply('Silahkan ketik /register untuk memulai pendaftaran anda');
        });

        // commands
        $botman->hears('/userdata', function (BotMan $bot) {
            $bot->reply($bot->getUser()->getInfo());
        });

        $botman->hears('/register', function ($bot) {
            $bot->startConversation(new RegisterConversation);
        });

        $botman->hears('/stop', function (BotMan $bot) {
            $bot->reply('Pendaftaran dibatalkan. Anda bisa mendaftar kembali dengan mengetik keyword /register');
        })->stopsConversation();

        $botman->listen();
    }
}
