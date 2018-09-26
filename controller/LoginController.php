<?php

namespace Controller;

class LoginController {
    private $loginView;
    private $registerView;
    private $registerModel;

    private $authorization;
    private $dateTimeView;
    private $layoutView;
    private $session;

	public function __construct() {
        // CREATE OBJECT OF THE MODELS
        $this->session = new \Model\Session();
        $this->authorization = new \Model\Auth($this->session);
        $this->registerModel = new \Model\RegisterModel();
        // CREATE OBJECTS OF THE VIEWS
        $this->loginView = new \View\LoginView($this->session, $this->authorization);
        $this->dateTimeView = new \View\DateTimeView();
        $this->layoutView = new \View\LayoutView();
        $this->registerView = new \View\RegisterView();
    }

    public function routerHandler() {
        if ($this->registerView->wantsToRegisterV2()) {
            $this->doRegisterManagement();
        } else if (!empty($_POST)) {
            $this->doUserManagement();
        } else {
            $this->renderView();
        }
    }

    private function doRegisterManagement() {
        if ($this->registerView->userWantsToRegister()) {
            $credentials = $this->registerView->getRegisterCredentials();
            $this->registerModel->registerWithCredentials($credentials);
            // $this->session->setSessionMessage($this->authorization->validationMessage());

            $this->renderRegisterView();
        } else {
            $this->renderRegisterView();
        }
    }

    private function renderRegisterView() {
        return $this->layoutView->renderRegister(false, $this->registerView, $this->dateTimeView);
    }

    private function doUserManagement() {
        if ($this->loginView->userWantsToLogin()) {
            $credentials = $this->loginView->getUserCredentials();
            $this->authorization->loginWithCredentials($credentials);
            $this->session->setSessionMessage($this->authorization->validationMessage());

            $this->handleCookies();
            $this->renderView();
        } else if ($this->loginView->userWantsToLogout()) {
            $this->session->setSessionKey("loggedIn", false);
            $this->session->setSessionMessage('Bye bye!');

            // session_unset();
            $this->session->destroy();
            $this->renderView();
        } else {
            $this->session->setSessionMessage($this->authorization->validationMessage());
            $this->renderView();
        }
    }

    private function renderView() {
        return $this->layoutView->render($this->session->isLoggedIn(), $this->loginView, $this->dateTimeView);
    }

    private function handleCookies() {
        if ($this->loginView->userWantsToKeepLogin() && $this->authorization->authentication()) {
            $cookieValueUserName = $this->session->getSessionKey("sessionUserName");
            $cookieValuePassword = $this->session->getSessionKey("sessionPassword");
            $cookieNameUserName = $this->loginView->getCookieNameUN();
			$cookieNamePassword = $this->loginView->getCookieNamePWD();

			// $cookieValuePassword = $this->authorization->random_string($cookieValuePassword);

            $this->session->addCookie($cookieNameUserName, $cookieValueUserName);
            $this->session->addCookie($cookieNamePassword, $cookieValuePassword);
            $this->session->setSessionMessage('Welcome and you will be remembered');
        }
    }

    private function preCookie() : bool {
        return ($this->loginView->hasCookie($this->loginView->getCookieNameUN()) && $this->loginView->hasCookie($this->loginView->getCookieNamePWD()));
    }
/*
    public function loginManagement() {
        if($this->preCookie()) {
            $test = $this->loginView->getCookieNameUN();
            $test2 = $this->loginView->getCookieNamePWD();

            $cookieNameUserName = $this->session->getCookieValue($test);
            $cookieNamePassword = $this->session->getCookieValue($test2);

            $credentials = array($cookieNameUserName, $cookieNamePassword);
            
            if (!$this->loginView->userWantsToLogout()) {
                $this->authorization->loginWithCredentials($credentials);
            }
            
            if ($this->authorization->authentication() && !$this->loginView->userWantsToLogout()) {
                $this->authorization->setMessage('Welcome back with cookie');
                $this->renderView();
            } else {
                $this->doUserManagement();
            }
        } else {
            $this->doUserManagement();
        }
    } */
}