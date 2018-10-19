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
	private $database;
	private $messageFromException = '';
	
	public function __construct(\Model\Session $startSession, \Model\DatabaseModel $a_database) {
		$this->session = $startSession;
		$this->database = $a_database;
	}

	/**
	 * Create HTTP response
	 *
	 * Should be called after a login attempt has been determined
	 *
	 * @return  void BUT writes to standard output and cookies!
	 */
	public function response() {
		$message = $this->messageFromException;

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
					<input type="text" id="' . self::$name . '" name="' . self::$name . '" value="' . $this->getUserName() . '" />

					<label for="' . self::$password . '">Password :</label>
					<input type="password" id="' . self::$password . '" name="' . self::$password . '"  />

					<label for="' . self::$keep . '">Keep me logged in  :</label>
					<input type="checkbox" id="' . self::$keep . '" name="' . self::$keep . '" />

					<input type="submit" name="' . self::$login . '" value="login" />
				</fieldset>
			</form>
		';
	}

	public function loginUser() {
		try {
			if($this->userWantsToLogin()) {
				$this->tryToLoginUser();
				$this->setLoginMessage();
				$this->addUsernameToSession();
			}
		} catch (\Exception $e) {
            $this->messageFromException = $e->getMessage();
        }
	}
	// Placed here due to high abstraction level and strong connection to loginUser.
	private function tryToLoginUser() {
		if ($this->postEmptyUserName()) {
			throw new \Exception("Username is missing");	
		}

		if ($this->postEmptyPassword()) {
			throw new \Exception("Password is missing");
		}
		// Encrypt information so it can be compared with database information using hash_equals.
		$inputUserName = $this->database->encryptWithCrypt($_POST[self::$name]);
		$inputPassword = $this->database->encryptWithCrypt($_POST[self::$password]);
		$users_HTTP_USER_AGENT = $this->getHashedNoSpacedHTTP_USER_AGENT();
		$loginWithCookie = false;

		$this->database->checkCredentials($inputUserName, $inputPassword, $users_HTTP_USER_AGENT, $loginWithCookie);
	}

	public function logoutUser() {
		try {
			if($this->userWantsToLogout()) {
				$this->tryToLogoutUser();
			}
		} catch (\Exception $e) {
            $this->messageFromException = $e->getMessage();
        }	
	}
	// Placed here due to strong connection to logoutUser.
	private function tryToLogoutUser() {
		$this->session->userLogsOut();
		$this->delete_PHPSESSID_cookie();
		$this->session->destroy();
		$this->setLogoutMessage();
	}

	public function loginUserWithCookie() {
		try {
			$this->tryToLoginUserWithCookie();
			$this->cookieFeedbackToUser();
		} catch (\Exception $e) {
            $this->messageFromException = $e->getMessage();
        }
	}
	// Placed here due to high abstraction level and strong connection to loginUserWithCookie.
	private function tryToLoginUserWithCookie() {
        $cookieName = $this->getCookieNameForUsername();
        $cookiePassword = $this->getCookieNameForPassword();
		$cookieValuePassword = $this->getCookieValue($cookiePassword);
		$cookieValueUsername = $this->getCookieValue($cookieName);
		$users_HTTP_USER_AGENT = $this->getHashedNoSpacedHTTP_USER_AGENT();
		$loginWithCookie = true;

		$this->database->checkCredentials($cookieValueUsername, $cookieValuePassword, $users_HTTP_USER_AGENT, $loginWithCookie);
	}
	
	public function sendCookieToUser() {
		$cookieNameUserName = $this->getCookieNameForUsername();
		$cookieNamePassword = $this->getCookieNameForPassword();
		
		if ($this->emptyUserName()) {
			$cookieValuePassword = $this->getCookieValue($cookieNamePassword);
			$cookieValueUserName = $this->getCookieValue($cookieNameUserName);
		} else {
			$cookieValueUserName = $this->database->encryptWithCrypt($_POST[self::$name]);
			$cookieValuePassword = $this->database->encryptWithCrypt($_POST[self::$password]);
		}

        $this->addCookie($cookieNameUserName, $cookieValueUserName);
        $this->addCookie($cookieNamePassword, $cookieValuePassword);
	}

	public function authorizedUserWantsToStayLoggedIn() : bool {
        return ($this->userWantsToKeepLogin() && $this->session->isLoggedIn());
	}
	
	public function hasCookiesForUsernameAndPassword() : bool {
        return ($this->hasCookie($this->getCookieNameForUsername()) && $this->hasCookie($this->getCookieNameForPassword()));
    }

	public function showRegisteredUserTheLoginPage() : bool {
        return isset($_GET["login"]);
	}

	public function userWantsToLogin() : bool {
		return (isset($_POST[self::$login]));
	}

	public function userWantsToLogout() : bool {
		return (isset($_POST[self::$logout]));
	}

	public function successfullRegisterView() {
		$this->setRegisterMessage();
		$_POST[self::$name] = $this->session->getRegisteredUsername();
	}

	public function resetMessage() {
		$this->messageFromException = "";
	}

	public function hasNoSessionCookie() : bool {
        return (!isset($_COOKIE['PHPSESSID']));
	}

	public function setCookieMessage() {
		$this->messageFromException = "Welcome and you will be remembered";
	}

	public function doesCookieExist($cookieName) : bool {
		return isset($_COOKIE[$cookieName]);
	}

	public function delete_PHPSESSID_cookie() {
		$this->deleteCookie("PHPSESSID");
	}

	private function cookieFeedbackToUser() {
        if ($this->session->isLoggedIn()) {
            $this->setAuthCookieMessage();
        } else {
			$this->removeNotAuthorizedCookies();
			$this->setFailedAuthCookieMessage();
        }
	}

	private function removeNotAuthorizedCookies() {
		$cookieNameUserName = $this->getCookieNameForUsername();
		$cookieNamePassword = $this->getCookieNameForPassword();

		$this->deleteCookie($cookieNameUserName);
		$this->deleteCookie($cookieNamePassword);
	}

	private function getHashedNoSpacedHTTP_USER_AGENT() {
		$HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
		$HTTP_USER_AGENT = str_replace(' ', '', $HTTP_USER_AGENT);
		$HTTP_USER_AGENT = substr($HTTP_USER_AGENT, 21, 33);
		$hashedNoSpacedHTTP_USER_AGENT = $this->database->encryptWithCrypt($HTTP_USER_AGENT);
		return $hashedNoSpacedHTTP_USER_AGENT;
	}

	private function addUsernameToSession() {
		if(!$this->emptyUserName()) {
			$this->session->setRegisteredUsername($_POST[self::$name]);
		}
	}

	private function getUserName() {
		if (isset($_POST[self::$name]) && !empty($_POST[self::$name])) {
			return $_POST[self::$name];
		}
	}

	private function postEmptyUserName() : bool {
		return (isset($_POST[self::$name]) && empty($_POST[self::$name]));
	}

	private function emptyUserName() : bool {
		return empty($_POST[self::$name]);
	}

	private function postEmptyPassword() : bool {
		return (isset($_POST[self::$password]) && empty($_POST[self::$password]));
	}

	private function get_PHPSESSID_Cookie_Value() {
		if(isset($_COOKIE["PHPSESSID"])){
			return $_COOKIE["PHPSESSID"];
		}
	}

	private function userWantsToKeepLogin() : bool {
		return (isset($_POST[self::$keep])) != null;
	}

	private function hasCookie($cookie_Name) {
		return (isset($_COOKIE[$cookie_Name]));
	}

	private function getCookieNameForUsername() {
		return self::$cookieName;
	}

	private function getCookieNameForPassword() {
		return self::$cookiePassword;
	}

	private function addCookie($cookieName, $cookieValue) {
		setcookie($cookieName, $cookieValue, time() + (86400 * 30), '/');
	}

	private function deleteCookie($cookieName) {
        setcookie($cookieName, '', time() - 3600);
	}

	private function getCookieValue($cookieName) {
		if(isset($_COOKIE[$cookieName])){
			return $_COOKIE[$cookieName];
		}
	}

	private function setLoginMessage() {
		$this->messageFromException = "Welcome";
	}

	private function setLogoutMessage() {
		$this->messageFromException = "Bye bye!";
	}

	private function setRegisterMessage() {
		$this->messageFromException = "Registered new user.";
	}

	private function setFailedAuthCookieMessage() {
		$this->messageFromException = "Wrong information in cookies";
	}

	private function setAuthCookieMessage() {
		$this->messageFromException = "Welcome back with cookie";
	}
}