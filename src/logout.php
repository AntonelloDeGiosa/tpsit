<?php
date_default_timezone_set('Europe/Rome');
session_start();


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


$_SESSION = [];

//Elimina cookie PHPSESSID
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),   
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

//Distruggo sessione lato server
session_destroy();
header("Location: login.php");
exit();
?>
