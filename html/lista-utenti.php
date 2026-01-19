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

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM Utente WHERE id_utente = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $cognome = $_POST['cognome'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    if (isset($_POST['id'])) {
        // edit
        $id = intval($_POST['id']);
        if (!empty($password)) {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE Utente SET nome = ?, cognome = ?, email = ?, password_hash = ? WHERE id_utente = ?");
            $stmt->bind_param("ssssi", $nome, $cognome, $email, $password_hash, $id);
        } else {
            $stmt = $conn->prepare("UPDATE Utente SET nome = ?, cognome = ?, email = ? WHERE id_utente = ?");
            $stmt->bind_param("sssi", $nome, $cognome, $email, $id);
        }
    } else {
        // add
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO Utente (nome, cognome, email, password_hash) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nome, $cognome, $email, $password_hash);
    }
    $stmt->execute();
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT id_utente, nome, cognome, email FROM Utente WHERE id_utente = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        header('Content-Type: application/json');
        echo json_encode($user);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'User not found']);
    }
    $stmt->close();
    $conn->close();
    exit;
}
?>
<!doctype html>
<html lang="it">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Palestra - Lista Utenti Totali</title>
    <meta name="description" content="Lista utenti totali della palestra Il Tempio di Apollo." />
    <meta name="keywords" content="lista utenti, utenti palestra, amministrazione" />
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
            <a lang="en" href="./home.html">Home</a> > > <a href="./utente-admin.php">Area personale</a> > > Lista Utenti Totali
        </p>
    </nav>

    <main class="contenuto-principale">

        <section class="area-personale">
            <h1>Lista Utenti Totali</h1>

            <section class="user-section">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Cognome</th>
                            <th>Email</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $queryUtenti = "SELECT id_utente, nome, cognome, email FROM Utente";
                        $resultUtenti = $conn->query($queryUtenti);
                        if ($resultUtenti->num_rows > 0) {
                            while ($row = $resultUtenti->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['id_utente']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['nome']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['cognome']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                echo "<td><button onclick='editUser(" . $row['id_utente'] . ")'>Modifica</button> <a href='?delete=" . $row['id_utente'] . "' onclick='return confirm(\"Sei sicuro di voler eliminare questo utente?\")'>Elimina</a></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>Nessun utente trovato.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>

                <button onclick="showForm()">Aggiungi Nuovo Utente</button>

                <div id="userForm" style="display: none; margin-top: 20px; padding: 20px; border: 1px solid #ccc;">
                    <h2 id="formTitle">Aggiungi Utente</h2>
                    <form method="POST" action="">
                        <input type="hidden" id="userId" name="id" value="">
                        <label for="nome">Nome:</label>
                        <input type="text" id="nome" name="nome" required><br><br>
                        <label for="cognome">Cognome:</label>
                        <input type="text" id="cognome" name="cognome" required><br><br>
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required><br><br>
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required><br><br>
                        <button type="submit">Salva</button>
                        <button type="button" onclick="hideForm()">Annulla</button>
                    </form>
                </div>
            </section>

            <section class="account-actions" aria-label="Gestione Account">
                <a href="./utente-admin.php" class="btn-logout">Torna al Pannello</a>
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
        function showForm() {
            document.getElementById('formTitle').textContent = 'Aggiungi Utente';
            document.getElementById('userId').value = '';
            document.getElementById('nome').value = '';
            document.getElementById('cognome').value = '';
            document.getElementById('email').value = '';
            document.getElementById('password').value = '';
            document.getElementById('userForm').style.display = 'block';
        }

        function editUser(id) {
            // Fetch user data via AJAX or pre-fill from PHP
            // For simplicity, assume we need to fetch
            fetch('?edit=' + id)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('formTitle').textContent = 'Modifica Utente';
                    document.getElementById('userId').value = data.id_utente;
                    document.getElementById('nome').value = data.nome;
                    document.getElementById('cognome').value = data.cognome;
                    document.getElementById('email').value = data.email;
                    document.getElementById('password').value = ''; // Don't pre-fill password
                    document.getElementById('userForm').style.display = 'block';
                });
        }

        function hideForm() {
            document.getElementById('userForm').style.display = 'none';
        }
    </script>
</body>

</html>
<?php
$conn->close();
?>
