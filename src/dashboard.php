<?php
session_start();
if(!isset($_SESSION['id_sessione']) || $_SESSION['ruolo'] != 0){
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca</title>
    <link rel="stylesheet" href="sito.css">
</head>
<body>
    <h1>Biblioteca</h1>


<div class="container">
    <div class="menu">
        <a href="libri.php">Libri Disponibili</a>
        <a href="prestiti_utente.php">Prestiti attivi</a>
       
    </div>
</div>

<div class="logout-wrapper">
    <a class="logout-link" href="logout.php">Logout</a>
</div>  
    
</body>
</html>