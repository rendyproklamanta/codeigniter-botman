<?php

use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Conversations\Conversation;

class ConversationMenu extends Conversation
{

   public function __construct($lang)
   {
      $this->language = $lang;
   }

   /**
    * @return mixed
    */
   public function run()
   {
      $this->listMenu();
   }

   /* -------------------------------------------------------------------------- */
   /*                               noMenu                                       */
   /* -------------------------------------------------------------------------- */
   public function noMenu()
   {
      if ($this->language == 'id') {
         $this->say('Pilihan menu tidak ditemukan!');
      } else if ($this->language == 'en') {
         $this->say('Menu options not found!');
      }
   }

   /* -------------------------------------------------------------------------- */
   /*                            sayBackToMenu                                   */
   /* -------------------------------------------------------------------------- */
   public function sayBackToMenu()
   {
      if ($this->language == 'id') {
         $this->say('Untuk menampilkan kembali menu' . PHP_EOL . 'Ketik atau klik => /menu');
      } else if ($this->language == 'en') {
         $this->say('To show menu again' . PHP_EOL . 'Type or click => /menu');
      }
   }

   /* -------------------------------------------------------------------------- */
   /*                                 run                                */
   /* -------------------------------------------------------------------------- */
   public function listMenu()
   {
      $this->menus = array(
         'Menu One', // menu 0
         'Menu Two', // menu 1
         'Menu Three', // menu 2
         'Menu Four', // menu 3
         'Menu Five', // menu 4
      );

      if ($this->language == 'id') {
         $questionText = 'Klik pilihan menu dibawah ini :';
      } else if ($this->language == 'en') {
         $questionText = 'Please click menus below :';
      }

      $question = Question::create($questionText);
      foreach ($this->menus as $menu) {
         $question->addButtons([
            Button::create($menu)->value($menu),
         ]);
      }

      $this->ask($question, function (Answer $answer) {
         // Detect if button was clicked:
         switch ($answer->getValue()) {
            case $this->menus[0]:
               $this->menuOne();
               break;
            case $this->menus[1]:
               $this->menuTwo();
               break;
            case $this->menus[2]:
               $menus = array(
                  'English Version' => 'https://youtu.be/',
                  'Chinese Version' => 'https://youtu.be',
                  'Indonesian Version' => 'https://youtu.be/',
               );
               $this->openLink($menus, $this->menus[2]);
               break;
            case $this->menus[3]:
               $this->news();
               break;
            case $this->menus[4]:
               $menus = array(
                  'English Version' => 'http://',
                  'Chinese Version' => 'http://',
                  'Indonesian Version' => 'http://',
               );
               $this->openLink($menus, 'File ' . $this->menus[4]);
               break;
            case $this->menus[5]:
               if ($this->language == 'id') {
                  $this->say('Klik untuk menuju web => https://');
               } else if ($this->language == 'en') {
                  $this->say('Click to open web => https://');
               }
               $this->sayBackToMenu();
               break;
            case $this->menus[6]:
               if ($this->language == 'id') {
                  $this->say("Jika Anda memiliki pertanyaan lebih lanjut tentang sistem dan produk kami, jangan ragu untuk menghubungi email kami : " . PHP_EOL . "my@email.com");
               } else if ($this->language == 'en') {
                  $this->say("If you have further question about our system and products, don't hesitate to contact our email : " . PHP_EOL . "my@email.com");
               }
               $this->sayBackToMenu();
               break;
            default:
               $this->noMenu();
               $this->listMenu();
               break;
         }
      });

      if ($this->language == 'id') {
         $this->say("Jika Anda ingin keluar dari pilihan menu." . PHP_EOL . "Ketik atau klik => /stopmenu");
      } else if ($this->language == 'en') {
         $this->say("If you want to exit the menu selection." . PHP_EOL . "Type or click => /stopmenu");
      }
   }


