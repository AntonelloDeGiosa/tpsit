<?php
session_start();

if (!isset($_SESSION['id_sessione']) || $_SESSION['ruolo'] != 0) {
    header("Location: login.php");
    exit();
}
?>

<h1>Area Utente</h1>
<p>Benvenuto <?php echo $_SESSION['username']; ?></p>
<a href="logout.php">Logout</a>