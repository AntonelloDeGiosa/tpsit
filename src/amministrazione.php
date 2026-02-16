<?php
session_start();
if(!isset($_SESSION['id_sessione']) || $_SESSION['ruolo'] != 1){
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Area Amministrazione</title>
    <link rel="stylesheet" href="sito.css">
</head>
<body>
    <h1>Area Amministrazione</h1>


<div class="container">
    <div class="menu">
        <a href="libri.php">Gestione Libri</a>
        <a href="copie.php">Gestione Copie</a>
        <a href="prestiti.php">Prestiti</a>
        <a href="bibliotecari.php">Bibliotecari</a>
    </div>
</div>

<div class="logout-wrapper">
    <a class="logout-link" href="logout.php">Esci dall'Amministrazione</a>
</div>  
    
</body>
</html>




