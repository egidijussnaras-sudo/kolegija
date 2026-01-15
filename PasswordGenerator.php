<?php
class PasswordGenerator {
    public function generate($length, $useUpper, $useLower, $useNumbers, $useSpecial) {
        $sets = [];
        if ($useUpper) $sets[] = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        if ($useLower) $sets[] = 'abcdefghijkmnopqrstuvwxyz';
        if ($useNumbers) $sets[] = '23456789';
        if ($useSpecial) $sets[] = '!@#$%^&*()-_=+';

        $allCharacters = '';
        $password = '';

        // Užtikriname, kad bent vienas simbolis būtų iš kiekvienos pasirinktos grupės
        foreach ($sets as $set) {
            $password .= $set[array_rand(str_split($set))];
            $allCharacters .= $set;
        }

        // Likusius simbolius parenkame atsitiktinai
        for ($i = strlen($password); $i < $length; $i++) {
            $password .= $allCharacters[array_rand(str_split($allCharacters))];
        }

        // Sumaišome slaptažodį, kad pirmi simboliai nebūtų nuspėjami
        return str_shuffle($password);
    }
}
?>