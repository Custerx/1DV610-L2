<?php

namespace Model;

class DatabaseModel {
    private $file;
    private $createTestMember;
    private $session;

    public function __construct(\Model\Session $a_session) {
        $this->session = $a_session;
        $this->file = getenv("DOCUMENT_ROOT") . "/database.json";
        $this->createTestMember = new \Test\CreateTestMember();


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

    public function checkCredentials(string $a_username, string $a_password, string $a_HTTP_USER_AGENT) {
        
        if ($a_username == "Admin" && $a_password == "Password") { // Admin is considered an exception. Any browser can login with these credentials.
            $this->session->userLogsIn();
            return;
        }

        $member = $this->findMemberByUsername($a_username);

        if ($this->correctPassword($a_password, $member)) {
        } else {
            throw new \Exception("Wrong name or password");
        }

        if ($this->noHiJackingAttempt($a_HTTP_USER_AGENT, $member)) {
            $this->session->userLogsIn();
        } else {
            $this->session->userLogsOut();
            throw new \Exception(""); 
        }      
    }

    public function isUniqueUsername(string $a_username) : bool {
        if (file_exists($this->file)) {
            return $this->isUsernameAvailable($a_username);
        } else {
            return true;
        }
    }

    private function findMemberByUsername(string $a_username) {
        $members_ = $this->loadJSONFile();

        $findMemberByUsername = function($a_findUsername) use ($members_) {
            foreach ($members_ as $member) {
                if (password_verify($a_findUsername, $member->username)) {
                    return $member;
                }
            }
            
            throw new \Exception("Wrong name or password");
        };

        return $findMemberByUsername($a_username);
    }

    private function isUsernameAvailable(string $a_username) : bool {
        $members_ = $this->loadJSONFile();

        $findUsername = function($a_findUsername) use ($members_) {
            foreach ($members_ as $member) {
                if (password_verify($a_findUsername, $member->username)) {
                    return false;
                }
            }
            
            return true;
        };

        return $findUsername($a_username);
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
        $member["cookie"] = $a_member->getCookie();
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

    private function noHiJackingAttempt($a_HTTP_USER_AGENT, $member) : bool {
        return password_verify($a_HTTP_USER_AGENT, $member->HTTP_USER_AGENT);
    }

    private function correctPassword($a_password, $member) : bool {
        return password_verify($a_password, $member->password);
    }

    private function ifFileDoNotExist() : bool {
        return !file_exists($this->file);
    }

    private function createAdminMember() {
        $this->saveMemberToJSONFile(new \Model\Member("Admin", "Password", "Password", "fakeUserAgent", "fakeCookie"));
    }
}