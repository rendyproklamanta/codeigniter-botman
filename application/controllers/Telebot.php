<?php

class Telebot extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->botman = new BotmanCore(); //init
	}

	/* -------------------------------------------------------------------------- */
	/*                                    index                                   */
	/* -------------------------------------------------------------------------- */
	public function index()
	{
		$this->botman->run();
	}

	/* -------------------------------------------------------------------------- */
	/*                             sayToRecipient Test                            */
	/* -------------------------------------------------------------------------- */
	public function sayToRecipient()
	{
		$botman = new BotmanCore();
		$username = 'testapi'; // username member
		$message = 'Hello...'; // message to send to user telegram
		if (!$botman->sayToRecipient('username', $username, $message)) {
			echo 'Username/message or Telegram ID Not Found!';
		}
	}
}
