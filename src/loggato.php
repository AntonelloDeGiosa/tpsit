<?php
require 'vendor/autoload.php';

use OTPHP\TOTP;


 
        $conn = new mysqli("db", "myuser", "mypassword", "myapp_db");

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
            if (password_verify($password, $user['password'] || $user['password'] == null || $user['password'] == '')) {

                // Ricrea TOTP con la secret salvata
                $totp = TOTP::create($user['totp_secret']);

                // Verifica codice
                if ($totp->verify($codice)) {
                    echo "Login riuscito!";
                } else {
                    echo "Codice TOTP errato.";
                    echo '<br><a href="login.php">Torna al login</a>';
                }

            } else {
                echo "Password errata.";
                echo '<br><a href="login.php">Torna al login</a>';
            }

        } else {
            echo "Utente non trovato.";
            echo '<br><a href="login.php">Torna al login</a>';
        }


?>  