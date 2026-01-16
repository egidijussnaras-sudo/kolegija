<?php
require_once 'Cipher.php';
require_once 'PasswordGenerator.php';

class User {
    private $conn;
    private $table = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register($username, $plainPassword) {
        $passwordHash = password_hash($plainPassword, PASSWORD_BCRYPT);
        
        $gen = new PasswordGenerator();
        $masterKey = $gen->generate(32, true, true, true, true);
        
        $cipher = new Cipher();
        $encryptedKey = $cipher->encrypt($masterKey, $plainPassword);

        $query = "INSERT INTO " . $this->table . " (username, password_hash, encrypted_key) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$username, $passwordHash, $encryptedKey]);
    }
}
?>