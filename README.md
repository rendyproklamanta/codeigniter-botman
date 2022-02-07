# Codeigniter v3.2 + BotMan v2.6

This repository is an example Codeigniter application to instantly start developing cross-platform messaging bots using [BotMan](https://github.com/mpociot/botman).

## Installation

1. Go to cloned folder

```bash
composer install
```

2. Install ngrok
3. running with vhost

```bash
ngrok http -host-header=rewrite sites.dev:80
```

4. Connect your ngrok https url to telegram webhook

```bash
https://api.telegram.org/bot<TOKEN>/setWebhook?url=https://123.asd.ngrok.io
https://api.telegram.org/bot<TOKEN>/getWebhookInfo
```

5. Install telegram web
6. Go to url : https://t.me/{name}_bot
7. Type : /start

> Feature:
1. Conversation Message using CodeigniterCache

## License

BotMan and Codeigniter is free software distributed under the terms of the MIT license.
