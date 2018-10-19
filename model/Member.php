<?php

namespace Model;

class Member {
    private static $userName;
    private static $passWord;
    private static $cookiePassword;
    private static $HTTP_USER_AGENT;
    
    private $registerModel;
    private $env;
    private $SALT_STRING;

    public function __construct($a_username, $a_password, $a_repeatPassword, $a_HTTP_USER_AGENT, $a_cookiePassword) {
      $this->registerModel = new \Model\RegisterModel($a_username, $a_password, $a_repeatPassword);
      $this->env = new \Env\Environment();
      $this->$SALT_STRING = $this->env->getSaltString();

      self::$userName = $this->encryptWithCrypt($a_username);
      self::$passWord = $this->encryptWithCrypt($a_password);
      self::$HTTP_USER_AGENT = $this->encryptWithCrypt($a_HTTP_USER_AGENT);
      self::$cookiePassword = $this->encryptWithCrypt($a_cookiePassword);
    }

    public function getUsername() {
		  return self::$userName;
    }
    
    public function getPassword() {
		  return self::$passWord;
    }
    
    public function getCookiePassword() {
		  return self::$cookiePassword;
    }

    public function getHTTP_USER_AGENT() {
		  return self::$HTTP_USER_AGENT;
    }

    public function setCookiePassword($a_cookiePassword) {
		  self::$cookiePassword = $this->encrypt($a_cookiePassword);
    }

    public function setHTTP_USER_AGENT($a_HTTP_USER_AGENT) {
      self::$HTTP_USER_AGENT = $this->encrypt($a_HTTP_USER_AGENT);
    }

    private function encryptWithCrypt($a_toBeEncrypted) {
      return crypt($a_toBeEncrypted, $this->SALT_STRING);
    }
}
