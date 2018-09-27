<?php

namespace Model;

class Session {
	private $HTTP_USER_AGENT;
	private $CSRF = 'sdaldjvnoaida895723juigbbvfdasid7378892234jadbaKBD';
	private $session;
	private $hiJack;

	public function __construct() {
		if (session_status() == PHP_SESSION_NONE) {
			ini_set('session.cookie_httponly', true); // Disallow access to session cookie by Javascript.
			ini_set('session.use_trans_sid', false); // Removing possibility of session ID injection and session ID leak.
			ini_set('session.use_only_cookies', true); // Prevents session module to use session ID values set by GET/POST/URL when session ID cookie is not initialized. 
			session_start();
		}
		$this->setBrowsers();
	}

	private function setBrowsers() {
		if (!empty($this->HTTP_USER_AGENT)) {
			$this->checkHijacked($_SERVER['HTTP_USER_AGENT']);
		} else {
			$this->HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
		}
	}

	public function isHijacked() : bool {
		if (empty($this->hiJack)) {
			return false;
		} else {
			return $this->hiJack;
		}
	}	

	private function checkHijacked($browser) {
		if ($this->HTTP_USER_AGENT != $browser) {
			$this->hiJack = true;
		} else {
			$this->hiJack = false;
		}
	}

	public function destroy() {
		session_destroy();
	}
	
	public function unsetSessionKey($sessionData) {
		if (isset($_SESSION[$sessionData])) {
			unset($_SESSION[$sessionData]);
		}
	}

	public function setSessionKey($sessionName, $sessionValue) {
		$_SESSION[$sessionName] = $sessionValue;
	}

	public function getSessionKey($sessionData) {
		if (isset($_SESSION[$sessionData])) {
			return $_SESSION[$sessionData];
		} else {
			return "";
		}
	}

	public function setSessionMessage($message) {
        $_SESSION["message"] = $message;
	}

	public function unsetSessionMessage() {
		if (isset($_SESSION["message"])) {
			unset($_SESSION["message"]);
		}
	}
	
    public function getSessionMessage() {
        if(isset($_SESSION["message"]) && strlen($_SESSION["message"]) > 0){
            return $_SESSION["message"];
        }
        else {
            return "";
        }
	}
	
	public function isLoggedIn() {
        if(isset($_SESSION["loggedIn"])){
            return $_SESSION["loggedIn"];
        }
        return false;
    }

	public function addCookie($cookieName, $cookieValue) {
		setcookie($cookieName, $cookieValue, time() + (86400 * 30), '/');
	}

	public function getCookieValue($cookieName) {
		return $_COOKIE[$cookieName];
	}

	public function deleteCookie($cookieName) {
        setcookie($cookieName, '', time() - 3600);
    }
}