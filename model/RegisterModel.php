<?php

namespace Model;

class RegisterModel {
    private $inputPassword;
    private $inputPasswordRepeat;
    private $inputUserName;
    private $registerMessage;

	private $session;

	public function __construct($session) {
		$this->session = $session;
	}

	public function registerWithCredentials($credentials) {
		list($inputUserName, $inputPassword, $inputPasswordRepeat) = $credentials;

        $this->inputUserName = $inputUserName;
        $this->inputPassword = $inputPassword;
		$this->inputPasswordRepeat = $inputPasswordRepeat;
	}
	
	private function validateUserName() : bool {
		return (strlen($this->inputUserName) < 3);
	}

	private function validatePassword() : bool {
		return (strlen($this->inputPassword) < 6);
	}

	private function validateThatPasswordMatch() : bool {
		return ($this->inputPassword != $this->inputPasswordRepeat);
	}

	private function validateUserNameCharacters() : bool {
		return ($this->inputUserName != strip_tags($this->inputUserName));
	}

	private function validateUserNameIsExclusive() : bool {
		return ($this->inputUserName == "Admin");
	}

    /**
	 * Checks username, password and passwordrepeat input.
	*/
	public function registerMessage() {
		if ($this->validateUserName() && $this->validatePassword()) {
			return 'Username has too few characters, at least 3 characters. Password has too few characters, at least 6 characters.';
		} else if ($this->validatePassword()) {
			return 'Password has too few characters, at least 6 characters.';
		} else if ($this->validateUserName()) {
			return 'Username has too few characters, at least 3 characters.';
		} else if ($this->validateThatPasswordMatch()) {
			return 'Passwords do not match.';
		} else if ($this->validateUserNameIsExclusive()) {
			return 'User exists, pick another username.';
		} else if ($this->validateUserNameCharacters()) {
			return 'Username contains invalid characters.';
		} else {
			return 'Registered new user';
		}
	}
}