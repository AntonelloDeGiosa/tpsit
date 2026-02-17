<?php
session_start();
$messaggio = "";

// Permetti accesso ad admin (0) e bibliotecario (1)
if (!isset($_SESSION['id_sessione']) || ($_SESSION['ruolo'] != 1 && $_SESSION['ruolo'] != 2)) {
    header("Location: login.php");
    exit();
}

// Connessione DB
$conn = new mysqli("db", "myuser", "mypassword", "myapp_db");

// ==========================
// GESTIONE COPIE (AGGIUNGI / RIMUOVI)
// ==========================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['azione']) && isset($_POST['id_libro'])) {
    $id_libro = (int)$_POST['id_libro'];
    $azione = $_POST['azione'];

    if ($azione === 'aggiungi') {
        // Incrementa sia totali che disponibili
        $stmt = $conn->prepare("UPDATE libri SET copie_totali = copie_totali + 1, copie_disponibili = copie_disponibili + 1 WHERE id = ?");
        $stmt->bind_param("i", $id_libro);
        if ($stmt->execute()) {
            $messaggio = "Copia aggiunta con successo!";
        }
    } 
    elseif ($azione === 'rimuovi') {
        // Controlla prima se ci sono copie da rimuovere (evitiamo numeri negativi)
        $check = $conn->prepare("SELECT copie_totali, copie_disponibili FROM libri WHERE id = ?");
        $check->bind_param("i", $id_libro);
        $check->execute();
        $info = $check->get_result()->fetch_assoc();

        if ($info['copie_totali'] > 0 && $info['copie_disponibili'] > 0) {
            // Decrementa sia totali che disponibili
            $stmt = $conn->prepare("UPDATE libri SET copie_totali = copie_totali - 1, copie_disponibili = copie_disponibili - 1 WHERE id = ?");
            $stmt->bind_param("i", $id_libro);
            $stmt->execute();
            $messaggio = "Copia rimossa con successo!";
        } else {
            $messaggio = "Impossibile rimuovere: non ci sono copie disponibili o il magazzino è vuoto.";
        }
    }
}

// ==========================
// RICERCA LIBRI
// ==========================
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
    <title>Gestione Inventario Libri</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea, #764ba2);
            margin: 0;
            color: white;
            min-height: 100vh;
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
            background-color: rgba(255, 255, 255, 0.4);
            transform: scale(1.05);
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
            box-sizing: border-box;
        }

        .lista-libri {
            max-height: 500px;
            overflow-y: auto;
        }

        .libro {
            display: flex;
            align-items: center;
            background: rgba(255,255,255,0.15);
            padding: 15px;
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

        .copie-badge {
            display: inline-block;
            background: rgba(0,0,0,0.2);
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 13px;
            margin-top: 5px;
        }

        .azioni-copie {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .btn-gestione {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.2s;
            min-width: 120px;
        }

        .btn-add { background-color: #2ed573; color: white; }
        .btn-add:hover { background-color: #26af5f; }

        .btn-remove { background-color: #ff4757; color: white; }
        .btn-remove:hover { background-color: #d63031; }
    </style>
</head>
<body>

<div class="logout-wrapper">
    <a class="logout-link" href="amministrazione.php">Torna all'Area Amministrazione</a>
</div>

<div class="container">
    <h1>Gestione Inventario Libri</h1>

    <?php if ($messaggio != ""): ?>
        <div style="background: rgba(255,255,255,0.2); padding:10px; border-radius:6px; margin-bottom:15px; border-left: 5px solid #fff;">
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
                $img = (!empty($libro['immagine']) && file_exists('copertine/' . $libro['immagine'])) 
                       ? 'copertine/' . $libro['immagine'] 
                       : 'copertine/default.jpg';
                ?>
                <img src="<?php echo htmlspecialchars($img); ?>" alt="Copertina">

                <div class="libro-info">
                    <h3 style="margin:0;"><?php echo htmlspecialchars($libro['titolo']); ?></h3>
                    <p style="margin:5px 0;">Autore: <?php echo htmlspecialchars($libro['autore']); ?></p>
                    
                    <div class="copie-badge">
                        <strong>Totali:</strong> <?php echo $libro['copie_totali']; ?> | 
                        <strong>Disponibili:</strong> <?php echo $libro['copie_disponibili']; ?>
                    </div>
                </div>

                <div class="azioni-copie">
                    <form method="post" style="margin:0;">
                        <input type="hidden" name="id_libro" value="<?php echo $libro['id']; ?>">
                        <input type="hidden" name="azione" value="aggiungi">
                        <button type="submit" class="btn-gestione btn-add">+ Copia</button>
                    </form>

                    <form method="post" style="margin:0;">
                        <input type="hidden" name="id_libro" value="<?php echo $libro['id']; ?>">
                        <input type="hidden" name="azione" value="rimuovi">
                        <button type="submit" class="btn-gestione btn-remove" 
                                <?php echo ($libro['copie_totali'] <= 0) ? 'disabled' : ''; ?>>
                            - Copia
                        </button>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>

        <?php if ($result->num_rows == 0): ?>
            <p>Nessun libro trovato nell'inventario.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>