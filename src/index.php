<?php
require 'vendor/autoload.php';

use OTPHP\TOTP;

// Crea una nuova chiave segreta per l'utente
$totp = TOTP::create();
$secret = $totp->getSecret();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrati</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <h1>Registrati</h1>

    <div class="container">
        
        <div class="col istruzioni">
            <h3>Istruzioni per la registrazione</h3>
            <p>
                Inserisci correttamente tutti i campi obbligatori.<br><br>
                La chiave TOTP verrà usata per proteggere il tuo account,<br>
                copiala e inseriscila in un'app di autenticazione (es. Google Authenticator o quella offerta da noi) per generare i codici di accesso.<br>
                Al termine della registrazione dovrai accedere al tuo accountinserendo username, password e il codice TOTP generato dall'app.<br><br>
                Dopo la registrazione potrai usare il tuo account per accedere in sicurezza.
            </p>
        </div>

        
        <div class="col form-container">
            <form action="register.php" method="post">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" required>

                <label for="cognome">Cognome:</label>
                <input type="text" id="cognome" name="cognome" required>

                <label for="email">Email:</label>
                <input type="text" id="email" name="email" required>

                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>

                <label for="codice">Chiave TOTP (conservala):</label>
                <input type="text" id="codice" name="secret" value="<?php echo htmlspecialchars($secret); ?>" readonly>

                <input type="submit" value="Registrati">
            </form>

            <h2>Se disponi di un account, <a href="login.php">Accedi</a></h2>
        </div>
    </div>
</body>
</html>
