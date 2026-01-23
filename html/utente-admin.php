<?php
require_once 'config.php';

// Controllo accesso 
/*
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: error403.html");
    exit;
}
*/

//TEST
$id_admin = 1; 

$messaggio = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_credenziali') {
    $new_email = $_POST['email'];
    
    $new_pass = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
    
    try {
        if ($new_pass) {
            $stmt = $conn->prepare("UPDATE Utente SET email = ?, password_hash = ? WHERE id_utente = ?");
            $stmt->bind_param("ssi", $new_email, $new_pass, $id_admin);
        } else {
            $stmt = $conn->prepare("UPDATE Utente SET email = ? WHERE id_utente = ?");
            $stmt->bind_param("si", $new_email, $id_admin);
        }
        
        if ($stmt->execute()) {
            $messaggio = "Credenziali aggiornate con successo!";
        } else {
            $messaggio = "Errore durante l'aggiornamento: " . $stmt->error;
        }
        $stmt->close();
    } catch (Exception $e) {
        $messaggio = "Errore: " . $e->getMessage();
    }
}

$stmt = $conn->prepare("SELECT email FROM Utente WHERE id_utente = ?");
$stmt->bind_param("i", $id_admin);
$stmt->execute();
$result = $stmt->get_result();
$adminData = $result->fetch_assoc();
$currentEmail = $adminData['email'] ?? 'admin@email.it';
$stmt->close();
?>
<!doctype html>
<html lang="it">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Palestra - Area Utente Admin</title>
    <meta name="description" content="Pannello di Amministrazione Il Tempio di Apollo." />
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
            <a lang="en" href="./home.html">Home</a> >> Area personale
        </p>
    </nav>

    <main class="contenuto-principale">

        <section class="area-personale">
            <h1>Pannello Amministrazione</h1>

            <section class="user-section">
                <h2>Gestione Palestra</h2>
                <p style="margin-bottom: 1em;">Seleziona una lista per visualizzare, modificare o eliminare i record dal database.</p>
                <ul class="links-list">
                    <li>
                        <span>📝 Visualizza Lista Iscritti (Clienti)</span>
                        <a href="lista-clienti.php" class="btn-link">Gestisci</a>
                    </li>
                    <li>
                        <span>💪 Visualizza Lista Personal Trainer</span>
                        <a href="lista-pt.php" class="btn-link">Gestisci</a>
                    </li>
                    <li>
                        <span>👥 Visualizza Lista Utenti Totali</span>
                        <a href="lista-utenti.php" class="btn-link">Gestisci</a>
                    </li>
                </ul>
            </section>

            <section class="user-section">
                <h2>Credenziali Accesso</h2>
                
                <?php if ($messaggio): ?>
                    <div class="feedback-message"><?php echo $messaggio; ?></div>
                <?php endif; ?>

                <form action="" method="POST">
                    <input type="hidden" name="action" value="update_credenziali">
                    
                    <div class="tech-form-group">
                        <div class="data-item tech-input-wrapper">
                            <label>E-mail Amministratore</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($currentEmail); ?>" required>
                        </div>
                    </div>

                    <div class="tech-form-group">
                        <div class="data-item tech-input-wrapper">
                            <label>Nuova Password</label>
                            <input type="password" name="password" placeholder="Lascia vuoto per non cambiare">
                        </div>
                    </div>

                    <button type="submit" class="btn-modify">
                        Salva Credenziali
                    </button>
                </form>
            </section>

            <section class="account-actions" aria-label="Gestione Account">
                <a href="./home.html" class="btn-logout">Logout</a>
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
$conn->close();
?>