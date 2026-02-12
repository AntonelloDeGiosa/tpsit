<?php
session_start();

if (!isset($_SESSION['id_sessione']) || $_SESSION['ruolo'] != 1) {
    header("Location: login.php");
    exit();
}
?>

<h1>Area Amministratore</h1>
<p>Benvenuto Admin <?php echo $_SESSION['username']; ?></p>
<a href="logout.php">Logout</a>