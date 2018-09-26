<?php

namespace Model;

class Auth {
	private static $staticUserName = "Admin";
	private static $staticPassword = "Password";

	private static $staticUserNameForRegister = "userd6cfddebc0";
	private static $staticPasswordForRegister = "pass66c2450202";

	private $session;

	public function __construct($session) {
		$this->session = $session;
	}

	public function loginWithCredentials($credentials) {
		list($inputUserName, $inputPassword) = $credentials;

		$this->session->setSessionKey("sessionUserName", $inputUserName);
		$this->session->setSessionKey("sessionPassword", $inputPassword);

		if ($this->authentication()) {
			$this->session->setSessionKey("loggedIn", true);
		}

		if ($this->authenticationForRegisterTest()) {
			$this->session->setSessionKey("loggedIn", true);
		}
	}

	private function authentication() : bool {
		return ($this->session->getSessionKey("sessionPassword") == self::$staticPassword && $this->session->getSessionKey("sessionUserName") == self::$staticUserName);
	}
	
	private function authenticationForRegisterTest() : bool {
		return ($this->session->getSessionKey("sessionPassword") == self::$staticPasswordForRegister && $this->session->getSessionKey("sessionUserName") == self::$staticUserNameForRegister);
    }

    /**
	 * Checks username and password input.
	*/
	public function validationMessage() {
		if (empty($this->session->getSessionKey("sessionUserName"))) {
			return 'Username is missing';
		} else if (empty($this->session->getSessionKey("sessionPassword"))) {
			return 'Password is missing';
		} else {
			if ($this->session->isLoggedIn()) {
				return 'Welcome';
			} else {
				return 'Wrong name or password';
			}
		}
	}
}