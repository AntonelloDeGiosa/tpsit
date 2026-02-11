<?php
require 'vendor/autoload.php';

use OTPHP\TOTP;

// Connessione DB
$conn = new mysqli("db", "myuser", "mypassword", "myapp_db");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = $_POST['nome'];
    $cognome = $_POST['cognome'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $secret = $_POST['secret'];

    $stmt = $conn->prepare("INSERT INTO user (nome, cognome, username, password, totp_secret) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nome, $cognome, $username, $password, $secret);
    $stmt->execute();

    header("Location: login.php");
    exit();
}
?>
