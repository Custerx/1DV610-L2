<?php

namespace View;

class AccountView {
    private static $edit = 'AccountView::Edit';
    private static $confirm = 'AccountView::Confirm';
	private static $passwordRepeat = 'AccountView::PasswordRepeat';
	private static $name = 'AccountView::UserName';
	private static $password = 'AccountView::Password';
	private static $messageEdit = 'AccountView::Message';
	private static $placeHolder = "Rogge";
    
    private $messageFromException = '';
	private $session;
    private $database;

	public function __construct(\Model\Session $startSession, \Model\DatabaseModel $a_database) {
		$this->session = $startSession;
		$this->database = $a_database;
    }
    
    public function response() {
		$message = $this->messageFromException;
        
        $response = $this->generateAccountDetailsHTML($message);

        if ($this->session->isEditModeActivated()) {
			$response = $this->generateEditFormHTML($message);	
		}

		return $response;
	}

    private function generateEditFormHTML($message) {
		return '
			<form method="post" action="?confirm"> 
				<fieldset>
					<legend>Change Username - enter new Username and password</legend>
					<p id="' . self::$messageEdit . '">' . $message . '</p>
					
					<label for="' . self::$name . '">Username :</label>
					<input type="text" id="' . self::$name . '" name="' . self::$name . '" />

					<label for="' . self::$password . '">Password :</label>
					<input type="password" id="' . self::$password . '" name="' . self::$password . '"  />

					<label for="' . self::$passwordRepeat . '">Repeat password  :</label>
					<input type="password" id="' . self::$passwordRepeat . '" name="' . self::$passwordRepeat . '" />

					<input type="submit" name="' . self::$confirm . '" value="confirm" />
				</fieldset>
			</form>
		';
	}

    private function generateAccountDetailsHTML($message) {
        return '
            <h3>Account Details</h3>
            <p id="' . self::$messageEdit . '">' . $message . '</p>
            
            <p>Username: ' . $this->session->getRegisteredUsername() . '</p>
            
            <form method="post">
				<input type="submit" name="' . self::$edit . '" value="edit"/>
			</form>
		';
    }

    public function successfullEdit() {
		try {
            $this->tryToEditAccountDetails();
            $this->deleteOldAccountDetails();
			return true;
		} catch (\Exception $e) {
			$this->messageFromException = $e->getMessage();
		}
	}
	// Placed here due to high abstraction level and strong connection to userSuccessfullyRegistered.
	private function tryToEditAccountDetails() {			
		$inputUserName = $_POST[self::$name];
		$inputPassword = $_POST[self::$password];
		$inputPasswordRepeat = $_POST[self::$passwordRepeat];
		$users_HTTP_USER_AGENT = $this->noSpacedHTTP_USER_AGENT();
		$cookiePassword = $this->database->generateRandomPassword();

		if ($this->database->isUniqueUsername($inputUserName)) {
			$this->database->saveMemberToJSONFile(new \Model\Member($inputUserName, $inputPassword, 
				$inputPasswordRepeat, $users_HTTP_USER_AGENT, $cookiePassword));
		} else {
			throw new \Exception("User exists, pick another username.");
		}
    }
    // TODO : Create previous username getter.
    public function deleteOldAccountDetails() {
        $usernameHashed = $this->database->encryptWithCrypt(self::$placeHolder);
        $this->database->deleteMember($usernameHashed);
    }
    
    public function userWantsToEditAccountDetails() : bool {
		return (isset($_POST[self::$edit]));
    }
    
    public function userWantsToConfirmAccountDetails() : bool {
		return (isset($_POST[self::$confirm]));
    }

    public function successfullEditMessage() {
		$this->messageFromException = "Account successfully updated.";
    }
    
    public function showNewUsername() {
		$this->session->setRegisteredUsername($_POST[self::$name]);
	}

    private function noSpacedHTTP_USER_AGENT() {
		$HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
		$HTTP_USER_AGENT = str_replace(' ', '', $HTTP_USER_AGENT);
		$HTTP_USER_AGENT = substr($HTTP_USER_AGENT, 21, 33);
		return $HTTP_USER_AGENT;
	}
}