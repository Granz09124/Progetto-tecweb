<?php
require_once 'config.php';

// TEST
$_SESSION['user_id'] = 9;

$id_pt = $_SESSION['user_id'] ?? 0;
$messaggio = "";

if ($id_pt == 0) {
    header("Location: home.html");
    exit;
}

// 1. GESTIONE UPLOAD SCHEDA
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['scheda_file'])) {
    $id_cliente_target = $_POST['id_cliente'];
    $messaggio = uploadScheda($_FILES['scheda_file'], $id_cliente_target, $id_pt, $conn);
}

// 2. RECUPERO LISTA CLIENTI
$stmtClienti = $conn->prepare("
    SELECT u.id_utente, u.nome, u.cognome, c.codice_fiscale, c.telefono 
    FROM Utente u 
    JOIN Cliente c ON u.id_utente = c.id_utente
    JOIN Assegnazione_PT apt ON c.id_utente = apt.id_cliente
    WHERE apt.id_pt = ?
");
$stmtClienti->bind_param("i", $id_pt);
$stmtClienti->execute();
$resultClienti = $stmtClienti->get_result();
?>

<!doctype html>
<html lang="it">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Palestra - I Miei Clienti</title>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/utente.css" />
    <link rel="stylesheet" media="screen and (max-width:768px), only screen and (max-width:768px)" href="../css/mini.css">
</head>

<body id="layout-adhoc">
    <header class="intestazione">
        <div class="intestazione-bg">
            <a href="./home.html">
                <img src="../immagini/Logo_palestra.png" alt="Home" class="logo">
            </a>
            <h1>Il Tempio di Apollo</h1>
        </div>

        <nav id="menu" aria-label="menu">
            <ul>
                <li><a lang="en" href="./home.html">Home</a></li>
                <li>Area Utente</li>
                <li><a href="./home.html">Logout</a></li>
            </ul>
        </nav>
    </header>

    <nav id="breadcrumb">
        <p>
            Ti trovi in:
            <a lang="en" href="./home.html">Home</a> &gt; &gt; 
            <a href="./utente-pt.php">Area personale</a> &gt; &gt; 
            Lista Clienti
        </p>
    </nav>

    <main class="contenuto-principale">

        <section class="area-personale">
            <h1>I Miei Clienti Attivi</h1>

            <?php if($messaggio): ?>
                <div style="background:#fc0; color:black; padding:1em; margin-bottom:1em; border-radius:8px; text-align:center;">
                    <?php echo $messaggio; ?>
                </div>
            <?php endif; ?>

            <section class="user-section">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 20%;">Cliente</th>
                            <th style="width: 25%;">Codice Fiscale</th>
                            <th style="width: 15%;">Telefono</th>
                            <th style="width: 40%;">Carica Scheda Tecnica</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultClienti->num_rows > 0): ?>
                            <?php while($cliente = $resultClienti->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($cliente['nome'] . " " . $cliente['cognome']); ?></td>
                                <td><?php echo htmlspecialchars($cliente['codice_fiscale']); ?></td>
                                <td><?php echo htmlspecialchars($cliente['telefono']); ?></td>
                                <td>
                                    <form action="" method="POST" enctype="multipart/form-data" style="display:flex; gap:10px; align-items:center;">
                                        <input type="hidden" name="id_cliente" value="<?php echo $cliente['id_utente']; ?>">
                                        
                                        <input type="file" name="scheda_file" required 
                                               style="background-color: var(--mainBackgroundColorLight); 
                                                      border: 1px solid var(--mainColorDark); 
                                                      color: var(--textColor); 
                                                      padding: 5px; 
                                                      border-radius: 4px; 
                                                      font-size: 0.9em;
                                                      width: 100%;">
                                        
                                        <button type="submit">⬆️ Carica</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align:center; padding:2em;">Non hai clienti assegnati al momento.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>

            <section class="account-actions" aria-label="Gestione Account">
                <a href="./utente-pt.php" class="btn-logout">Torna Indietro</a>
            </section>
        </section>

    </main>

    <button id="torna-su" onclick="scrollToTop()" aria-label="Torna su">
        <img src="../immagini/Icon/torna_su.webp" alt="Torna su" />
    </button>

    <footer>
        <p>&copy; 2025 Palestra. Tutti i diritti riservati.</p>
    </footer>

    <script src="../javascript/torna-su.js"></script>
</body>
</html>
<?php 
$stmtClienti->close(); 
$conn->close(); 
?>