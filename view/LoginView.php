<?php

namespace View;

class LoginView {
	private static $login = 'LoginView::Login';
	private static $logout = 'LoginView::Logout';
	private static $name = 'LoginView::UserName';
	private static $password = 'LoginView::Password';
	private static $cookieName = 'LoginView::CookieName';
	private static $cookiePassword = 'LoginView::CookiePassword';
	private static $keep = 'LoginView::KeepMeLoggedIn';
	private static $messageId = 'LoginView::Message';

	private $session;
	private $authModel;
	
	/**
	 * Construct function
	 *
	 * @param \Model\Session $startSession and \Model\Auth $auth
	 */
	public function __construct(\Model\Session $startSession, \Model\Auth $auth) {
		$this->session = $startSession;
		$this->authModel = $auth;
	}

	/**
	 * Create HTTP response
	 *
	 * Should be called after a login attempt has been determined
	 *
	 * @return  void BUT writes to standard output and cookies!
	 */
	public function response() {
		$message = $this->authModel->loadMessage();
		/*
		if ($this->userWantsToLogout()) {
			$message = $this->authModel->logoutMessage();
		}
		*/
		$response = $this->generateLoginFormHTML($message);
		
		if ($this->authModel->authentication()) {
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
					
					<input type="submit" name="' . self::$login . '" value="login" />
				</fieldset>
			</form>
		';
	}
	public function userWantsToLogin() : bool {
		return (isset($_POST[self::$login]) && $_POST[self::$login] == 'login');
	}

	public function userWantsToLogout() : bool {
		return (isset($_POST[self::$logout]) && $_POST[self::$logout] == 'logout');
	}

	public function resetUserCredentials() {
		$_POST[self::$name] = '';
		$_POST[self::$password] = '';
	}

	public function getUserCredentials() {
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
/*
	private function getUserName() {
		if ($this->hasUserName()) {
			return $_POST[self::$name];
		}
	}
*/
	private function hasUserName() : bool {
		return (isset($_POST[self::$name]) && !empty($_POST[self::$name]));
	}

	private function hasPassword() : bool {
		return (isset($_POST[self::$password]) && !empty($_POST[self::$password]));
	}
}