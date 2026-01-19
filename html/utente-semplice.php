<?php
require_once 'config.php';

// TEST
$_SESSION['user_id'] = 3;

$id_utente = $_SESSION['user_id'] ?? 0;
$messaggio = "";

if ($id_utente == 0) {
    header("Location: error403.html");
    exit;
}

// 1. UPDATE DATI
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_info') {
    $new_email = $_POST['email'];
    $new_phone = $_POST['telefono'];
    $new_pass = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    if ($new_pass) {
        $stmt = $conn->prepare("UPDATE Utente SET email = ?, password_hash = ? WHERE id_utente = ?");
        $stmt->bind_param("ssi", $new_email, $new_pass, $id_utente);
    } else {
        $stmt = $conn->prepare("UPDATE Utente SET email = ? WHERE id_utente = ?");
        $stmt->bind_param("si", $new_email, $id_utente);
    }
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("UPDATE Cliente SET telefono = ? WHERE id_utente = ?");
    $stmt->bind_param("si", $new_phone, $id_utente);
    
    if($stmt->execute()) {
        $messaggio = "Dati aggiornati!";
    }
    $stmt->close();
}

// 2. GET USER DATA
$stmt = $conn->prepare("SELECT u.*, c.telefono, c.codice_fiscale FROM Utente u JOIN Cliente c ON u.id_utente = c.id_utente WHERE u.id_utente = ?");
$stmt->bind_param("i", $id_utente);
$stmt->execute();
$userData = $stmt->get_result()->fetch_assoc();
$stmt->close();

// 3. GET SCHEDE
$stmtSchede = $conn->prepare("
    SELECT s.*, u.nome as nome_pt, u.cognome as cognome_pt 
    FROM Scheda_Allenamento s
    LEFT JOIN Utente u ON s.id_pt = u.id_utente
    WHERE s.id_cliente = ? 
    ORDER BY s.data_caricamento DESC
");
$stmtSchede->bind_param("i", $id_utente);
$stmtSchede->execute();
$resultSchede = $stmtSchede->get_result();
?>

<!doctype html>
<html lang="it">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Palestra - Area Utente</title>
    <meta name="description" content="Area Utente Il Tempio di Apollo, consulta la tua scheda, la dieta, prenota i corsi e controlla la scadenza del tuo abbonamento." />
    <meta name="keywords" content="profilo utente, scheda allenamento, dieta, prenotazione corsi, scadenza abbonamento, area clienti" />
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/utente.css" />

    <link rel="stylesheet" media="screen and (max-width:768px), only screen and (max-width:768px)" href="../css/mini.css">
    <link rel="stylesheet" media="print" href="../css/print-utenti.css">
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
            <input type="checkbox" id="menu-toggle">
            <div id="hamburger-menu">
                <span id="ham-line1"></span>
                <span id="ham-line2"></span>
                <span id="ham-line3"></span>
            </div>

            <ul>
                <li><a lang="en" href="./home.html">Home</a></li>
                <li><a href="./Palestra.html"><strong>Palestra</strong></a></li>
                <li><a href="./abbonamenti.html">Abbonamenti</a></li>
                <li><a href="./cerca-pt.html">Ricerca Istruttori</a></li>
                <li><a href="./Contattaci.html">Contattaci</a></li>
                <li>Area Utente</li>
                <li><a href="./home.html">Logout</a></li>
            </ul>
        </nav>
    </header>

    <nav id="breadcrumb">
        <p>
            Ti trovi in:
            <a lang="en" href="./home.html">Home</a> &gt; &gt; Area personale
        </p>
    </nav>

    <main class="contenuto-principale">

        <section class="area-personale">
            <h1>Profilo Utente: <?php echo htmlspecialchars($userData['nome']); ?></h1>

            <?php if($messaggio): ?>
                <div style="background:#fc0; color:black; padding:1em; margin-bottom:1em; border-radius:8px;">
                    <?php echo $messaggio; ?>
                </div>
            <?php endif; ?>

            <section class="user-section">
                <h2>Informazioni Personali</h2>
                <form class="data-list-group">
                    <div class="data-item">
                        <label>Nome</label>
                        <input type="text" value="<?php echo htmlspecialchars($userData['nome']); ?>" readonly>
                    </div>
                    <div class="data-item">
                        <label>Cognome</label>
                        <input type="text" value="<?php echo htmlspecialchars($userData['cognome']); ?>" readonly>
                    </div>
                    <div class="data-item">
                        <label>Data di Nascita</label>
                        <input type="date" value="1990-05-15" readonly>
                    </div>
                    <div class="data-item">
                        <label>Peso (kg)</label>
                        <input type="number" value="78" readonly>
                    </div>
                    <div class="data-item full-width">
                        <label>Tipologia Iscrizione</label>
                        <input type="text" value="Abbonamento Premium Annuale" readonly>
                    </div>
                </form>
            </section>

            <section class="user-section">
                <h2>I tuoi Documenti & Attività</h2>
                <ul class="links-list">
                    <?php if ($resultSchede->num_rows > 0): ?>
                        <?php while($scheda = $resultSchede->fetch_assoc()): ?>
                        <li>
                            <span>
                                📄 <strong><?php echo htmlspecialchars($scheda['nome_file']); ?></strong>
                                <br><small>PT: <?php echo htmlspecialchars($scheda['nome_pt']); ?></small>
                            </span>
                            <a href="<?php echo htmlspecialchars($scheda['percorso_file']); ?>" class="btn-link" download>Scarica</a>
                        </li>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <li><span>Nessuna scheda caricata al momento.</span></li>
                    <?php endif; ?>

                    <li>
                        <span>ℹ️ Info Personal Trainer</span>
                        <a href="./cerca-istruttore.php" class="btn-link">Vai al Profilo</a>
                    </li>
                    <li>
                        <span>📅 Calendario Corsi</span>
                        <a href="./corsi.html" class="btn-link">Visualizza</a>
                    </li>
                </ul>
            </section>

            <section class="user-section">
                <h2>Info Tecniche</h2>
                <form action="" method="POST">
                    <input type="hidden" name="action" value="update_info">
                    
                    <div class="tech-form-group">
                        <div class="tech-input-wrapper">
                            <label>E-mail</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($userData['email']); ?>">
                        </div>
                    </div>
                    <div class="tech-form-group">
                        <div class="tech-input-wrapper">
                            <label>Nuova Password</label>
                            <input type="password" name="password" placeholder="********">
                        </div>
                    </div>
                    <div class="tech-form-group">
                        <div class="tech-input-wrapper">
                            <label>Numero Telefono</label>
                            <input type="tel" name="telefono" value="<?php echo htmlspecialchars($userData['telefono']); ?>">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-modify">Salva Modifiche</button>
                </form>
            </section>

            <section class="account-actions" aria-label="Gestione Account">
                <a href="./home.html" class="btn-logout">Logout</a>
                <button class="btn-delete" onclick="alert('Sei sicuro di voler cancellare l\'account?')">Cancella Account</button>
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
$stmtSchede->close();
$conn->close(); 
?>