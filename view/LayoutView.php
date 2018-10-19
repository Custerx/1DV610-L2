<?php

namespace View;

class LayoutView { 
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

              ' . $this->renderAccountView($isLoggedIn, $a) . '
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
          ' . $this->renderIsLoggedInForRegister($isLoggedIn) . '
          
          <div class="container">
              ' . $v->response() . '
              
              ' . $dtv->show() . '
          </div>
         </body>
      </html>
    ';
  }
  
  private function renderIsLoggedInForLogin($isLoggedIn) {
    if ($isLoggedIn) {
      return '<h2>Logged in</h2>';
    }
    else {
      return '
      <a href="?register">Register a new user</a>
      <h2>Not logged in</h2>
      ';
    }
  }

  private function renderIsLoggedInForRegister($isLoggedIn) {
    if ($isLoggedIn) {
      return '<h2>Logged in</h2>';
    }
    else {
      return '
      <a href="?">Back to login</a>
      <h2>Not logged in</h2>
      ';
    }
  }

  private function renderAccountView($isLoggedIn, AccountView $a) {
    if ($isLoggedIn) {
      return $a->response();
    }
  }
}
