<?php
require_once 'config.php';

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
    $codice_fiscale = $_POST['codice_fiscale'];
    $telefono = $_POST['telefono'];

    if (!empty($_POST['id'])) {

        $id = intval($_POST['id']);
        if (!empty($password)) {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE Utente SET nome = ?, cognome = ?, email = ?, password_hash = ? WHERE id_utente = ?");
            $stmt->bind_param("ssssi", $nome, $cognome, $email, $password_hash, $id);
        } else {
            $stmt = $conn->prepare("UPDATE Utente SET nome = ?, cognome = ?, email = ? WHERE id_utente = ?");
            $stmt->bind_param("sssi", $nome, $cognome, $email, $id);
        }
        $stmt->execute(); $stmt->close();

        $stmt = $conn->prepare("UPDATE Cliente SET codice_fiscale = ?, telefono = ? WHERE id_utente = ?");
        $stmt->bind_param("ssi", $codice_fiscale, $telefono, $id);
        $stmt->execute(); $stmt->close();
    } 
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT u.id_utente, u.nome, u.cognome, u.email, c.codice_fiscale, c.telefono 
                            FROM Utente u JOIN Cliente c ON u.id_utente = c.id_utente 
                            WHERE u.id_utente = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) echo json_encode($result->fetch_assoc());
    else echo json_encode(['error' => 'Non trovato']);
    exit;
}

$result = $conn->query("SELECT u.id_utente, u.nome, u.cognome, u.email FROM Utente u JOIN Cliente c ON u.id_utente = c.id_utente");
?>
<!doctype html>
<html lang="it">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Gestione Clienti - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/utente.css" />
    <link rel="stylesheet" media="screen and (max-width:768px)" href="../css/mini.css">
</head>
<body id="layout-adhoc">
    <header class="intestazione">
        <div class="intestazione-bg">
            <a href="./home.html"><img src="../immagini/Logo_palestra.png" alt="Home" class="logo"></a>
            <h1>Il Tempio di Apollo</h1>
        </div>
        <nav id="menu" aria-label="menu">
            <input type="checkbox" id="menu-toggle">
            <div id="hamburger-menu">
                <span id="ham-line1"></span><span id="ham-line2"></span><span id="ham-line3"></span>
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
        <p>Ti trovi in: <a href="./home.html">Home</a> >> <a href="./utente-admin.php">Area Admin</a> >> Gestione Clienti</p>
    </nav>

    <main class="contenuto-principale">
        <section class="area-personale">
            <h1>Lista Iscritti (Clienti)</h1>

            <section class="user-section">
                <p id="sum">La tabella contiene l’elenco degli iscritti attuali alla palestra.
                    Per ogni iscritto ci sono le varie colonne che indicano il nome, il cognome e l’indirizzo email.
                    Accanto ai dati di ogni persona è disponibile una colonna azione dove si trova un pulsante chiamato “Modifica”, che consente di aggiornare le informazioni dell’iscritto selezionato.
                <table aria-describedby="sum" class="admin-table">
                    <thead>
                        <tr>
                            <th scope="col">Nome</th>
                            <th scope="col">Cognome</th>
                            <th scope="col">Email</th>
                            <th scope="col">Azione</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['nome']); ?></td>
                            <td><?php echo htmlspecialchars($row['cognome']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td>
                                <button type="button" class="btn-modify" onclick="editUser(<?php echo $row['id_utente']; ?>)">Modifica</button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </section>

            <section class="user-section" id="formSection" style="display: none;">
                <h2 id="formTitle">Modifica Cliente</h2>
                <form method="POST" action="">
                    <input type="hidden" id="userId" name="id" value="">
                    
                    <div class="tech-form-group">
                        <div class="data-item tech-input-wrapper">
                            <label>Nome</label>
                            <input type="text" id="nome" name="nome" required>
                        </div>
                        <div class="data-item tech-input-wrapper">
                            <label>Cognome</label>
                            <input type="text" id="cognome" name="cognome" required>
                        </div>
                    </div>

                    <div class="tech-form-group">
                        <div class="data-item tech-input-wrapper">
                            <label>Email</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="data-item tech-input-wrapper">
                            <label>Password (Opzionale)</label>
                            <input type="password" id="password" name="password" placeholder="********">
                        </div>
                    </div>

                    <div class="tech-form-group">
                        <div class="data-item tech-input-wrapper">
                            <label>Codice Fiscale</label>
                            <input type="text" id="codice_fiscale" name="codice_fiscale" required>
                        </div>
                        <div class="data-item tech-input-wrapper">
                            <label>Telefono</label>
                            <input type="text" id="telefono" name="telefono">
                        </div>
                    </div>

                    <section class="account-actions">
                        <button type="submit" class="btn-modify btn-save">Salva</button>
                        <button type="button" class="btn-delete" onclick="deleteUser('Cliente')" id="btnDelete">Elimina</button>
                        <button type="button" class="btn-modify" onclick="hideForm()">Annulla</button>
                    </section>
                </form>
            </section>

            <section class="account-actions">
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
    <script src="../javascript/formLista.js"></script>

</body>
</html>
<?php $conn->close(); ?>