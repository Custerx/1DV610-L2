<?php

namespace Model;

class DatabaseModel {
    private $file;
    private $createTestMember;

    public function __construct() {
        $this->file = getenv("DOCUMENT_ROOT") . "/database.json";
        $this->createTestMember = new \Test\CreateTestMember();

        if ($this->ifFileDoNotExist()) {
            $this->create20TestMembers();
        }
    }

    public function checkCredentials(string $a_username, string $a_password) {
        try {
            $members_ = $this->loadJSONFile();
   
            $findPasswordByUsername = function($a_findUsername) use ($members_) {
                foreach ($members_ as $member) {
                    if (password_verify($a_findUsername, $member->username)) return $member->password;
                 }
            
                 throw new \Exception("Username not found in the database.");
            };

            $password = $findPasswordByUsername($a_username);

            if (password_verify($a_password, $password)) {
                echo "korrekt lösenord";
            } else {
                throw new \Exception("Wrong username or password.");
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function check_Cookie_HTTP_USER_AGENT($a_cookie, $a_HTTP_USER_AGENT) {
        try {
            $members_ = $this->loadJSONFile();

            $find_HTTP_USER_AGENT_By_Cookie = function($a_findCookie) use ($members_) {
                foreach ($members_ as $member) {
                    if (password_verify($a_findCookie, $member->cookie)) return $member->HTTP_USER_AGENT;
                 }
            
                 throw new \Exception("Cookie not found in the database.");
            };

            $HTTP_USER_AGENT = $find_HTTP_USER_AGENT_By_Cookie($a_cookie);

            if (password_verify($a_HTTP_USER_AGENT, $HTTP_USER_AGENT)) {
                echo "korrekt browser";
            } else {
                throw new \Exception("Hijacking attempt.");
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }   

    public function loadJSONFile() {
        if (file_exists($this->file)) {
            return json_decode(file_get_contents($this->file));
        } else {
            throw new \Exception("No file exist.");
        }
    }

    public function saveMemberToJSONFile(\Model\Member $a_member) {
        if (file_exists($this->file)) {
            $members_ = json_decode(file_get_contents($this->file), true); // Default returns a class, when set to true returns an array.

            $member["username"] = $a_member->getUsername();
            $member["password"] = $a_member->getPassword();
            $member["cookie"] = $a_member->getCookie();
            $member["HTTP_USER_AGENT"] = $a_member->getHTTP_USER_AGENT();

            array_push($members_, $member);
            
            file_put_contents($this->file, json_encode($members_));
        } else {
            $members_ = []; // Creates the array containing all members.
            
            $member["username"] = $a_member->getUsername();
            $member["password"] = $a_member->getPassword();
            $member["cookie"] = $a_member->getCookie();
            $member["HTTP_USER_AGENT"] = $a_member->getHTTP_USER_AGENT();
            
            array_push($members_, $member);
            
            file_put_contents($this->file, json_encode($members_));
        }
    }

    private function create20TestMembers() {
        $amountMembers = 20;
        
        for ($i = 0; $i < $amountMembers; $i++)
        {
            $this->saveMemberToJSONFile($this->createTestMember->randomMember());
        }
    }

    private function ifFileDoNotExist() : bool {
        return !file_exists($this->file);
    }
}