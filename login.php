<?php
session_start();
require_once 'Database.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $database = new Database();
    $db = $database->connect();

    $username = $_POST['username'];
    $password = $_POST['password']; // Vartotojo įvestas PLAIN slaptažodis

    $query = "SELECT id, password_hash FROM users WHERE username = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        // IŠSAUGOME SESIJOJE:
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $username;
        $_SESSION['user_plain_password'] = $password; // Būtina RAKTO atkodavimui!

        header("Location: dashboard.php"); // Nukreipiame į valdymo langą
        exit();
    } else {
        $message = "Neteisingas prisijungimo vardas arba slaptažodis.";
    }
}
?>

<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <title>Prisijungimas</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .login-box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 300px; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .error { color: red; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Prisijungti</h2>
        <?php if($message) echo "<p class='error'>$message</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Vartotojo vardas" required>
            <input type="password" name="password" placeholder="Slaptažodis" required>
            <button type="submit" name="login">Prisijungti</button>
        </form>
        <p><a href="index.php">Registruotis</a></p>
    </div>
</body>
</html>