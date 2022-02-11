# Codeigniter v3.2 + BotMan v2.6 + Telegram Bot

This repository is an example Codeigniter application to instantly start developing cross-platform messaging bots using [BotMan](https://github.com/mpociot/botman).

> Make sure you have created telegram bot in botfather and get the token


> https://sendpulse.com/knowledge-base/chatbot/create-telegram-chatbot


## Installation

1. Go to cloned folder

```bash
composer install
```

2. Insert token from telegram bot

```bash
/application/controllers/Telebot.php
```

3. Download and Install ngrok : https://ngrok.com/download <br/>
- Make sure you have download and set cacert.pem in your php.ini !! <br/>
- Download cacert : https://curl.haxx.se/ca/cacert.pem

Locate php.ini in command line

```bash
php --ini
```

Edit php.ini and insert below (if you use MAMP. If not edit the path of location cacert.pem)

```bash
[curl]
curl.cainfo="C:\MAMP\bin\apache\bin\cacert.pem"

[openssl]
openssl.cafile="C:\MAMP\bin\apache\bin\cacert.pem"
openssl.capath="C:\MAMP\bin\apache\bin\"
```

5. running with vhost

```bash
ngrok http -host-header=rewrite sites.dev:80
```

5. Connect your ngrok https url to telegram webhook

```bash
https://api.telegram.org/bot<TOKEN>/setWebhook?drop_pending_updates=1&url=https://123.asd.ngrok.io/telebot
https://api.telegram.org/bot<TOKEN>/getWebhookInfo
```

6. Install telegram desktop for test runinng bot: https://desktop.telegram.org/
7. Go to your bot url : https://t.me/{name}_bot
8. Type : /start

> Feature:
1. Conversation Message using CodeigniterCache

## License

BotMan and Codeigniter is free software distributed under the terms of the MIT license.
