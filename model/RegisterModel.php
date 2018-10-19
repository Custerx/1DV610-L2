<?php

namespace Model;

class RegisterModel {
	private static $username;
	private static $password;
	private static $repeatPassword;

	public function __construct($a_username, $a_password, $a_repeatPassword) {
		self::$username = $a_username;
		self::$password = $a_password;
		self::$repeatPassword = $a_repeatPassword;
		$this->registerValidation();
	}
	
	private function validateUserName() : bool {
		return (strlen(self::$username) < 3);
	}

	private function validatePassword() : bool {
		return (strlen(self::$password) < 6);
	}

	private function validateThatPasswordMatch() : bool {
		return (self::$password != self::$repeatPassword);
	}

	private function validateUserNameCharacters() : bool {
		return self::$username != strip_tags(self::$username);
	}

	public function registerValidation() {
		if ($this->validateUserName() && $this->validatePassword()) {
			throw new \Exception('Username has too few characters, at least 3 characters. Password has too few characters, at least 6 characters.');
		} else if ($this->validatePassword()) {
			throw new \Exception('Password has too few characters, at least 6 characters.');
		} else if ($this->validateUserName()) {
			throw new \Exception('Username has too few characters, at least 3 characters.');
		} else if ($this->validateThatPasswordMatch()) {
			throw new \Exception('Passwords do not match.');
		} else if ($this->validateUserNameCharacters()) {
			throw new \Exception('Username contains invalid characters.');
		} else {
			// User validated. No exception needed.
		}
	}
}