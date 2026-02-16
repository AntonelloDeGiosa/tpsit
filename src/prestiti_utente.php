<?php
session_start();
$messaggio = "";

// Controllo login
if (!isset($_SESSION['id_sessione'])) {
    header("Location: login.php");
    exit();
}

// Connessione DB
$conn = new mysqli("db", "myuser", "mypassword", "myapp_db");

// ==========================
// RESTITUZIONE LIBRO
// ==========================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_prestito'])) {

    $id_prestito = (int)$_POST['id_prestito'];

    // Prendi id_libro del prestito
    $stmt_libro = $conn->prepare("SELECT id_libro FROM prestiti WHERE id = ? AND id_utente = ? AND restituito = 0");
    $stmt_libro->bind_param("ii", $id_prestito, $_SESSION['id_utente']);
    $stmt_libro->execute();
    $res_libro = $stmt_libro->get_result();

    if ($res_libro->num_rows > 0) {
        $libro = $res_libro->fetch_assoc();
        $id_libro = $libro['id_libro'];

        $data_restituzione = date("Y-m-d H:i:s");

        $stmt = $conn->prepare("
            UPDATE prestiti 
            SET restituito = 1, data_restituzione = ? 
            WHERE id = ? AND id_utente = ?
        ");
        $stmt->bind_param("sii", $data_restituzione, $id_prestito, $_SESSION['id_utente']);

        if ($stmt->execute()) {
            // Incrementa copie disponibili
            $stmt_update = $conn->prepare("UPDATE libri SET copie_disponibili = copie_disponibili + 1 WHERE id = ?");
            $stmt_update->bind_param("i", $id_libro);
            $stmt_update->execute();

            $messaggio = "Restituzione avvenuta con successo!";
        } else {
            $messaggio = "Errore durante la restituzione.";
        }
    } else {
        $messaggio = "Prestito non valido o già restituito.";
    }
}

// ==========================
// ELENCO PRESTITI ATTIVI
// ==========================
$stmt = $conn->prepare("
    SELECT p.id AS id_prestito, l.titolo, l.autore, l.immagine, p.data_prestito, p.data_restituzione
    FROM prestiti p
    JOIN libri l ON p.id_libro = l.id
    WHERE p.id_utente = ? AND p.restituito = 0
    ORDER BY p.data_prestito DESC
");
$stmt->bind_param("i", $_SESSION['id_utente']);
$stmt->execute();
$result = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>I miei Prestiti</title>

<style>
body {
    font-family: Arial, sans-serif;
    background: linear-gradient(135deg, #667eea, #764ba2);
    margin: 0;
    color: white;
}

.logout-wrapper {
    position: absolute;
    top: 20px;
    right: 20px;
}

.logout-link {
    display: inline-flex;
    align-items: center;
    padding: 12px 24px;
    background-color: rgba(255, 255, 255, 0.2);
    color: #fff;
    text-decoration: none;
    border-radius: 50px;
    font-weight: 600;
    font-size: 0.9rem;
    border: 1px solid rgba(255, 255, 255, 0.4);
    transition: all 0.3s ease;
    backdrop-filter: blur(5px);
}

.logout-link:hover {
    background-color: #ff4757;
    border-color: #ff4757;
    transform: scale(1.05);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    color: white;
}

.logout-link::before {
    content: '↩';
    margin-right: 8px;
}

.container {
    max-width: 900px;
    margin: 80px auto;
    background: rgba(255,255,255,0.1);
    padding: 20px;
    border-radius: 10px;
    backdrop-filter: blur(10px);
}

.lista-libri {
    max-height: 500px;
    overflow-y: auto;
}

.libro {
    display: flex;
    align-items: center;
    background: rgba(255,255,255,0.15);
    padding: 12px;
    margin-bottom: 10px;
    border-radius: 8px;
}

.libro img {
    width: 70px;
    height: 100px;
    object-fit: cover;
    border-radius: 4px;
    margin-right: 15px;
}

.libro-info {
    flex: 1;
}

.libro-info h3 {
    margin: 0;
    font-size: 1.1rem;
}

.libro-info p {
    margin: 2px 0;
    font-size: 14px;
}

.restituisci-btn {
    padding: 8px 14px;
    background: #ff6b81; /* rosso pastello */
    border: none;
    border-radius: 6px;
    color: white;
    cursor: pointer;
    font-weight: 600;
    transition: 0.3s;
}

.restituisci-btn:hover {
    background: #ff4757;
    transform: translateY(-2px);
}

.messaggio {
    background: rgba(0,0,0,0.3);
    padding:10px;
    border-radius:6px;
    margin-bottom:15px;
}
</style>
</head>

<body>

<div class="logout-wrapper">
    <a class="logout-link" href="dashboard.php">Torna alla Dashboard</a>
</div>

<div class="container">
    <h1>I miei Prestiti</h1>

    <?php if ($messaggio != ""): ?>
        <div class="messaggio"><?php echo htmlspecialchars($messaggio); ?></div>
    <?php endif; ?>

    <div class="lista-libri">
        <?php if ($result->num_rows > 0): ?>
            <?php while($libro = $result->fetch_assoc()): ?>
                <div class="libro">
                    <?php
                    $img = $libro['immagine'] ? $libro['immagine'] : 'copertine/default.jpg';
                    ?>
                    <img src="<?php echo htmlspecialchars($img); ?>" alt="Copertina">

                    <div class="libro-info">
                        <h3><?php echo htmlspecialchars($libro['titolo']); ?></h3>
                        <p>Data prestito: <?php echo date("d/m/Y", strtotime($libro['data_prestito'])); ?></p>
                        <p>Restituzione prevista: <?php echo date("d/m/Y", strtotime($libro['data_restituzione'])); ?></p>
                    </div>

                    <form method="post">
                        <input type="hidden" name="id_prestito" value="<?php echo $libro['id_prestito']; ?>">
                        <button type="submit" class="restituisci-btn">Restituisci</button>
                    </form>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Nessun prestito attivo.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
