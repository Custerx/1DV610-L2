<?php

namespace Controller;

class MasterController {
    private $loginView;
    private $registerView;
    private $dateTimeView;
    private $layoutView;
    private $session;
    private $database;
    private $env;

	public function __construct() {
        $this->env = new \Env\Environment();
        $this->session = new \Model\Session();
        $this->database = new \Model\DatabaseModel($this->session, $this->env);
        $this->loginView = new \View\LoginView($this->session, $this->database);
        $this->registerView = new \View\RegisterView($this->session, $this->database);
        $this->dateTimeView = new \View\DateTimeView();
        $this->layoutView = new \View\LayoutView();
        $this->accountView = new \View\AccountView($this->session, $this->database);
    }

    public function routerHandler() {
        if ($this->accountView->userWantsToConfirmAccountDetails() && $this->session->isLoggedIn())
            $this->doEditManagement();
        else if ($this->accountView->userWantsToEditAccountDetails() && $this->session->isLoggedIn()) {
            $this->session->startEditMode();
            $this->renderView();
        } else if ($this->registerView->userWantsToViewRegisterPage()) {
            $this->doRegisterManagement();
        } else if (!empty($_POST)) {
            $this->doLoginManagement();
        } else if ($this->loginView->showRegisteredUserTheLoginPage()) { 
            $this->loginView->successfullRegisterView();
            $this->renderView();
        } else if ($this->loginView->hasCookiesForUsernameAndPassword() && $this->loginView->hasNoSessionCookie()) {
            $this->doCookieManagement();
        } else {
            $this->renderView();
        }
    }

    private function doEditManagement() {
        if ($this->accountView->successfullEdit()) {
            $this->session->stopEditMode();
            $this->accountView->showNewUsername();
            $this->accountView->successfullEditMessage();
            $this->renderView();
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

            if ($this->loginView->authorizedUserWantsToStayLoggedIn()){
                $this->loginView->sendCookieToUser();
                $this->loginView->setCookieMessage();
            }
            $this->renderView();
        }
    }

    private function logout() {
        if ($this->session->isLoggedIn() == false) {
            $this->session->destroy();
            $this->loginView->resetMessage();
            $this->renderView();
        } else {
            $this->loginView->logoutUser();
            $this->renderView();
        }
    }

    private function renderView() {
        return $this->layoutView->render($this->session->isLoggedIn(), $this->loginView, $this->dateTimeView, $this->accountView);
    }

    private function doCookieManagement() {
        $this->loginView->loginUserWithCookie();
        $this->loginView->sendCookieToUser();
        $this->renderView();
    }
}