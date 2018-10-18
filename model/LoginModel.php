<?php

namespace Model;

class LoginModel {
	private static $staticUserName = "Admin";
	private static $staticPassword = "Password";

	private static $staticUserNameForRegister = "userd6cfddebc0";
	private static $staticPasswordForRegister = "pass66c2450202";

	private $session;

	public function __construct($session) {
		$this->session = $session;
	}

	public function loginWithCredentials() {


		$this->session->setSessionKey("sessionUserName", $inputUserName);
		$this->session->setSessionKey("sessionPassword", $inputPassword);

		if ($this->authentication()) {
			$this->session->setSessionKey("loggedIn", true);
		}

		if ($this->authenticationForRegisterTest()) {
			$this->session->setSessionKey("loggedIn", true);
		}

		if ($this->authenticationForCookie()) {
			$this->session->setSessionKey("loggedIn", true);
		}
	}

	private function authentication() : bool {
		return ($this->session->getSessionKey("sessionPassword") == self::$staticPassword && $this->session->getSessionKey("sessionUserName") == self::$staticUserName);
	}
	
	private function authenticationForRegisterTest() : bool {
		return ($this->session->getSessionKey("sessionPassword") == self::$staticPasswordForRegister && $this->session->getSessionKey("sessionUserName") == self::$staticUserNameForRegister);
	}
	
	private function authenticationForCookie() : bool {
		return (password_verify(self::$staticPassword, $this->session->getSessionKey("sessionPassword")) && $this->session->getSessionKey("sessionUserName") == self::$staticUserName);
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