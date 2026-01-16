<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'Database.php';
require_once 'User.php';
require_once 'PasswordGenerator.php';
require_once 'Cipher.php';

$database = new Database();
$db = $database->connect();
$gen = new PasswordGenerator();
$cipher = new Cipher();

$generatedPassword = "";
$message = "";

// 1. Slaptažodžio generavimo logika pagal nustatytus GUI parametrus
if (isset($_POST['generate'])) {
    $length = $_POST['length'] ?? 12;
    $upper = isset($_POST['upper']);
    $lower = isset($_POST['lower']);
    $nums = isset($_POST['nums']);
    $spec = isset($_POST['spec']);

    $generatedPassword = $gen->generate($length, $upper, $lower, $nums, $spec);
}

// 2. Slaptažodžio saugojimo logika (Užduoties esmė)
if (isset($_POST['save'])) {
    $service = $_POST['service_name'];
    $passToSave = $_POST['pass_to_save'];
    $userPlainPass = $_SESSION['user_plain_password']; // Paimta iš sesijos

    // Gauname vartotojo užkoduotą RAKTĄ
    $stmt = $db->prepare("SELECT encrypted_key FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $userData = $stmt->fetch();

    // Atkoduojame RAKTĄ su vartotojo PLAIN slaptažodžiu
    $masterKey = $cipher->decrypt($userData['encrypted_key'], $userPlainPass);

    // Užkoduojame paslaugos slaptažodį su atkoduotu RAKTU
    $encryptedPass = $cipher->encrypt($passToSave, $masterKey);

    // Įrašome į 'passwords' lentelę
    $query = "INSERT INTO passwords (user_id, service_name, encrypted_password) VALUES (?, ?, ?)";
    $stmt = $db->prepare($query);
    
    if ($stmt->execute([$_SESSION['user_id'], $service, $encryptedPass])) {
        $message = "Slaptažodis sėkmingai užkoduotas ir išsaugotas!";
    }
}
?>

<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <title>Valdymo skydas</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; padding: 20px; }
        .box { background: white; padding: 20px; border-radius: 8px; max-width: 500px; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .field { margin-bottom: 15px; }
        input[type="text"], input[type="number"] { width: 100%; padding: 8px; }
        button { padding: 10px; background: #28a745; color: white; border: none; cursor: pointer; width: 100%; }
        .result { background: #e9ecef; padding: 10px; margin: 10px 0; font-weight: bold; border: 1px dashed #333; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Sveiki, <?php echo $_SESSION['username']; ?>!</h2>
        <p><a href="logout.php">Atsijungti</a></p>

        <form method="POST">
            <h3>1. Generuoti naują slaptažodį</h3>
            <div class="field">
                Ilgis: <input type="number" name="length" value="12" min="4" max="32">
            </div>
            <div class="field">
                <input type="checkbox" name="upper" checked> Didžiosios (A-Z)<br>
                <input type="checkbox" name="lower" checked> Mažosios (a-z)<br>
                <input type="checkbox" name="nums" checked> Skaičiai (0-9)<br>
                <input type="checkbox" name="spec" checked> Spec. simboliai (!@#)
            </div>
            <button type="submit" name="generate">Generuoti</button>
        </form>

        <?php if ($generatedPassword): ?>
            <div class="result">Sugeneruotas: <?php echo $generatedPassword; ?></div>
            
            <form method="POST">
                <h3>2. Išsaugoti slaptažodį</h3>
                <input type="hidden" name="pass_to_save" value="<?php echo $generatedPassword; ?>">
                <div class="field">
                    Svetainės pavadinimas: <input type="text" name="service_name" placeholder="Pvz. Facebook" required>
                </div>
                <button type="submit" name="save" style="background: #007bff;">Užkoduoti ir saugoti DB</button>
            </form>
        <?php endif; ?>

        <?php if ($message) echo "<p style='color: green;'>$message</p>"; ?>
    </div>
</body>
</html>