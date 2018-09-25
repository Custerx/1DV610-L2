<?php

namespace Model;

class Auth {
	private static $setUserName = "Admin";
	private static $setPassword = "Password";

    private $message = "";
	private $session;

	public function __construct($session) {
		$this->session = $session;
	}

	public function loginWithCredentials($credentials) {
		list($inputUserName, $inputPassword) = $credentials;

		$this->session->setSessionKey("sessionUserName", $inputUserName);
		$this->session->setSessionKey("sessionPassword", $inputPassword);

		if ($this->authentication()) {
			echo "correct login";
		}

	}

	public function setMessage($message) {
		$this->message = $message;
    }

	public function authentication() : bool {
		return ($this->session->getSessionKey("sessionPassword") == self::$setPassword && $this->session->getSessionKey("sessionUserName") == self::$setUserName);
    }

	public function loadMessage() : string {
		return $this->message;
    }

    /**
	 * Checks username and password input.
	*/
	public function validation() {
        if (empty($this->session->getSessionKey("sessionUserName")) && empty($this->session->getSessionKey("sessionPassword"))) {
            $this->message = '';
        } else if (empty($this->session->getSessionKey("sessionUserName"))) {
			$this->message = 'Username is missing';
		} else if (empty($this->session->getSessionKey("sessionPassword"))) {
			$this->message = 'Password is missing';
		} else if ($this->session->getSessionKey("sessionUserName") && $this->session->getSessionKey("sessionPassword")) {
			if ($this->authentication()) {
				$this->message = 'Welcome';
			} else {
				$this->message = 'Wrong name or password';
			}
		} else {
			$this->message = '';
		}
	}
}