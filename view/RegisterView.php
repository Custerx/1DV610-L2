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

	/**
	 * Create HTTP response
	 *
	 * Should be called after a register attempt has been determined
	 *
	 * @return  void BUT writes to standard output and cookies!
	 */
	public function response() {
		$message = $this->messageFromException; // TODO : Add solution to handle messages.

		$response = $this->generateRegistrationFormHTML($message);

		return $response;
	}

	/**
	* Generate HTML code on the output buffer for the logout button
	* @param $message, String output message
	* @return  void, BUT writes to standard output!
	*/
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

	private function postedEmptyUsername() : bool {
		return (isset($_POST[self::$name]) && empty($_POST[self::$name]));
	}

	private function postedEmptyPassword() : bool {
		return (isset($_POST[self::$password]) && empty($_POST[self::$password]));
    }
    
    private function postedEmptyPasswordRepeat() : bool {
		return (isset($_POST[self::$passwordRepeat]) && empty($_POST[self::$passwordRepeat]));
	}

	private function get_PHPSESSID_Cookie_Value() {
		if(isset($_COOKIE["PHPSESSID"])){
			return $_COOKIE["PHPSESSID"];
		}
	}
}