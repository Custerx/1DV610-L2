<?php

namespace Model;

class Session {
	private $session;

	public function __construct() {
		if (session_status() == PHP_SESSION_NONE) {
			session_start();
		}
	}

	public function destroy() {
		// Remove all keys from SESSION
		session_destroy();
	}
	
	public function unsetSessionKey($sessionData) {
		if (isset($_SESSION[$sessionData])) {
			// Remove a key from SESSION
			unset($_SESSION[$sessionData]);
		}
	}

	public function setSessionKey($sessionName, $sessionValue) {
		// Add a key to SESSION
		$_SESSION[$sessionName] = $sessionValue;
	}

	public function getSessionKey($sessionData) {
		if (isset($_SESSION[$sessionData])) {
		// Get a key from SESSION
			return $_SESSION[$sessionData];
		}
	}
}