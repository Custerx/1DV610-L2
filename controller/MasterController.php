<?php

namespace Controller;

class MasterController {
    private $loginView;
    private $registerView;
    private $registerModel;

    private $loginModel;
    private $dateTimeView;
    private $layoutView;
    private $session;
    private $database;

	public function __construct() {
        // CREATE OBJECT OF THE MODELS
        $this->session = new \Model\Session();
        $this->loginModel = new \Model\LoginModel($this->session);
        $this->database = new \Model\DatabaseModel($this->session);

        // CREATE OBJECTS OF THE VIEWS
        $this->loginView = new \View\LoginView($this->session, $this->database);
        $this->dateTimeView = new \View\DateTimeView();
        $this->layoutView = new \View\LayoutView();
        $this->registerView = new \View\RegisterView($this->session, $this->database);
    }

    public function routerHandler() {
        if ($this->registerView->userWantsToViewRegisterPage()) {
            $this->doRegisterManagement();
        } else if (!empty($_POST)) {
            $this->doLoginManagement();
        } else if ($this->loginView->showRegisteredUserTheLoginPage()) { 
            $this->loginView->successfullRegisterView();
            $this->renderView();
        } else if ($this->hasAllOtherCookie() && $this->hasNoSessionCookie()) {
            $this->doCookieManagement();
        } else {
            $this->renderView();
        }
    }

    private function doRegisterManagement() {
        if ($this->registerView->userSuccessfullyRegistered()) {
            $this->registerView->makeUsernameAvailableForLoginPage();
            $this->registerView->redirectToLoginPage();
        } else {
            $this->renderRegisterView();
        }
    }

    private function renderRegisterView() {
        return $this->layoutView->renderRegister(false, $this->registerView, $this->dateTimeView);
    }

    private function doLoginManagement() {
        if ($this->loginView->userWantsToLogin()) {
            $this->login();
        } else if ($this->loginView->userWantsToLogout()) {
            $this->logout();
        } else {
            $this->loginView->resetMessage();
            $this->renderView();
        }
    }

    private function login() {
        if ($this->session->isLoggedIn()) {
            $this->loginView->resetMessage();
            $this->renderView();
        } else {
            $this->loginView->loginUser();
            $this->renderView();
        }
    }

    private function logout() {
        if ($this->session->isLoggedIn() == false) {
            $this->loginView->resetMessage();
            $this->renderView();
        } else {
            $this->loginView->logoutUser();
            $this->renderView();
        }
    }

    private function renderView() {
        return $this->layoutView->render($this->session->isLoggedIn(), $this->loginView, $this->dateTimeView);
    }

    private function doCookieManagement() {
        echo "kaka";
        $cookieName = $this->loginView->getCookieNameUN();
        $cookiePassword = $this->loginView->getCookieNamePWD();
        $cookieValuePasswordHashed = $this->session->getCookieValue($cookiePassword);

        $credentials = array($this->session->getCookieValue($cookieName), $cookieValuePasswordHashed);
        $this->loginModel->loginWithCredentials($credentials);

        if ($this->session->isLoggedIn()) {
            $this->session->setSessionMessage('Welcome back with cookie');
        } else {
            $this->session->setSessionMessage('Wrong information in cookies');
        }

        $this->renderView();
    }

    private function handleCookies() {
        if ($this->loginView->userWantsToKeepLogin() && $this->session->isLoggedIn()) {
            $cookieValueUserName = $this->session->getSessionKey("sessionUserName");
            $cookieValuePasswordHashed = $this->hashCookiePassword($this->session->getSessionKey("sessionPassword"));
            $cookieNameUserName = $this->loginView->getCookieNameUN();
			$cookieNamePassword = $this->loginView->getCookieNamePWD();

            $this->session->addCookie($cookieNameUserName, $cookieValueUserName);
            $this->session->addCookie($cookieNamePassword, $cookieValuePasswordHashed);
            $this->session->setSessionMessage('Welcome and you will be remembered');
        }
    }

    private function hashCookiePassword($cookiePassword) {
        return password_hash($cookiePassword, PASSWORD_BCRYPT);
    }

    private function hasAllOtherCookie() : bool {
        return ($this->loginView->hasCookie($this->loginView->getCookieNameUN()) && $this->loginView->hasCookie($this->loginView->getCookieNamePWD()));
    }

    private function hasNoSessionCookie() : bool {
        return (!isset($_COOKIE['PHPSESSID']));
    }
}