<?php

namespace View;

class LoginView {
	private static $loginCSRF;
	private static $login = 'LoginView::Login';
	private static $logout = 'LoginView::Logout';
	private static $name = 'LoginView::UserName';
	private static $password = 'LoginView::Password';
	private static $cookieName = 'LoginView::CookieName';
	private static $cookiePassword = 'LoginView::CookiePassword';
	private static $keep = 'LoginView::KeepMeLoggedIn';
	private static $messageId = 'LoginView::Message';

	private $session;
	private $loginModel;
	
	/**
	 * Construct function
	 *
	 * @param \Model\Session $startSession and \Model\Auth $auth
	 */
	public function __construct(\Model\Session $startSession, \Model\LoginModel $loginModel) {
		$this->session = $startSession;
		$this->loginModel = $loginModel;
		$this->setHttpUserAgent();
	}

	private function setHttpUserAgent() {
		if (!isset($_SESSION["AGENT_007"])) {
			$hashedUserAgent = password_hash($_SERVER['HTTP_USER_AGENT'], PASSWORD_BCRYPT);
			$this->session->setSessionKey("AGENT_007", $hashedUserAgent);
		}
	}

	public function isHttpUserAgentOriginal() : bool {
		if ($_SERVER['HTTP_USER_AGENT'] == "Other") { // Using no database, had no time setting up so did a non-generic solution.
			return false;
		} else {
			return (password_verify($_SERVER['HTTP_USER_AGENT'], $this->session->getSessionKey("AGENT_007")));
		}
	}

	/**
	 * Create HTTP response
	 *
	 * Should be called after a login attempt has been determined
	 *
	 * @return  void BUT writes to standard output and cookies!
	 */
	public function response() {
		$message = $this->session->getSessionMessage();

		$response = $this->generateLoginFormHTML($message);
		
		if ($this->session->isLoggedIn()) {
			$response = $this->generateLogoutButtonHTML($message);	
		}

		return $response;
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
			<form method="post" class="form" id="loginForm"> 
				<fieldset>
					<legend>Login - enter Username and password</legend>
					<p id="' . self::$messageId . '">' . $message . '</p>
					
					<label for="' . self::$name . '">Username :</label>
					<input type="text" id="' . self::$name . '" name="' . self::$name . '" value="' . $this->session->getSessionKey("sessionUserName") . '" />

					<label for="' . self::$password . '">Password :</label>
					<input type="password" id="' . self::$password . '" name="' . self::$password . '"  />

					<label for="' . self::$keep . '">Keep me logged in  :</label>
					<input type="checkbox" id="' . self::$keep . '" name="' . self::$keep . '" />
					
					<input type="hidden" name="' . self::$loginCSRF . '" value="sdaldjvnoaida895723juigbbvfdasid7378892234jadbaKBD"/>

					<input type="submit" name="' . self::$login . '" value="login" />
				</fieldset>
			</form>
		';
	}

	public function userWantsToLogin() : bool {
		return (isset($_POST[self::$login]));
	}

	public function userWantsToReload() {
		
	}

	public function userWantsToLogout() : bool {
		return (isset($_POST[self::$logout]));
	}

	public function userWantsToKeepLogin() : bool {
		return (isset($_POST[self::$keep])) != null;
	}

	public function hasCookie($cookie_Name) {
		return (isset($_COOKIE[$cookie_Name]));
	}

	public function getCookieNameUN() {
		return self::$cookieName;
	}

	public function getCookieNamePWD() {
		return self::$cookiePassword;
	}

	public function getLoginCSRF() {
		if (isset($_POST[self::$loginCSRF])) {
			return self::$loginCSRF;
		}
	}

	public function resetUserCredentials() {
		$_POST[self::$name] = '';
		$_POST[self::$password] = '';
	}

	public function getUserCredentials() : array {
		$inputUserName = '';
		$inputPassword = '';

		if ($this->hasUserName()) {
			$inputUserName = $_POST[self::$name];	
		}

		if ($this->hasPassword()) {
			$inputPassword = $_POST[self::$password];
		}

		return array($inputUserName, $inputPassword);
	}

	private function hasUserName() : bool {
		return (isset($_POST[self::$name]) && !empty($_POST[self::$name]));
	}

	private function hasPassword() : bool {
		return (isset($_POST[self::$password]) && !empty($_POST[self::$password]));
	}
}