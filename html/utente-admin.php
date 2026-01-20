<?php
/*
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: error403.html");
    exit;
}
*/

$conn = new mysqli("db", "root", "example", "palestra_db", 3306);
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}
?>
<!doctype html>
<html lang="it">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Palestra - Area Utente Admin</title>
    <meta name="description" content="Pannello di Amministrazione Il Tempio di Apollo, gestione iscritti, abbonamenti, corsi, staff e statistiche della palestra." />
    <meta name="keywords" content="pannello admin, gestione palestra, database utenti, amministrazione, statistiche" />
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
            <a lang="en" href="./home.html">Home</a> > > Area personale
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
                <form action="#" method="POST" id="credentialsForm">
                    <div class="tech-form-group">
                        <div class="data-item tech-input-wrapper">
                            <label>E-mail Amministratore</label>
                            <input type="email" value="admin@tempioapollo.it" name="email" readonly id="emailInput">
                        </div>
                        <button type="button" class="btn-modify" id="editEmailBtn">Modifica</button>
                    </div>

                    <div class="tech-form-group">
                        <div class="data-item tech-input-wrapper">
                            <label>Password</label>
                            <input type="password" value="********" name="password" readonly id="passwordInput">
                        </div>
                        <button type="button" class="btn-modify" id="editPasswordBtn">Cambia</button>
                    </div>

                    <div id="saveCancelButtons" style="display: none;">
                        <button type="submit" class="btn-modify save-btn">Salva</button>
                        <button type="button" class="btn-modify cancel-btn" id="cancelBtn">Annulla</button>
                    </div>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editEmailBtn = document.getElementById('editEmailBtn');
            const editPasswordBtn = document.getElementById('editPasswordBtn');
            const emailInput = document.getElementById('emailInput');
            const passwordInput = document.getElementById('passwordInput');
            const saveCancelButtons = document.getElementById('saveCancelButtons');
            const cancelBtn = document.getElementById('cancelBtn');
            const credentialsForm = document.getElementById('credentialsForm');

            let originalEmail = emailInput.value;
            let originalPassword = passwordInput.value;

            editEmailBtn.addEventListener('click', function() {
                emailInput.removeAttribute('readonly');
                emailInput.focus();
                saveCancelButtons.style.display = 'block';
            });

            editPasswordBtn.addEventListener('click', function() {
                passwordInput.removeAttribute('readonly');
                passwordInput.value = '';
                passwordInput.focus();
                saveCancelButtons.style.display = 'block';
            });

            cancelBtn.addEventListener('click', function() {
                emailInput.setAttribute('readonly', 'readonly');
                passwordInput.setAttribute('readonly', 'readonly');
                emailInput.value = originalEmail;
                passwordInput.value = originalPassword;
                saveCancelButtons.style.display = 'none';
            });

            credentialsForm.addEventListener('submit', function(e) {
                // Prevent default form submission for demo purposes
                e.preventDefault();
                alert('Credenziali salvate con successo!');
                emailInput.setAttribute('readonly', 'readonly');
                passwordInput.setAttribute('readonly', 'readonly');
                originalEmail = emailInput.value;
                originalPassword = passwordInput.value;
                saveCancelButtons.style.display = 'none';
            });
        });
    </script>
</body>

</html>
<?php
$conn->close();
?>
