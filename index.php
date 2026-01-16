<?php
require_once 'Database.php';
require_once 'User.php';

$message = "";

// Tikriname, ar forma buvo išsiųsta
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $database = new Database();
    $db = $database->connect();

    if ($db) {
        $user = new User($db);
        $username = $_POST['username'];
        $password = $_POST['password'];

        if ($user->register($username, $password)) {
            $message = "Vartotojas sėkmingai sukurtas! Patikrinkite MySQL 'users' lentelę.";
        } else {
            $message = "Klaida registruojant vartotoją.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <title>Slaptažodžių Valdyklė</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 50px; }
        .container { background: white; padding: 20px; border-radius: 8px; max-width: 400px; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        input { width: 100%; padding: 10px; margin: 10px 0; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #28a745; color: white; border: none; cursor: pointer; }
        .message { color: green; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <h2>Registracija</h2>
    <?php if($message) echo "<p class='message'>$message</p>"; ?>
    <form method="POST">
        <input type="text" name="username" placeholder="Vartotojo vardas" required>
        <input type="password" name="password" placeholder="Puslapio slaptažodis" required>
        <button type="submit" name="register">Užsiregistruoti</button>
    </form>
</div>

</body>
</html>