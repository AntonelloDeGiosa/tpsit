<?php
session_start();

if (isset($_SESSION['id_sessione'])) {

    $conn = new mysqli("db", "myuser", "mypassword", "myapp_db");

    $id_sessione = $_SESSION['id_sessione'];
    $logout_time = date("Y-m-d H:i:s");

    $stmt = $conn->prepare("UPDATE sessioni SET logout = ? WHERE id_sessione = ?");
    $stmt->bind_param("ss", $logout_time, $id_sessione);
    $stmt->execute();
}

session_destroy();

header("Location: login.php");
exit();
