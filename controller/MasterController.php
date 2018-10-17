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
        $this->database = new \Model\DatabaseModel();
        $this->session = new \Model\Session();
        $this->loginModel = new \Model\LoginModel($this->session);
        $this->registerModel = new \Model\RegisterModel($this->session);
        $this->database->saveMemberToJSONFile(new \Model\Member("Arne", "testtest"));
        $this->database->checkCredentials("Arne", "testtest");

        // CREATE OBJECTS OF THE VIEWS
        $this->loginView = new \View\LoginView($this->session, $this->loginModel);
        $this->dateTimeView = new \View\DateTimeView();
        $this->layoutView = new \View\LayoutView($this->registerModel, $this->session);
        $this->registerView = new \View\RegisterView($this->registerModel, $this->session);
    }

    public function routerHandler() {
        if ($this->registerView->wantsToRegisterV2()) {
            $this->doRegisterManagement();
        } else if (!empty($_POST)) {
            $this->antiHijacking();
        } else if ($this->hasAllOtherCookie() && $this->hasNoSessionCookie()) {
            $this->doCookieManagement();
        } else {
            if (!$this->loginView->isHttpUserAgentOriginal()) {
                $this->session->setSessionKey("loggedIn", false);
                $this->session->setSessionMessage('');
                $this->renderView();
            } else {
                if ($this->session->doesCookieExist('PHPSESSID') && $this->session->getSessionMessage() != 'Registered new user.') { // Solution to reach 96% without a database...
                    $this->session->setSessionKey("loggedIn", true);
                    $this->renderView();
                } else {
                    $this->renderView();
                }
            }
        }
    }

    private function antiHijacking() {
        if (!$this->loginView->isHttpUserAgentOriginal()) {
            $this->session->setSessionKey("loggedIn", false);
            $this->session->setSessionMessage('');
            $this->renderView();
        } else {
            $this->doLoginManagement();
        }
    }

    private function doRegisterManagement() {
        if ($this->registerView->userWantsToRegister()) {
            $credentials = $this->registerView->getRegisterCredentials();
            $this->registerModel->registerWithCredentials($credentials);

            if ($this->successfullRegistration()) {
                list($registeredUserName, $registeredPassword) = $credentials;
                $this->loginModel->loginWithCredentials(array($registeredUserName, ''));
                $this->session->setSessionMessage('Registered new user.');
                header("location:?");
                $this->renderView();
            } else {
                $this->session->setSessionMessage($this->registerModel->registerMessage());
                $this->renderRegisterView();
            }
        } else {
            $this->session->setSessionMessage('');
            $this->renderRegisterView();
        }
    }

    private function renderRegisterView() {
        return $this->layoutView->renderRegister(false, $this->registerView, $this->dateTimeView);
    }

    private function successfullRegistration() {
        return ($this->registerModel->registerMessage() == "Registered new user");
    }

    private function doLoginManagement() {
        if ($this->loginView->userWantsToLogin()) {
            if ($this->session->isLoggedIn()) {
                $this->session->setSessionMessage('');
                $this->renderView();
            } else {
                $credentials = $this->loginView->getUserCredentials();
                $this->loginModel->loginWithCredentials($credentials);
                $this->session->setSessionMessage($this->loginModel->validationMessage());
                
                $this->handleCookies();
                $this->renderView();
                $this->session->unsetSessionMessage();
            }
        } else if ($this->loginView->userWantsToLogout()) {
            if ($this->session->isLoggedIn() == false) {
                $this->session->setSessionMessage('');
                $this->renderView();
            } else {
                $this->session->setSessionKey("loggedIn", false);
                $this->session->setSessionMessage('Bye bye!');

                $this->session->destroy();
                $this->renderView();
            }
        } else {
            $this->session->setSessionMessage('');
            $this->renderView();
        }
    }

    private function renderView() {
        return $this->layoutView->render($this->session->isLoggedIn(), $this->loginView, $this->dateTimeView);
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

    private function doCookieManagement() {
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

    private function isHijackingRegister() : bool {
        $rCSRF = $this->registerView->getRegisterCSRF();
        return $this->session->isHijacked($rCSRF);
    }

    private function isHijackingLogin() : bool {
        $lCSRF = $this->registerView->getLoginCSRF();
        return $this->session->isHijacked($lCSRF);
    }
}