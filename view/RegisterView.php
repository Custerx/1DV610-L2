<?php

namespace View;

class RegisterView {
	private static $registerCSRF;
	private static $register = 'RegisterView::Register';
	private static $passwordRepeat = 'RegisterView::PasswordRepeat';
	private static $name = 'RegisterView::UserName';
	private static $password = 'RegisterView::Password';
	private static $messageId = 'RegisterView::Message';

	private $registerModel;
	private $session;

	/**
	 * Construct function
	 *
	 * @param \Model\Session $startSession and \Model\Auth $auth
	 */
	public function __construct(\Model\RegisterModel $registerModel, \Model\Session $startSession) {
		$this->registerModel = $registerModel;
		$this->session = $startSession;
	}

	/**
	 * Create HTTP response
	 *
	 * Should be called after a register attempt has been determined
	 *
	 * @return  void BUT writes to standard output and cookies!
	 */
	public function response() {
		$message = $this->session->getSessionMessage();

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
					
					<input type="hidden" name="' . self::$registerCSRF .'" value="sdaldjvnoaida895723juigbbvfdasid7378892234jadbaKBD"/>

					<input type="submit" name="' . self::$register . '" value="register" />
				</fieldset>
			</form>
		';
	}

	public function getRegisterCSRF() {
		if (isset($_POST[self::$registerCSRF])) {
			return self::$registerCSRF;
		}
	}
	
	public function userWantsToRegister() : bool {
		return (isset($_POST[self::$register]));
    }
    
    public function wantsToRegisterV2() : bool {
        return isset($_GET["register"]);
    }
	
	private function getUserName() {
		if ($this->hasUserName()) {
			return strip_tags($_POST[self::$name]);
		} else {
			return '';
		}
	}

	public function getRegisterCredentials() : array {
		$inputUserName = '';
        $inputPassword = '';
        $inputPasswordRepeat = '';

		if ($this->hasUserName()) {
			$inputUserName = $_POST[self::$name];	
		}

		if ($this->hasPassword()) {
			$inputPassword = $_POST[self::$password];
        }
        
        if ($this->hasPasswordRepeat()) {
			$inputPasswordRepeat = $_POST[self::$passwordRepeat];
        }

		return array($inputUserName, $inputPassword, $inputPasswordRepeat);
	}

	private function hasUserName() : bool {
		return (isset($_POST[self::$name]) && !empty($_POST[self::$name]));
	}

	private function hasPassword() : bool {
		return (isset($_POST[self::$password]) && !empty($_POST[self::$password]));
    }
    
    private function hasPasswordRepeat() : bool {
		return (isset($_POST[self::$passwordRepeat]) && !empty($_POST[self::$passwordRepeat]));
	}
}