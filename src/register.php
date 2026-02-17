<?php
require 'vendor/autoload.php';

use OTPHP\TOTP;


$conn = new mysqli("db", "myuser", "mypassword", "myapp_db");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = $_POST['nome'];
    $cognome = $_POST['cognome'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $secret = $_POST['secret'];
    $email = $_POST['email'];
    $ruolo = 0;
    $data_registrazione = date("Y-m-d H:i:s");

    $stmt = $conn->prepare("INSERT INTO user (nome, cognome, username, password, totp_secret, email, ruolo, registrazione) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssis", $nome, $cognome, $username, $password, $secret, $email, $ruolo, $data_registrazione);
    $stmt->execute();
    header("Location: login.php");
    exit();
}
?>
