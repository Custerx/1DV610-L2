<?php

namespace View;

class AccountView {
    private static $edit = 'AccountView::Edit';
    private static $confirm = 'AccountView::Confirm';
	private static $passwordRepeat = 'AccountView::PasswordRepeat';
	private static $name = 'AccountView::UserName';
	private static $password = 'AccountView::Password';
	private static $messageId = 'AccountView::Message';

    public function response() {
		$message = $this->messageFromException;

		$response = $this->generateAccountDetailsHTML($message);

		return $response;
	}

    public function render($isLoggedIn, LoginView $v, DateTimeView $dtv, AccountView $a) {
        echo '<!DOCTYPE html>
        <html>
            <head>
            <meta charset="utf-8">
            <title>Login Example</title>
            </head>
            <body>
            <h1>Assignment 2</h1>
            ' . $this->renderIsLoggedInForLogin($isLoggedIn) . '
            
            <div class="container">
                ' . $v->response() . '
                
                ' . $dtv->show() . '

                ' . $a->response($isLoggedIn) . '
            </div>
            </body>
        </html>
        ';
    }

    private function generateEditFormHTML($message) {
		return '
			<form method="post" action="?confirm"> 
				<fieldset>
					<legend>Change Username - enter new Username and password</legend>
					<p id="' . self::$messageId . '">' . $message . '</p>
					
					<label for="' . self::$name . '">Username :</label>
					<input type="text" id="' . self::$name . '" name="' . self::$name . '" value=" />

					<label for="' . self::$password . '">Password :</label>
					<input type="password" id="' . self::$password . '" name="' . self::$password . '"  />

					<label for="' . self::$passwordRepeat . '">Repeat password  :</label>
					<input type="password" id="' . self::$passwordRepeat . '" name="' . self::$passwordRepeat . '" />

					<input type="submit" name="' . self::$confirm . '" value="confirm" />
				</fieldset>
			</form>
		';
	}

    private function generateAccountDetailsHTML($member) {
        return '
            <h3>Account Details</h3>
                <p>Username: ' . $this->session->getRegisteredUsername() . '</p>
		';
    }

    private function generateEditButtonHTML() {
		return '
			<form  method="post" action="?edit">
				<input type="submit" name="' . self::$edit . '" value="Edit"/>
			</form>
		';
    }
    
    public function userWantsToEditAccountDetails() : bool {
		return (isset($_POST[self::$change]));
    }
    

}