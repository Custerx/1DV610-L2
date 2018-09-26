<?php

namespace Model;

class RegisterModel {
    private $inputPassword;
    private $inputPasswordRepeat;
    private $inputUserName;
    private $registerMessage;

	public function registerWithCredentials($credentials) {
		list($inputUserName, $inputPassword, $inputPasswordRepeat) = $credentials;

        $this->inputUserName = $inputUserName;
        $this->inputPassword = $inputPassword;
        $this->inputPasswordRepeat = $inputPasswordRepeat;
    }

    /**
	 * Checks username, password and passwordrepeat input.
	*/
	public function registerMessage() {
		if (empty($this->inputUserName())) {
			return 'Username is missing';
		} else if (empty($this->inputPassword())) {
			return 'Password is missing';
		} else if (empty($this->inputPasswordRepeat())) {
			return 'Repeat password is missing';
		} else {
				return 'Wrong name or password';
		}
	}
}