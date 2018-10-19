<?php

namespace View;

class RegisterView {
	private static $register = 'RegisterView::Register';
	private static $passwordRepeat = 'RegisterView::PasswordRepeat';
	private static $name = 'RegisterView::UserName';
	private static $password = 'RegisterView::Password';
	private static $messageId = 'RegisterView::Message';

	private $session;
	private $database;
	private $messageFromException = '';

	public function __construct(\Model\Session $startSession, \Model\DatabaseModel $a_database) {
		$this->session = $startSession;
		$this->database = $a_database;
	}

	public function response() {
		$message = $this->messageFromException;

		$response = $this->generateRegistrationFormHTML($message);

		return $response;
	}

	private function generateRegistrationFormHTML($message) {
		return '
			<form method="post" action="?register"> 
				<fieldset>
					<legend>Register a new user - enter Username and password</legend>
					<p id="' . self::$messageId . '">' . $message . '</p>
					
					<label for="' . self::$name . '">Username :</label>
					<input type="text" id="' . self::$name . '" name="' . self::$name . '" value="' . $this->getUserName() . '" />

					<label for="' . self::$password . '">Password :</label>
					<input type="password" id="' . self::$password . '" name="' . self::$password . '"  />

					<label for="' . self::$passwordRepeat . '">Repeat password  :</label>
					<input type="password" id="' . self::$passwordRepeat . '" name="' . self::$passwordRepeat . '" />

					<input type="submit" name="' . self::$register . '" value="register" />
				</fieldset>
			</form>
		';
	}

	public function userSuccessfullyRegistered() {
		try {
			if($this->userWantsToRegister()) {
				$this->tryToregisterUser();
				return true;
			}
		} catch (\Exception $e) {
			$this->messageFromException = $e->getMessage();
		}
	}
	// Placed here due to high abstraction level and strong connection to userSuccessfullyRegistered.
	private function tryToregisterUser() {				
		$inputUserName = $_POST[self::$name];
		$inputPassword = $_POST[self::$password];
		$inputPasswordRepeat = $_POST[self::$passwordRepeat];
		$users_HTTP_USER_AGENT = $this->noSpacedHTTP_USER_AGENT();
		$cookiePassword = $this->database->generateRandomPassword();

		if ($this->database->isUniqueUsername($inputUserName)) {
			$this->database->saveMemberToJSONFile(new \Model\Member($inputUserName, $inputPassword, $inputPasswordRepeat, $users_HTTP_USER_AGENT, $cookiePassword));
		} else {
			throw new \Exception("User exists, pick another username.");
		}
	}

	public function userWantsToViewRegisterPage() : bool {
        return isset($_GET["register"]);
	}

	public function makeUsernameAvailableForLoginPage() {
		$this->session->setRegisteredUsername($_POST[self::$name]);
	}

	public function redirectToLoginPage() {
		header("location:?login");
	}

	private function noSpacedHTTP_USER_AGENT() {
		$HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
		$HTTP_USER_AGENT = str_replace(' ', '', $HTTP_USER_AGENT);
		$HTTP_USER_AGENT = substr($HTTP_USER_AGENT, 21, 33);
		return $HTTP_USER_AGENT;
	}

	private function userWantsToRegister() : bool {
		return (isset($_POST[self::$register]));
    }

	private function getUserName() {
		if ($this->hasUserName()) {
			return strip_tags($_POST[self::$name]);
		} else {
			return '';
		}
	}

	private function hasUserName() : bool {
		return (isset($_POST[self::$name]) && !empty($_POST[self::$name]));
	}
}