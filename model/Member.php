<?php

namespace Model;

class Member {
    private static $userName;
    private static $passWord;
    private static $cookie;
    private static $HTTP_USER_AGENT;

    public function __construct(string $a_username, string $a_password) {
        self::$userName = $this->encrypt($a_username);
        self::$passWord = $this->encrypt($a_password);
    }

    public function getUsername() {
		  return self::$userName;
    }
    
    public function getPassword() {
		  return self::$passWord;
    }
    
    public function getCookie() {
		  return self::$cookie;
    }

    public function getHTTP_USER_AGENT() {
		  return self::$HTTP_USER_AGENT;
    }
    
    public function setCookie($a_cookie) {
		  self::$cookie = $this->encrypt($a_cookie);
    }

    public function setHTTP_USER_AGENT($a_HTTP_USER_AGENT) {
      self::$HTTP_USER_AGENT = $this->encrypt($a_HTTP_USER_AGENT);
    }
    
    private function encrypt($a_toBeEncrypted) {
      return password_hash($a_toBeEncrypted, PASSWORD_BCRYPT);
    }
}
