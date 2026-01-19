<?php
require_once 'config.php';

// TEST
$_SESSION['user_id'] = 9; 

$id_utente = $_SESSION['user_id'] ?? 0;
$messaggio = "";

if ($id_utente == 0) {
    header("Location: error403.html");
    exit;
}

// 1. AGGIORNAMENTO DATI
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_info') {
    $new_email = $_POST['email'];
    $new_phone = $_POST['telefono'];
    $new_pass = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    try {
        if ($new_pass) {
            $stmt = $conn->prepare("UPDATE Utente SET email = ?, password_hash = ? WHERE id_utente = ?");
            $stmt->bind_param("ssi", $new_email, $new_pass, $id_utente);
        } else {
            $stmt = $conn->prepare("UPDATE Utente SET email = ? WHERE id_utente = ?");
            $stmt->bind_param("si", $new_email, $id_utente);
        }
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("UPDATE Istruttore SET telefono = ? WHERE id_utente = ?");
        $stmt->bind_param("si", $new_phone, $id_utente);
        
        if($stmt->execute()) {
            $messaggio = "Dati aggiornati con successo!";
        } else {
            $messaggio = "Errore database: " . $stmt->error;
        }
        $stmt->close();
    } catch (Exception $e) {
        $messaggio = "Errore: " . $e->getMessage();
    }
}

// 2. RECUPERO DATI DEL PT
$stmt = $conn->prepare("SELECT u.*, i.telefono, i.specializzazione, i.qualifica FROM Utente u JOIN Istruttore i ON u.id_utente = i.id_utente WHERE u.id_utente = ?");
$stmt->bind_param("i", $id_utente);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();
$stmt->close();
?>

<!doctype html>
<html lang="it">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Palestra - Area Utente PT</title>
    <meta name="description" content="Area Trainer Il Tempio di Apollo, gestione clienti, caricamento schede e diete, agenda appuntamenti e monitoraggio progressi." />
    <meta name="keywords" content="area trainer, gestione clienti, agenda pt, caricamento schede, dashboard istruttore, personal trainer" />
    
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
            <h1>Profilo Trainer: <?php echo htmlspecialchars($userData['nome'] . " " . $userData['cognome']); ?></h1>

            <?php if($messaggio): ?>
                <div style="background:#fc0; color:black; padding:1em; margin-bottom:1em; border-radius:8px; text-align:center; font-weight:bold;">
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
                        <input type="date" value="1985-11-20" readonly>
                    </div>
                    <div class="data-item">
                        <label>Peso (kg)</label>
                        <input type="number" value="85" readonly>
                    </div>
                    <div class="data-item full-width">
                        <label>Educazione / Certificazioni</label>
                        <textarea readonly rows="2"><?php echo htmlspecialchars($userData['qualifica'] ?? 'Laurea in Scienze Motorie, Certificazione CONI'); ?></textarea>
                    </div>
                    <div class="data-item full-width">
                        <label>Skills / Specializzazione</label>
                        <input type="text" value="<?php echo htmlspecialchars($userData['specializzazione'] ?? 'Bodybuilding'); ?>" readonly>
                    </div>
                    <div class="data-item full-width">
                        <label>Presentazione</label>
                        <textarea readonly rows="3">Appassionato di fitness funzionale, aiuto i miei atleti a raggiungere la performance massima.</textarea>
                    </div>
                </form>
            </section>

            <section class="user-section">
                <h2>Gestione Lavoro</h2>
                <ul class="links-list">
                    <li>
                        <span>👥 Lista Clienti Attivi</span>
                        <a href="lista-clienti-pt.php" class="btn-link">Gestisci</a>
                    </li>
                    <li>
                        <span>📅 Calendario dei Corsi</span>
                        <a href="./corsi.html" class="btn-link">Visualizza</a>
                    </li>
                </ul>
            </section>

            <section class="user-section">
                <h2>Info Tecniche</h2>
                <form action="" method="POST">
                    <input type="hidden" name="action" value="update_info">
                    
                    <div class="tech-form-group">
                        <div class="data-item tech-input-wrapper">
                            <label>E-mail</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($userData['email']); ?>" required>
                        </div>
                    </div>

                    <div class="tech-form-group">
                        <div class="data-item tech-input-wrapper">
                            <label>Nuova Password (lascia vuoto per non cambiare)</label>
                            <input type="password" name="password" placeholder="********">
                        </div>
                    </div>

                    <div class="tech-form-group">
                        <div class="data-item tech-input-wrapper">
                            <label>Numero Telefono</label>
                            <input type="tel" name="telefono" value="<?php echo htmlspecialchars($userData['telefono'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-modify" style="width:100%; margin-top:1em;">Salva Modifiche</button>
                </form>
            </section>

            <section class="account-actions" aria-label="Gestione Account">
                <a href="./home.html" class="btn-logout">Logout</a>
                <button type="button" class="btn-delete" onclick="alert('Contattare l\'amministrazione per cancellare un account PT.')">Cancella Account</button>
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