<?php

namespace Model;

class DatabaseModel {
    private $file;
    private $createTestMember;
    private $session;
    private $env;
    private $SALT_STRING;

    public function __construct(\Model\Session $a_session, \Env\Environment $a_env) {
        $this->session = $a_session;
        $this->createTestMember = new \Test\CreateTestMember();
        $this->env = $a_env;
        $this->SALT_STRING = $this->env->getSaltString();
        $this->file = $this->env->getFileLocation();

        if ($this->ifFileDoNotExist()) {
            try {
                $this->createAdminMember();
                $this->create20TestMembers();
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }
    }

    public function saveMemberToJSONFile(\Model\Member $a_member) {
        if (file_exists($this->file)) {
            $members_ = $this->loadJSONFileAsArray();
            $member = $this->createMember($a_member);

            array_push($members_, $member);
            
            $this->saveToJSONFile($members_);
        } else {
            $members_ = []; // Creates the array containing all members.
            $member = $this->createMember($a_member);
            
            array_push($members_, $member);
            
            $this->saveToJSONFile($members_);
        }
    }

    public function checkCredentials(string $a_username, string $a_password, string $a_HTTP_USER_AGENT, 
        bool $a_loginWithCookie = false, string $a_cookiePassword = "placeholder") {
        
        $member = $this->findMemberByUsername($a_username, $a_loginWithCookie);

        if ($this->correctPassword($a_password, $member)) {
        } else {
            $this->throwExceptions($a_loginWithCookie);
        }

        if ($this->noHiJackingAttempt($a_HTTP_USER_AGENT, $member)) {
            $this->session->userLogsIn();
        } else {
            $this->session->userLogsOut();
            throw new \Exception(""); 
        }      
    }

    public function isUniqueUsername($a_username) : bool {
        if ($a_username == null) {
            return true;
        } else if (file_exists($this->file)) {
            return $this->isUsernameAvailable($a_username);
        } else {
            return true;
        }
    }

    // TODO : Deletemember function is not working atm.
    public function deleteMember($a_username) {
        $members_ = $this->loadJSONFileAsArray();

        foreach($members_ as $member) { 
            if (hash_equals($member["username"], $a_username)) {      
                unset($members_[$member["username"]]); //Delete member from Array
                unset($members_[$member["password"]]);
                unset($members_[$member["cookiePassword"]]);
                unset($members_[$member["HTTP_USER_AGENT"]]);

                $members_ = array_values($members_); // Index kept intact with unset. array_values force re-indexed seq.
            }
        }

        $this->saveToJSONFile($members_);
    }

    public function encryptWithCrypt($a_toBeEncrypted) {
        return crypt($a_toBeEncrypted, $this->SALT_STRING);
    }

    // https://stackoverflow.com/questions/33134021/generate-a-random-password-in-php
    public function generateRandomPassword($length = 8) {
        $string = "";
        $chars = "abcdefghijklmanopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $size = strlen($chars);
        for ($i = 0; $i < $length; $i++) {
            $string .= $chars[rand(0, $size - 1)];
        }
        return $string; 
    }

    private function findMemberByUsername(string $a_username, bool $a_loginWithCookie = false) {
        $members_ = $this->loadJSONFile();

        $findMemberByUsername = function($a_findUsername) use ($members_) {
            foreach ($members_ as $member) {
                if (hash_equals($member->username, $a_findUsername)) {
                    return $member;
                }
            }
            
            $this->throwExceptions($a_loginWithCookie = false);
        };

        return $findMemberByUsername($a_username);
    }

    private function isUsernameAvailable(string $a_username) : bool {
        $members_ = $this->loadJSONFile();
        $usernamedHashed = $this->encryptWithCrypt($a_username);

        $findUsername = function($a_findUsername) use ($members_) {
            foreach ($members_ as $member) {
                if (hash_equals($member->username, $a_findUsername)) {
                    return false;
                }
            }
            
            return true;
        };

        return $findUsername($usernamedHashed);
    }

    private function loadJSONFile() {
        if (file_exists($this->file)) {
            return json_decode(file_get_contents($this->file));
        } else {
            throw new \Exception("No file exist.");
        }
    }

    private function saveToJSONFile($data) {
        file_put_contents($this->file, json_encode($data));
    }

    private function loadJSONFileAsArray() {
        if (file_exists($this->file)) {
            return json_decode(file_get_contents($this->file), true);
        } else {
            throw new \Exception("No file exist.");
        }
    }

    private function createMember(\Model\Member $a_member) {
        $member["username"] = $a_member->getUsername();
        $member["password"] = $a_member->getPassword();
        $member["cookiePassword"] = $a_member->getCookiePassword();
        $member["HTTP_USER_AGENT"] = $a_member->getHTTP_USER_AGENT();
        return $member;
    }

    private function create20TestMembers() {
        $amountMembers = 20;
        
        for ($i = 0; $i < $amountMembers; $i++)
        {
            $this->saveMemberToJSONFile($this->createTestMember->randomMember());
        }
    }

    private function throwExceptions(bool $a_loginWithCookie = false) {
        if($a_loginWithCookie) {
            throw new \Exception("Wrong information in cookies");
        } else {
            throw new \Exception("Wrong name or password");
        }
    }

    private function noHiJackingAttempt($a_HTTP_USER_AGENT, $member) : bool {
        return hash_equals($member->HTTP_USER_AGENT, $a_HTTP_USER_AGENT);
    }

    private function correctPassword($a_password, $member) : bool {
        return hash_equals($member->password, $a_password);
    }
    // TOD : Edit cookiepassword in database.
    private function correctCookiePassword($a_Cookiepassword, $member) : bool {
        return hash_equals($member->CookiePassword, $a_Cookiepassword);
    }

    private function ifFileDoNotExist() : bool {
        return !file_exists($this->file);
    }

    private function createAdminMember() { // Just for testing, not placing in Environment.php
        $this->saveMemberToJSONFile(new \Model\Member("Admin", "Password", "Password", $this->getNoSpacedHTTP_USER_AGENT(), "fatCookie"));
    }

    private function getNoSpacedHTTP_USER_AGENT() {
		$HTTP_USER_AGENT = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13";
        $HTTP_USER_AGENT = str_replace(' ', '', $HTTP_USER_AGENT);
        // Crypt hash_equals() is limited when comparing long strings.
        // TODO : Change encryption system when comparing user agents and passwords.
        $HTTP_USER_AGENT = substr($HTTP_USER_AGENT, 21, 33);
        return $HTTP_USER_AGENT;
    }
}