<?php

namespace View;

class LayoutView {
  private $registerModel;
  private $session;

	public function __construct(\Model\RegisterModel $registerModel, \Model\Session $session) {
    $this->registerModel = $registerModel;
    $this->session = $session;
	}
  
  public function render($isLoggedIn, LoginView $v, DateTimeView $dtv) {
    echo '<!DOCTYPE html>
      <html>
        <head>
          <meta charset="utf-8">
          <title>Login Example</title>
        </head>
        <body>
          <h1>Assignment 2</h1>
          ' . $this->renderIsLoggedIn($isLoggedIn) . '
          
          <div class="container">
              ' . $v->response() . '
              
              ' . $dtv->show() . '
          </div>
         </body>
      </html>
    ';
  }

  public function renderRegister($isLoggedIn, RegisterView $v, DateTimeView $dtv) {
    echo '<!DOCTYPE html>
      <html>
        <head>
          <meta charset="utf-8">
          <title>Login Example</title>
        </head>
        <body>
          <h1>Assignment 2</h1>
          ' . $this->renderIsLoggedIn($isLoggedIn) . '
          
          <div class="container">
              ' . $v->response() . '
              
              ' . $dtv->show() . '
          </div>
         </body>
      </html>
    ';
  }
  
  private function renderIsLoggedIn($isLoggedIn) {
    if ($isLoggedIn) {
      return '<h2>Logged in</h2>';
    }
    else {
      return '
      ' . $this->renderLink() . '
      <h2>Not logged in</h2>
      ';
    }
  }

  private function renderLink() {
    $registerView = new \View\RegisterView($this->registerModel, $this->session);
    if ($registerView->wantsToRegisterV2()) {
        $link = '<a href="?">Back to login</a>';
    } else {
        $link = '<a href="?register">Register a new user</a>';
    }
    return $link;
  }
}