   /* -------------------------------------------------------------------------- */
   /*                               menuOne                                      */
   /* -------------------------------------------------------------------------- */
   public function menuOne()
   {
      $this->menus = array(
         'Menu One', // menu 0
         'Menu Two', // menu 1
         'Menu Three', // menu 2
         'Menu Four', // menu 3
         'Menu Five', // menu 4
      );

      $question = Question::create('Menu One List :');
      foreach ($this->menus as $menu) {
         $question->addButtons([
            Button::create($menu)->value($menu),
         ]);
      }

      $this->ask($question, function (Answer $answer) {
         // print_r($this->menus[0]);die; // check menus

         // Detect if button was clicked:
         switch ($answer->getValue()) {
            case $this->menus[0]:
               $links = array(
                  'English Version' => 'https://youtu.be/DG1fFK3KpFE',
                  'Chinese Version' => 'https://youtube.com/watch?v=zVJ4P8v5PqA',
                  'Indonesian Version' => 'https://youtube.com/watch?v=SLgSEtZi8DY',
               );
               $this->openLink($links, $this->menus[0]);
               break;
            case $this->menus[1]:
               $links = array(
                  'English Version' => 'https://youtube.com/',
                  'Chinese Version' => 'https://youtube.com/',
                  'Indonesian Version' => 'https://youtube.com/',
               );
               $this->openLink($links, $this->menus[1]);
               break;
            case $this->menus[2]:
               $links = array(
                  'English Version' => 'https://youtu.be/ByesKt1EFbk',
                  'Chinese Version' => 'https://youtu.be/p_AqZtItzYM',
                  'Indonesian Version' => 'https://youtu.be/nW4iOsiIpQw',
               );
               $this->openLink($links, $this->menus[2]);
               break;
            case $this->menus[3]:
               $links = array(
                  'English Version' => 'https://youtube.com/',
                  'Chinese Version' => 'https://youtube.com/',
                  'Indonesian Version' => 'https://youtube.com/',
               );
               $this->openLink($links, $this->menus[3]);
               break;
            case $this->menus[4]:
               $links = array(
                  'English Version' => 'https://youtube.com/',
                  'Chinese Version' => 'https://youtube.com/',
                  'Indonesian Version' => 'https://youtube.com/',
               );
               $this->openLink($links, $this->menus[4]);
               break;
            case $this->menus[5]:
               $links = array(
                  'English Version' => 'https://youtube.com/',
                  'Chinese Version' => 'https://youtube.com/',
                  'Indonesian Version' => 'https://youtube.com/',
               );
               $this->openLink($links, $this->menus[5]);
               break;
            case $this->menus[6]:
               $links = array(
                  'English Version' => 'https://youtube.com/',
                  'Chinese Version' => 'https://youtube.com/',
                  'Indonesian Version' => 'https://youtube.com/',
               );
               $this->openLink($links, $this->menus[6]);
               break;
            default:
               $this->noMenu();
               $this->menuOne();
               break;
         }
      });
   }

   /* -------------------------------------------------------------------------- */
   /*                                  menuTwo                                    */
   /* -------------------------------------------------------------------------- */
   public function menuTwo()
   {
      $this->menus = array(
         'Menu One', // menu 0
         'Menu Two', // menu 1
         'Menu Three', // menu 2
         'Menu Four', // menu 3
         'Menu Five', // menu 4
      );

      $question = Question::create('Menu Two List :');
      foreach ($this->menus as $menu) {
         $question->addButtons([
            Button::create($menu)->value($menu),
         ]);
      }

      $this->ask($question, function (Answer $answer) {
         // Detect if button was clicked:
         switch ($answer->getValue()) {
            case $this->menus[0]:
               $links = array(
                  'English Version' => 'https://youtu.be/',
                  'Chinese Version' => 'https://youtu.be/',
                  'Indonesian Version' => 'https://youtu.be/',
               );
               $this->openLink($links, $this->menus[0]);
               break;
            case $this->menus[1]:
               $links = array(
                  'English Version' => 'https://youtu.be/',
                  'Chinese Version' => 'https://youtu.be/',
                  'Indonesian Version' => 'https://youtu.be/',
               );
               $this->openLink($links, $this->menus[1]);
               break;
            case $this->menus[2]:
               $links = array(
                  'English Version' => 'https://youtu.be/',
                  'Chinese Version' => 'https://youtu.be/',
                  'Indonesian Version' => 'https://youtu.be/',
               );
               $this->openLink($links, $this->menus[2]);
               break;
            case $this->menus[3]:
               $links = array(
                  'English Version' => 'https://youtu.be/',
                  'Chinese Version' => 'https://youtu.be/',
                  'Indonesian Version' => 'https://youtu.be/',
               );
               $this->openLink($links, $this->menus[3]);
               break;
            case $this->menus[4]:
               $links = array(
                  'English Version' => 'https://youtu.be/',
                  'Chinese Version' => 'https://youtu.be/',
                  'Indonesian Version' => 'https://youtu.be/',
               );
               $this->openLink($links, $this->menus[4]);
               break;
            default:
               $this->noMenu();
               $this->menuTwo();
               break;
         }
      });
   }

   /* -------------------------------------------------------------------------- */
   /*                                news                                       */
   /* -------------------------------------------------------------------------- */
   public function news()
   {
      $attachment = new Image('http://domain.com/news/one.jpg', [
         'custom_payload' => true,
      ]);
      $message = OutgoingMessage::create('Displaying Image')->withAttachment($attachment);
      $this->say($message);

      $attachment = new Image('http://domain.com/news/two.jpg', [
         'custom_payload' => true,
      ]);
      $message = OutgoingMessage::create('Displaying Image')
         ->withAttachment($attachment);
      $this->say($message);

      $this->sayBackToMenu();
   }

   /* -------------------------------------------------------------------------- */
   /*                             openLink                                */
   /* -------------------------------------------------------------------------- */
   public function openLink($links, $title)
   {
      if ($this->language == 'id') {
         $choose = 'Klik untuk membuka';
      } else if ($this->language == 'en') {
         $choose = 'Click to open';
      }

      $question = Question::create($choose . ' ' . $title . ' :');
      foreach ($links as $menu => $value) {
         $question->addButtons([
            Button::create($menu)->additionalParameters(['url' => $value]),
         ]);
      }

      $this->ask($question, function (Answer $answer) {
         switch ($answer->getText()) {
            case '/menu':
               $this->listMenu();
               break;
            default:
               $this->noMenu();
               $this->sayBackToMenu();
               break;
         }
      });
      $this->sayBackToMenu();
   }


   // END OF FILE ------------------------------------------------
}
