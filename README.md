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

3. Install ngrok
4. running with vhost

```bash
ngrok http -host-header=rewrite sites.dev:80
```

5. Connect your ngrok https url to telegram webhook

```bash
https://api.telegram.org/bot<TOKEN>/setWebhook?url=https://123.asd.ngrok.io
https://api.telegram.org/bot<TOKEN>/getWebhookInfo
```

6. Install telegram web
7. Go to url : https://t.me/{name}_bot
8. Type : /start

> Feature:
1. Conversation Message using CodeigniterCache

## License

BotMan and Codeigniter is free software distributed under the terms of the MIT license.
