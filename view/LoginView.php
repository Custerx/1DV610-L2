<?php

class LoginView {
	private static $login = 'LoginView::Login';
	private static $logout = 'LoginView::Logout';
	private static $name = 'LoginView::UserName';
	private static $password = 'LoginView::Password';
	private static $cookieName = 'LoginView::CookieName';
	private static $cookiePassword = 'LoginView::CookiePassword';
	private static $keep = 'LoginView::KeepMeLoggedIn';
	private static $messageId = 'LoginView::Message';
	
	private static $setUserName = "Admin";
	private static $setPassword = "Password";
	private static $SESSION_MSG;
	private $message = "";
	private $session;
	
	/**
	 * Construct function
	 *
	 * @param \Model\Session $startSession
	 */
	public function __construct(\Model\Session $startSession) {
		$this->session = $startSession;
	}

	/**
	 * Create HTTP response
	 *
	 * Should be called after a login attempt has been determined
	 *
	 * @return  void BUT writes to standard output and cookies!
	 */
	public function response() {
		$this->validation();

		$this->session->setSessionKey("message", $this->message);
		$message = $this->session->getSessionKey("message");
		$this->session->unsetSessionKey("message");
		
		$response = $this->generateLoginFormHTML($message);
		
		if ($this->authentication()) {
			$response = $this->generateLogoutButtonHTML($message);	
		}

		return $response;
	}

	private function logout() {
		if ($this->authentication()) {
			$this->session->destroy();
		}
	}

	/**
	* Generate HTML code on the output buffer for the logout button
	* @param $message, String output message
	* @return  void, BUT writes to standard output!
	*/
	private function generateLogoutButtonHTML($message) {
		return '
			<form  method="post" >
				<p id="' . self::$messageId . '">' . $message .'</p>
				<input type="submit" name="' . self::$logout . '" value="logout"/>
			</form>
		';
	}
	
	/**
	* Generate HTML code on the output buffer for the logout button
	* @param $message, String output message
	* @return  void, BUT writes to standard output!
	*/
	private function generateLoginFormHTML($message) {
		return '
			<form method="post" > 
				<fieldset>
					<legend>Login - enter Username and password</legend>
					<p id="' . self::$messageId . '">' . $message . '</p>
					
					<label for="' . self::$name . '">Username :</label>
					<input type="text" id="' . self::$name . '" name="' . self::$name . '" value="' . $this->getRequestUserName() . '" />

					<label for="' . self::$password . '">Password :</label>
					<input type="password" id="' . self::$password . '" name="' . self::$password . '" />

					<label for="' . self::$keep . '">Keep me logged in  :</label>
					<input type="checkbox" id="' . self::$keep . '" name="' . self::$keep . '" />
					
					<input type="submit" name="' . self::$login . '" value="login" />
				</fieldset>
			</form>
		';
	}
	
	//CREATE GET-FUNCTIONS TO FETCH REQUEST VARIABLES
	private function getRequestUserName() {		
		//RETURN REQUEST VARIABLE: USERNAME
		if ($this->hasUserName()) {
			return $_REQUEST[self::$name];
		}
	}

	private function getRequestPassword() {
		//RETURN REQUEST VARIABLE: PASSWORD
		if ($this->hasPassword()) {
			return $_REQUEST[self::$password];
		}
	}

	private function hasUserName() : bool {
		return (isset($_REQUEST[self::$name]) && !$this->hasNoUserName());
	}

	private function hasPassword() : bool {
		return (isset($_REQUEST[self::$password]) && !$this->hasNoPassword());
	}	

	private function hasNoUserName() : bool {
		return empty($_POST[self::$name]);
	}

	private function hasNoPassword() : bool {
		return empty($_POST[self::$password]);
	}

	public function authentication() : bool {
		return ($this->getRequestPassword() == self::$setPassword && $this->getRequestUserName() == self::$setUserName);
	}

	public function loadSessionMessage() {
		if (isset($_SESSION[self::$SESSION_MSG])) {
			$MSG = $_SESSION[self::$SESSION_MSG];
			unset($_SESSION[self::$SESSION_MSG]);
			return $MSG;
		}
	}

	public function saveSessionMessage($toBeSaved) {
		$_SESSION[self::$SESSION_MSG] = $toBeSaved;
	}

	public function loadMessage() {
		return $this->message;
	}

	/**
	 * Checks username and password input.
	 *
	 * @return string
	 */
	private function validation() {
		if ($this->hasNoUserName() && $this->hasPassword()) {
			$this->message = 'Username is missing';
		} else if ($this->hasUserName() && $this->hasNoPassword()) {
			$this->message = 'Password is missing';
		} else if ($this->hasUserName() && $this->hasPassword()) {
			// Also check if isLoggedIn - how?
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