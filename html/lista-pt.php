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

// LOGICA CANCELLAZIONE
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM Utente WHERE id_utente = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// LOGICA SALVATAGGIO (ADD / EDIT)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $cognome = $_POST['cognome'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $specializzazione = $_POST['specializzazione'];
    $qualifica = $_POST['qualifica'];
    
    if (!empty($_POST['id'])) {
        // Modifica esistente
        $id = intval($_POST['id']);
        if (!empty($password)) {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE Utente SET nome = ?, cognome = ?, email = ?, password_hash = ? WHERE id_utente = ?");
            $stmt->bind_param("ssssi", $nome, $cognome, $email, $password_hash, $id);
        } else {
            $stmt = $conn->prepare("UPDATE Utente SET nome = ?, cognome = ?, email = ? WHERE id_utente = ?");
            $stmt->bind_param("sssi", $nome, $cognome, $email, $id);
        }
        $stmt->execute();
        $stmt->close();
        
        $stmt = $conn->prepare("UPDATE Istruttore SET specializzazione = ?, qualifica = ? WHERE id_utente = ?");
        $stmt->bind_param("ssi", $specializzazione, $qualifica, $id);
        $stmt->execute();
        $stmt->close();
    } else {
        // Aggiunta nuovo
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO Utente (nome, cognome, email, password_hash) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nome, $cognome, $email, $password_hash);
        $stmt->execute();
        $id_utente = $conn->insert_id;
        $stmt->close();
        
        $stmt = $conn->prepare("INSERT INTO Istruttore (id_utente, specializzazione, qualifica) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $id_utente, $specializzazione, $qualifica);
        $stmt->execute();
        $stmt->close();
        
        $stmt = $conn->prepare("INSERT INTO Personal_Trainer (id_istruttore) VALUES (?)");
        $stmt->bind_param("i", $id_utente);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// API PER RECUPERO DATI EDIT (JSON)
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT Utente.id_utente, Utente.nome, Utente.cognome, Utente.email, Istruttore.specializzazione, Istruttore.qualifica
                            FROM Utente
                            JOIN Istruttore ON Utente.id_utente = Istruttore.id_utente
                            WHERE Utente.id_utente = ?");
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

// RECUPERO LISTA PER LA TABELLA
$queryPT = "SELECT u.id_utente, u.nome, u.cognome, u.email 
            FROM Utente u 
            JOIN Personal_Trainer pt ON u.id_utente = pt.id_istruttore";
$resultPT = $conn->query($queryPT);
?>
<!doctype html>
<html lang="it">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Palestra - Lista Personal Trainer</title>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/utente.css" />

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
        <h1>Gestione Personal Trainer</h1>

        <section class="user-section">
            
            <div class="table-container">
                <table class="admin-table">
                    <caption>Lista dei Personal Trainer registrati nel sistema</caption>
                    <thead>
                        <tr>
                            <th scope="col">Nome</th>
                            <th scope="col">Cognome</th>
                            <th scope="col">Email</th>
                            <th scope="col">Azione</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultPT->num_rows > 0): ?>
                            <?php while($row = $resultPT->fetch_assoc()): ?>
                                <tr>
                                    <td data-label="Nome"><?php echo htmlspecialchars($row['nome']); ?></td>
                                    <td data-label="Cognome"><?php echo htmlspecialchars($row['cognome']); ?></td>
                                    <td data-label="Email"><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td>
                                        <button type="button" 
                                                class="btn-edit-action" 
                                                onclick="editUser(<?php echo $row['id_utente']; ?>)"
                                                aria-label="Modifica <?php echo htmlspecialchars($row['nome'] . ' ' . $row['cognome']); ?>">
                                            Modifica
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="empty-msg">Nessun Personal Trainer trovato.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="actions-container">
                <button class="btn-add" onclick="showForm()">+ Aggiungi Nuovo Personal Trainer</button>
            </div>

            <div id="userForm" class="form-container">
                <h2 id="formTitle">Aggiungi Personal Trainer</h2>
                <form method="POST" action="">
                    <input type="hidden" id="userId" name="id" value="">
                    
                    <div class="form-group">
                        <label for="nome">Nome:</label>
                        <input type="text" id="nome" name="nome" required>
                    </div>

                    <div class="form-group">
                        <label for="cognome">Cognome:</label>
                        <input type="text" id="cognome" name="cognome" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password">
                    </div>

                    <div class="form-group">
                        <label for="specializzazione">Specializzazione:</label>
                        <input type="text" id="specializzazione" name="specializzazione" required>
                    </div>

                    <div class="form-group">
                        <label for="qualifica">Qualifica:</label>
                        <input type="text" id="qualifica" name="qualifica" required>
                    </div>

                    <div class="form-buttons">
                        <button type="submit" class="btn-save">Salva Dati</button>
                        <button type="button" class="btn-delete" onclick="deleteUser()">Elimina Trainer</button>
                        <button type="button" class="btn-cancel" onclick="hideForm()">Annulla</button>
                    </div>
                </form>
            </div>
        </section>
    </section>
</main>

    <script>
        function showForm() {
            document.getElementById('formTitle').textContent = 'Aggiungi Personal Trainer';
            document.getElementById('userId').value = '';
            document.getElementById('nome').value = '';
            document.getElementById('cognome').value = '';
            document.getElementById('email').value = '';
            document.getElementById('password').required = true;
            document.getElementById('specializzazione').value = '';
            document.getElementById('qualifica').value = '';
            document.getElementById('userForm').style.display = 'block';
            document.getElementById('userForm').scrollIntoView();
        }

        function editUser(id) {
            fetch('?edit=' + id)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('formTitle').textContent = 'Modifica Personal Trainer';
                    document.getElementById('userId').value = data.id_utente;
                    document.getElementById('nome').value = data.nome;
                    document.getElementById('cognome').value = data.cognome;
                    document.getElementById('email').value = data.email;
                    document.getElementById('password').required = false; // In modifica non è obbligatoria
                    document.getElementById('specializzazione').value = data.specializzazione;
                    document.getElementById('qualifica').value = data.qualifica;
                    document.getElementById('userForm').style.display = 'block';
                    document.getElementById('userForm').scrollIntoView();
                });
        }

        function deleteUser() {
            const id = document.getElementById('userId').value;
            if (id && confirm("Sei sicuro di voler eliminare definitivamente questo trainer?")) {
                window.location.href = '?delete=' + id;
            }
        }

        function hideForm() {
            document.getElementById('userForm').style.display = 'none';
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>