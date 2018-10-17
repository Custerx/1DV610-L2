<?php

namespace Test;

class CreateTestMember {
    private static $passWord = "Password";

    public function randomMember() {
        return new \Model\Member($this->randomUsername(), self::$passWord);
    }

    private function randomUsername() : string {
        $surnames = array(
            'alker',
            'hompson',
            'nderson',
            'ohnson',
            'remblay',
            'eltier',
            'unningham',
            'impson',
            'ercado',
            'ellers',
            'hristopher',
            'yan',
            'than',
            'ohn',
            'oey',
            'arah',
            'ichelle',
            'amantha'
        );

        $firstLetter = array(
            'W', 'T', 'A', 'J', 'T', 'P', 'C', 'S', 'M', 'R', 'O', 'I', 'E', 'J', 'Z', 'L', 'X', 'V'
        );

        return $firstLetter[mt_rand(0, sizeof($firstLetter) - 1)] . '' . $surnames[mt_rand(0, sizeof($surnames) - 1)];
    }
}