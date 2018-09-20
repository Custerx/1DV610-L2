<?php

namespace Model;

class Auth {
	private static $setUserName = "Admin";
	private static $setPassword = "Password";

    private $message = "";
    private $session;
    private $userPassword;
    private $userName;

	public function authentication() : bool {
		return ($this->userPassword == self::$setPassword && $this->userName == self::$setUserName);
    }

	public function loadMessage() : string {
		return $this->message;
    }

    public function logoutMessage() : string {
        return 'Bye bye!';
    }

    public function setUser($input) {
		$this->userName = $input;
    }

    public function setPassword($input) {
		$this->userPassword = $input;
    }

    /**
	 * Checks username and password input.
	 *
	 * @return string
	 */
	public function validation() {
        if (empty($this->userName) && empty($this->userPassword)) {
            $this->message = '';
        } else if (empty($this->userName)) {
			$this->message = 'Username is missing';
		} else if (empty($this->userPassword)) {
			$this->message = 'Password is missing';
		} else if ($this->userName && $this->userPassword) {
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