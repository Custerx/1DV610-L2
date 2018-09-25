<?php

namespace Controller;

class LoginController {
    private $loginView;
    private $authorization;
    private $dateTimeView;
    private $layoutView;
    private $session;

	public function __construct(\Model\Auth $auth, \Model\Session $session) {
        $this->session = $session;
        $this->authorization = $auth;

        // CREATE OBJECTS OF THE VIEWS
        $this->loginView = new \View\LoginView($this->session, $this->authorization);
        $this->dateTimeView = new \View\DateTimeView();
        $this->layoutView = new \View\LayoutView();
    }

    public function doLogin() {
        // INPUT

        if ($this->loginView->userWantsToLogin()) {
            if ($this->authorization->authentication()) {
                echo "your logged in already";
                // unset($_POST);
                $this->authorization->setMessage('');
                $this->renderView();
            } else {
                $credentials = $this->loginView->getUserCredentials();
                $this->authorization->loginWithCredentials($credentials);
                $this->authorization->validation();
                $this->renderView();
            }
        } else {
            if (!$this->authorization->authentication()) {
                echo "your already logged out";
                $this->authorization->setMessage('');
                $this->renderView();
            } else {
                $this->authorization->setMessage('Bye Bye!');
                $this->loginView->resetUserCredentials();
                $credentials = $this->loginView->getUserCredentials();
                $this->authorization->loginWithCredentials($credentials);

                $this->session->destroy();
                echo "logging out";
                $this->renderView();
            }
        }
    }

    private function renderView() {
        return $this->layoutView->render($this->authorization->authentication(), $this->loginView, $this->dateTimeView);
    }
}