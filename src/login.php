<?php
require 'vendor/autoload.php';

use OTPHP\TOTP;


 // Connessione DB
 
        $conn = new mysqli("db", "myuser", "mypassword", "myapp_db");
        if($_SERVER["REQUEST_METHOD"] == "POST") {
    

        $username = isset($_POST['username']) ? $_POST['username'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $codice   = isset($_POST['totp']) ? $_POST['totp'] : '';

    


        // Cerca utente
        $stmt = $conn->prepare("SELECT password, totp_secret FROM user WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        

        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();

            // Verifica password
            if (password_verify($password, $user['password'])) {

                // Ricrea TOTP con la secret salvata
                $totp = TOTP::create($user['totp_secret']);

                // Verifica codice
                if ($totp->verify($codice)) {
                    echo "Login riuscito!";
                    // session_start(); ecc.
                } else {
                    echo "Codice TOTP errato.";
                    echo '<br><a href="login.html">Torna al login</a>';
                }

            } else {
                echo "Password errata.";
                echo '<br><a href="login.html">Torna al login</a>';
            }

        } else {
            echo "Utente non trovato.";
            echo '<br><a href="login.html">Torna al login</a>';
        }
    
        }

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accedi</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <h1 class="accedi">Accedi</h1>
    <form action="login.php" method="post">
        <label for="username">Username:</label>
        <input type="text" id="user" name="username" required><br><br>
        <label for="password">Password:</label>
        <input type="password" id="pass" name="password" required><br><br>
        <label for="totp">Codice TOTP:</label>
        <input type="text" id="totp" name="totp" required><br><br>  
        <h3>Genera codice TOTP:</h3>
         <p>Se vuoi generare il tuo codice TOTP, visita <a href="http://localhost:8082" target="_blank">2FAuth App</a>.</p>
        <input type="submit" value="Login">
    </form>

    
    <h2>Se Non disponi di un account, <a href="index.php">Registrati</a></h2>
    
</body>
</html>

       

