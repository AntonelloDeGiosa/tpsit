<?php
session_start();
require 'vendor/autoload.php';

use OTPHP\TOTP;


if (!isset($_SESSION['id_sessione']) || $_SESSION['ruolo'] != 2) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("db", "myuser", "mypassword", "myapp_db");

$messaggio = "";

// AGGIUNTA BIBLIOTECARIO

if (isset($_POST['azione']) && $_POST['azione'] == 'aggiungi') {

    $nome = $_POST['nome'];
    $cognome = $_POST['cognome'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $ruolo = 1;
    $data_registrazione = date("Y-m-d H:i:s");

    
    $totp = TOTP::create();
    $secret = $totp->getSecret();

    $stmt = $conn->prepare("
        INSERT INTO user (nome, cognome, email, username, password, totp_secret, ruolo, registrazione)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("ssssssis", $nome, $cognome, $email, $username, $password, $secret, $ruolo, $data_registrazione);

    if ($stmt->execute()) {
        $messaggio = "Bibliotecario creato!";
    } else {
        $messaggio = "Errore durante la creazione.";
    }
}

// LICENZIA BIBLIOTECARIO

if (isset($_POST['azione']) && $_POST['azione'] == 'licenzia') {

    $id = (int)$_POST['id'];

    $stmt = $conn->prepare("DELETE FROM user WHERE id = ? AND ruolo = 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $messaggio = "Bibliotecario rimosso.";
}


// LISTA BIBLIOTECARI
$result = $conn->query("SELECT id, nome, cognome, email, username, totp_secret FROM user WHERE ruolo = 1 ORDER BY cognome");

?>

<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>Gestione Bibliotecari</title>

<style>
body {
    font-family: Arial;
    background: linear-gradient(135deg, #667eea, #764ba2);
    margin: 0;
    color: white;
}

.container {
    max-width: 900px;
    margin: 80px auto;
    background: rgba(255,255,255,0.1);
    padding: 20px;
    border-radius: 10px;
    backdrop-filter: blur(10px);
}

.logout-wrapper {
    position: absolute;
    top: 20px;
    right: 20px;
}

.logout-link {
    padding: 12px 24px;
    background: rgba(255,255,255,0.2);
    color: white;
    text-decoration: none;
    border-radius: 50px;
}

/* Bottone toggle */
.toggle-btn {
    padding: 10px 18px;
    background: #2ed573;
    border: none;
    border-radius: 6px;
    color: white;
    cursor: pointer;
    margin-bottom: 15px;
}

.toggle-btn:hover {
    background: #20bf6b;
}

/* Form nascosto */
.form-container {
    display: none;
    background: rgba(0,0,0,0.3);
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.form-container input {
    width: 100%;
    padding: 8px;
    margin-bottom: 10px;
    border-radius: 4px;
    border: none;
}

/* Lista */
.bibliotecario {
    background: rgba(255,255,255,0.15);
    padding: 12px;
    margin-bottom: 10px;
    border-radius: 8px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.licenzia-btn {
    background: #ff4757;
    border: none;
    padding: 6px 12px;
    border-radius: 5px;
    color: white;
    cursor: pointer;
}

.licenzia-btn:hover {
    background: #e84118;
}

.messaggio {
    background: rgba(0,0,0,0.4);
    padding: 10px;
    border-radius: 6px;
    margin-bottom: 15px;
}
</style>

<script>
function toggleForm() {
    var form = document.getElementById("formBibliotecario");
    form.style.display = (form.style.display === "none" || form.style.display === "") 
        ? "block" 
        : "none";
}
</script>

</head>
<body>

<div class="logout-wrapper">
    <a class="logout-link" href="amministrazione.php">Torna all'Area Amministrazione</a>
</div>

<div class="container">
    <h1>Gestione Bibliotecari</h1>

    <?php if ($messaggio != ""): ?>
        <div class="messaggio"><?php echo htmlspecialchars($messaggio); ?></div>
    <?php endif; ?>

    <button class="toggle-btn" onclick="toggleForm()">Aggiungi Bibliotecario</button>

    <div class="form-container" id="formBibliotecario">
        <form method="post">
            <input type="hidden" name="azione" value="aggiungi">

            <input type="text" name="nome" placeholder="Nome" required>
            <input type="text" name="cognome" placeholder="Cognome" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>

            <button class="toggle-btn" type="submit">Salva Bibliotecario</button>
        </form>
    </div>

    <h2>Bibliotecari attivi</h2>

    <?php while($b = $result->fetch_assoc()): ?>
        <div class="bibliotecario">
            <div>
                <strong><?php echo htmlspecialchars($b['nome']." ".$b['cognome']); ?></strong><br>
                <?php echo htmlspecialchars($b['email']); ?><br>
                Username: <?php echo htmlspecialchars($b['username']); ?><br>  
                
            </div>

            <form method="post" onsubmit="return confirm('Licenziare questo bibliotecario?');">
                <input type="hidden" name="azione" value="licenzia">
                <input type="hidden" name="id" value="<?php echo $b['id']; ?>">
                <button class="licenzia-btn">Licenzia</button>
            </form>
        </div>
    <?php endwhile; ?>

</div>

</body>
</html>
