<?php

namespace Model;

class Session {	
	public function __construct() {
		if (session_status() == PHP_SESSION_NONE) {
			// Disallow access to session cookie by Javascript.
			ini_set('session.cookie_httponly', true); 
			// Removing possibility of session ID injection and session ID leak.
			ini_set('session.use_trans_sid', false); 
			// Prevents session module to use session ID values set by GET/POST/URL 
			// when session ID cookie is not initialized. 
			ini_set('session.use_only_cookies', true); 
			session_start();
		}
	}

	public function isLoggedIn() {
        if(isset($_SESSION["loggedIn"])){
            return $_SESSION["loggedIn"];
        }
        return false;
	}
	
	public function userLogsOut() {
		$this->setSessionKey("loggedIn", false);
	}

	public function userLogsIn() {
		$this->setSessionKey("loggedIn", true);
	}

	public function isEditModeActivated() {
        if(isset($_SESSION["editMode"])){
            return $_SESSION["editMode"];
        }
        return false;
	}

	public function stopEditMode() {
		$this->setSessionKey("editMode", false);
	}

	public function startEditMode() {
		$this->setSessionKey("editMode", true);
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

	public function setRegisteredUsername($username) {
        $_SESSION["username"] = $username;
	}

	public function unsetRegisteredUsername() {
		if (isset($_SESSION["username"])) {
			unset($_SESSION["username"]);
		}
	}
	
    public function getRegisteredUsername() {
        if(isset($_SESSION["username"]) && strlen($_SESSION["username"]) > 0){
            return $_SESSION["username"];
        }
        else {
            return "";
        }
	}
}