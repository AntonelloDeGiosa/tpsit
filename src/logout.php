<?php
date_default_timezone_set('Europe/Rome');
session_start();

// Connessione e update logout (se serve)
if (isset($_SESSION['id_sessione'])) {

    $conn = new mysqli("db", "myuser", "mypassword", "myapp_db");

    if (!$conn->connect_error) {
        $id_sessione = $_SESSION['id_sessione'];
        $logout_time = date("Y-m-d H:i:s");

        $stmt = $conn->prepare("UPDATE sessioni SET logout = ? WHERE id_sessione = ?");
        $stmt->bind_param("ss", $logout_time, $id_sessione);
        $stmt->execute();
        $stmt->close();
        $conn->close();
    }
}

// 1. Svuota variabili sessione
$_SESSION = [];

// 2. Elimina cookie PHPSESSID
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),   // PHPSESSID
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// 3. Distrugge sessione lato server
session_destroy();

// Redirect
header("Location: login.php");
exit();
?>
