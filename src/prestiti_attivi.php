<?php
session_start();
$messaggio = "";

if (!isset($_SESSION['id_sessione'])  || ($_SESSION['ruolo'] != 1 && $_SESSION['ruolo'] != 2)) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("db", "myuser", "mypassword", "myapp_db");

//RESTITUZIONE

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_prestito'])) {
    $id_prestito = (int)$_POST['id_prestito'];
    $id_libro = (int)$_POST['id_libro'];

    // 1. Segna come restituito
    $stmt = $conn->prepare("UPDATE prestiti SET restituito = 1 WHERE id = ?");
    $stmt->bind_param("i", $id_prestito);
    
    if ($stmt->execute()) {
        // 2. Incrementa le copie disponibili nel database libri
        $update_libri = $conn->prepare("UPDATE libri SET copie_disponibili = copie_disponibili + 1 WHERE id = ?");
        $update_libri->bind_param("i", $id_libro);
        $update_libri->execute();
        
        $messaggio = "Restituzione registrata con successo!";
    } else {
        $messaggio = "Errore durante la restituzione.";
    }
}

//STORICO PRESTITI

$query = "
    SELECT p.id, p.id_libro, p.data_prestito, p.data_restituzione, p.restituito, 
           u.username, l.titolo 
    FROM prestiti p
    JOIN user    u ON p.id_utente = u.id
    JOIN libri l ON p.id_libro = l.id
    ORDER BY p.restituito ASC, p.data_prestito DESC
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Storico Prestiti</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea, #764ba2);
            margin: 0;
            color: white;
            min-height: 100vh;
        }

        .header-nav {
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-back {
            padding: 10px 20px;
            background: rgba(255,255,255,0.2);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: 0.3s;
        }

        .btn-back:hover { background: rgba(255,255,255,0.3); }

        .container {
            max-width: 1000px;
            margin: 20px auto;
            background: rgba(255,255,255,0.1);
            padding: 30px;
            border-radius: 15px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: rgba(0,0,0,0.2);
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        th {
            background: rgba(255,255,255,0.1);
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 1px;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .attivo { background: #ff9f43; color: #fff; }
        .restituito { background: #2ed573; color: #fff; }

        .btn-restituisci {
            background: #eb4d4b;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-restituisci:hover { background: #ff7979; }

        .alert {
            padding: 15px;
            background: rgba(46, 213, 115, 0.3);
            border-left: 5px solid #2ed573;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="header-nav">
    <h2>Gestione Amministrativa</h2>
    <a href="amministrazione.php" class="btn-back"> Torna all'Area Amministrazione</a>
</div>

<div class="container">
    <h1>Storico Prestiti</h1>

    <?php if ($messaggio != ""): ?>
        <div class="alert"><?php echo $messaggio; ?></div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Utente</th>
                <th>Libro</th>
                <th>Data Inizio</th>
                <th>Scadenza</th>
                <th>Stato</th>
                <th>Azione</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($row['username']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['titolo']); ?></td>
                    <td><?php echo date("d/m/Y", strtotime($row['data_prestito'])); ?></td>
                    <td><?php echo date("d/m/Y", strtotime($row['data_restituzione'])); ?></td>
                    <td>
                        <?php if ($row['restituito'] == 0): ?>
                            <span class="status-badge attivo">ATTIVO</span>
                        <?php else: ?>
                            <span class="status-badge restituito">RESTITUITO</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($row['restituito'] == 0): ?>
                            <form method="post" style="margin:0;">
                                <input type="hidden" name="id_prestito" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="id_libro" value="<?php echo $row['id_libro']; ?>">
                                <button type="submit" class="btn-restituisci">Segna Restituito</button>
                            </form>
                        <?php else: ?>
                            <span style="color: #aaa; font-style: italic;">Concluso</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
            
            <?php if ($result->num_rows == 0): ?>
                <tr>
                    <td colspan="6" style="text-align:center;">Nessun prestito registrato nel sistema.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>