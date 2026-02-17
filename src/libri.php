<?php
session_start();
$messaggio = "";

if (!isset($_SESSION['id_sessione']) || ($_SESSION['ruolo'] != 0 && $_SESSION['ruolo'] != 1)) {
    header("Location: login.php");
    exit();
}


$conn = new mysqli("db", "myuser", "mypassword", "myapp_db");

// PRESTITO LIBRO
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_libro'])) {

    $id_libro = (int)$_POST['id_libro'];
    $id_utente = $_SESSION['id_utente'];

    $check = $conn->prepare("
        SELECT id 
        FROM prestiti 
        WHERE id_utente = ? 
        AND id_libro = ? 
        AND restituito = 0
    ");
    $check->bind_param("ii", $id_utente, $id_libro);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows > 0) {
        $messaggio = "Hai già questo libro in prestito!";
    } else {
        
        $stmt_copie = $conn->prepare("SELECT copie_disponibili FROM libri WHERE id = ?");
        $stmt_copie->bind_param("i", $id_libro);
        $stmt_copie->execute();
        $res_copie = $stmt_copie->get_result();
        $libro_info = $res_copie->fetch_assoc();

        if ($libro_info['copie_disponibili'] <= 0) {
            $messaggio = "Non ci sono copie disponibili per questo libro.";
        } else {
            $data_prestito = date("Y-m-d H:i:s");
            $data_restituzione = date("Y-m-d H:i:s", strtotime("+30 days"));

            $stmt = $conn->prepare("
                INSERT INTO prestiti (id_utente, id_libro, data_prestito, data_restituzione, restituito) 
                VALUES (?, ?, ?, ?, 0)
            ");
            $stmt->bind_param("iiss", $id_utente, $id_libro, $data_prestito, $data_restituzione);

            if ($stmt->execute()) {
                $stmt_update = $conn->prepare("UPDATE libri SET copie_disponibili = copie_disponibili - 1 WHERE id = ?");
                $stmt_update->bind_param("i", $id_libro);
                $stmt_update->execute();

                $messaggio = "Prestito registrato! Restituzione entro il " . date("d/m/Y", strtotime($data_restituzione));
            } else {
                $messaggio = "Errore durante il prestito.";
            }
        }
    }
}
// RICERCA LIBRI
$ricerca = isset($_GET['q']) ? trim($_GET['q']) : '';

if ($ricerca != '') {
    $stmt = $conn->prepare("SELECT * FROM libri WHERE titolo LIKE ? OR autore LIKE ? ORDER BY titolo");
    $like = "%$ricerca%";
    $stmt->bind_param("ss", $like, $like);
} else {
    $stmt = $conn->prepare("SELECT * FROM libri ORDER BY titolo");
}

$stmt->execute();
$result = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>Libri</title>
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


.search-bar {
    width: 100%;
    padding: 12px;
    border-radius: 6px;
    border: none;
    margin-bottom: 20px;
    font-size: 16px;
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
    transition: 0.2s;
}

.libro:hover {
    background: rgba(255,255,255,0.25);
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
}

.libro-info p {
    margin: 4px 0;
    font-size: 14px;
}
.presta-btn {
    padding: 10px 20px;
    background-color: #2ed573; 
    color: #fff;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 6px rgba(0,0,0,0.2);
}

.presta-btn:hover {
    background-color: #20bf6b;
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.25);
}

.presta-btn:disabled {
    background-color: #999;
    cursor: not-allowed;
    box-shadow: none;
}

</style>
</head>

<body>

<div class="logout-wrapper">
    <a class="logout-link" href="dashboard.php">Torna alla Dashboard</a>
</div>

<div class="container">
    <h1>Elenco Libri</h1>

<?php if ($messaggio != ""): ?>
    <div style="background: rgba(0,0,0,0.3); padding:10px; border-radius:6px; margin-bottom:15px;">
        <?php echo htmlspecialchars($messaggio); ?>
    </div>
<?php endif; ?>


    
    <form method="get">
        <input class="search-bar" type="text" name="q" placeholder="Cerca per titolo o autore..." value="<?php echo htmlspecialchars($ricerca); ?>">
    </form>
   <div class="lista-libri">
    <?php while($libro = $result->fetch_assoc()): ?>
        <div class="libro">
            <?php
          
            $nome_file = $libro['immagine'];
            $percorso_cartella = 'copertine/'; 

            if (!empty($nome_file) && file_exists($percorso_cartella . $nome_file)) {
                $img = $percorso_cartella . $nome_file;
            } else {
                $img = $percorso_cartella . 'default.jpg'; 
            }

            $checkPrestito = $conn->prepare("
                SELECT id 
                FROM prestiti 
                WHERE id_utente = ? 
                AND id_libro = ? 
                AND restituito = 0
            ");
            $checkPrestito->bind_param("ii", $_SESSION['id_utente'], $libro['id']);
            $checkPrestito->execute();
            $prestitoResult = $checkPrestito->get_result();
            $giaInPrestito = $prestitoResult->num_rows > 0;
            ?>
            <img src="<?php echo htmlspecialchars($img); ?>" alt="Copertina">

            <div class="libro-info">
                <h3><?php echo htmlspecialchars($libro['titolo']); ?></h3>
                <p>Autore: <?php echo htmlspecialchars($libro['autore']); ?></p>
                <p>Descrizione: <?php echo htmlspecialchars($libro['descrizione']); ?></p>
            </div>

            
            <?php if (!$giaInPrestito): ?>
                <form method="post">
                    <input type="hidden" name="id_libro" value="<?php echo $libro['id']; ?>">
                    <button class="presta-btn" type="submit">Prendi in prestito</button>
                </form>
            <?php else: ?>
                <span style="color:#ff6b81; font-weight:bold;">Già in prestito</span>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>

    <?php if ($result->num_rows == 0): ?>
        <p>Nessun libro trovato.</p>
    <?php endif; ?>
</div>


</body>
</html>
