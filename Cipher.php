<?php
class Cipher {
    private $method = "AES-256-CBC";

    // Šifruoja duomenis (pvz., RAKTĄ arba slaptažodžius)
    public function encrypt($data, $password) {
        // Sugeneruojame saugų atsitiktinį IV (Initialization Vector)
        $iv_length = openssl_cipher_iv_length($this->method);
        $iv = openssl_random_pseudo_bytes($iv_length);
        
        // Užšifruojame
        $encrypted = openssl_encrypt($data, $this->method, $password, 0, $iv);
        
        // Grąžiname užšifruotą tekstą kartu su IV (atskirtu simboliais ::)
        // IV reikalingas tam, kad vėliau būtų galima atkoduoti
        return base64_encode($encrypted . "::" . $iv);
    }

    // Atkoduoja duomenis
    public function decrypt($data, $password) {
        list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
        return openssl_decrypt($encrypted_data, $this->method, $password, 0, $iv);
    }
}
?>