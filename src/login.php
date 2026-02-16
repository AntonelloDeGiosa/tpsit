<?php
require 'vendor/autoload.php';
use OTPHP\TOTP;

date_default_timezone_set('Europe/Rome');
session_start();

// Connessione DB
$conn = new mysqli("db", "myuser", "mypassword", "myapp_db");

$errore = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

   $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : ''; 
    $codice = isset($_POST['totp']) ? $_POST['totp'] : '';

    $stmt = $conn->prepare("SELECT id, ruolo, password, totp_secret FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {

            $totp = TOTP::create($user['totp_secret']);

            if ($totp->verify($codice)) {

                // Sicurezza: nuovo ID sessione
                session_regenerate_id(true);

                $id_utente = $user['id'];
                $ruolo = $user['ruolo'];

                // ID sessione applicativa (DB)
                $id_sessione = bin2hex(random_bytes(32));
                $login_time = date("Y-m-d H:i:s");
                $scadenza = date("Y-m-d H:i:s", time() + 1800);

                $stmt2 = $conn->prepare(
                    "INSERT INTO sessioni (id_sessione, id_utente, login, scadenza) 
                     VALUES (?, ?, ?, ?)"
                );
                $stmt2->bind_param("siss", $id_sessione, $id_utente, $login_time, $scadenza);
                $stmt2->execute();

                // Variabili di sessione PHP
                $_SESSION['id_sessione'] = $id_sessione;
                $_SESSION['id_utente'] = $id_utente;
                $_SESSION['username'] = $username;
                $_SESSION['ruolo'] = $ruolo;

                // Redirect
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
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Accedi</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>

<h1 class="accedi">Accedi</h1>

<?php if (!empty($errore)) : ?>
    <p style="color:red; font-weight:bold;">
        <?php echo $errore; ?>
    </p>
<?php endif; ?>

<form action="login.php" method="post">
    Username:
    <input type="text" name="username" required><br><br>

    Password:
    <input type="password" name="password" required><br><br>

    Codice TOTP:
    <input type="text" name="totp" required><br><br>

    <p>Genera codice TOTP su:
        <a href="http://localhost:8082" target="_blank">2FAuth</a>
    </p>

    <input type="submit" value="Login">
</form>

<h2>Se non hai un account, <a href="index.php">Registrati</a></h2>

</body>
</html>
