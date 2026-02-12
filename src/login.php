<?php
require 'vendor/autoload.php';
use OTPHP\TOTP;



// Connessione DB
$conn = new mysqli("db", "myuser", "mypassword", "myapp_db");

$errore = "";

// Esegui login solo se il form è stato inviato
if ($_SERVER["REQUEST_METHOD"] == "POST") {

   $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
     $codice = isset($_POST['totp']) ? $_POST['totp'] : '';

    // Cerca utente
    $stmt = $conn->prepare("SELECT id,ruolo,password, totp_secret FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $user = $result->fetch_assoc();

        // Verifica password
        if (password_verify($password, $user['password'])) {

            // Verifica TOTP
            $totp = TOTP::create($user['totp_secret']);

            if ($totp->verify($codice)) {
                session_start();

                    $id_utente = $user['id'];
                    $ruolo = $user['ruolo'];

                    // crea id sessione
                    $id_sessione = bin2hex(random_bytes(32));
                    $login_time = date("Y-m-d H:i:s");
                    $scadenza = date("Y-m-d H:i:s", time() + 1800);

                    // salva sessione DB
                    $stmt2 = $conn->prepare("INSERT INTO sessioni (id_sessione, id_utente, login, scadenza) VALUES (?, ?, ?, ?)");
                    $stmt2->bind_param("siss", $id_sessione, $id_utente, $login_time, $scadenza);
                    $stmt2->execute();

                    // sessione PHP
                    $_SESSION['id_sessione'] = $id_sessione;
                    $_SESSION['id_utente'] = $id_utente;
                    $_SESSION['username'] = $username;
                    $_SESSION['ruolo'] = $ruolo;

                    // redirect in base al ruolo
                    if ($ruolo == 0) {
                        header("Location: dashboard.php");
                    } else {
                        header("Location: amministrazione.php");
                    }
                    exit();


                

                
                
            
            } else {
                $errore = "Codice TOTP errato.";
            }

        } else {
            $errore = "Password errata.";
        }

    } else {
        $errore = "Utente non trovato.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accedi</title>
     <?php if (!empty($errore)) : ?>
        <p style="color:red; font-weight:bold;">
            <?php echo $errore; ?>
        </p>
    <?php endif; ?>
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

       

