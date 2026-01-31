<?php
session_start();
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require __DIR__ . '/../internal/header.php';

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
    $id = !empty($_POST['id']) ? intval($_POST['id']) : 0;
    $nome = trim($_POST['nome'] ?? '');
    $cognome = trim($_POST['cognome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $specializzazione = trim($_POST['specializzazione'] ?? '');
    $qualifica = trim($_POST['qualifica'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');

    $errori = [];

    if (empty($nome) || strlen($nome) < 2 || !preg_match("/^[a-zA-ZÀ-ÿ' -]+$/u", $nome)) $errori[] = "Nome non valido.";
    elseif (strlen($nome) > 50) $errori[] = "Nome troppo lungo.";
    
    if (empty($cognome) || strlen($cognome) < 2 || !preg_match("/^[a-zA-ZÀ-ÿ' -]+$/u", $cognome)) $errori[] = "Cognome non valido.";
    elseif (strlen($cognome) > 50) $errori[] = "Cognome troppo lungo.";

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errori[] = "Email non valida.";
    elseif (strlen($email) > 100) $errori[] = "Email troppo lunga.";

    if ($id == 0 && empty($password)) $errori[] = "Password obbligatoria per nuovi PT.";
    if (!empty($password)) {
        if (strlen($password) < 8 || strlen($password) > 72 || !preg_match("/^[\x20-\x7E]+$/", $password)) {
            $errori[] = "Password non valida (min 8 char).";
        }
    }

    if (empty($specializzazione) || strlen($specializzazione) > 50) $errori[] = "Specializzazione non valida o troppo lunga.";
    if (empty($qualifica) || strlen($qualifica) > 50) $errori[] = "Qualifica non valida o troppo lunga.";

    if (empty($errori)) {
        $stmtCheck = $conn->prepare("SELECT id_utente FROM Utente WHERE email = ? AND id_utente != ?");
        $stmtCheck->bind_param("si", $email, $id);
        $stmtCheck->execute();
        if ($stmtCheck->get_result()->num_rows > 0) $errori[] = "Email già in uso.";
        $stmtCheck->close();
    }

    if (!empty($errori)) {
        echo "<script>alert('" . addslashes($errori[0]) . "'); window.history.back();</script>";
        exit;
    }

    if ($id > 0) {
        if (!empty($password)) {
            $password_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            $stmt = $conn->prepare("UPDATE Utente SET nome = ?, cognome = ?, email = ?, password_hash = ? WHERE id_utente = ?");
            $stmt->bind_param("ssssi", $nome, $cognome, $email, $password_hash, $id);
        } else {
            $stmt = $conn->prepare("UPDATE Utente SET nome = ?, cognome = ?, email = ? WHERE id_utente = ?");
            $stmt->bind_param("sssi", $nome, $cognome, $email, $id);
        }
        $stmt->execute(); $stmt->close();
        
        $stmt = $conn->prepare("UPDATE Istruttore SET specializzazione = ?, qualifica = ?, telefono = ? WHERE id_utente = ?");
        $stmt->bind_param("sssi", $specializzazione, $qualifica, $telefono, $id);
        $stmt->execute(); $stmt->close();
    } else {
        $password_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        
        $stmt = $conn->prepare("INSERT INTO Utente (nome, cognome, email, password_hash) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nome, $cognome, $email, $password_hash);
        $stmt->execute();
        $id_utente = $conn->insert_id;
        $stmt->close();
        
        $stmt = $conn->prepare("INSERT INTO Istruttore (id_utente, specializzazione, qualifica, telefono) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $id_utente, $specializzazione, $qualifica, $telefono);
        $stmt->execute(); $stmt->close();

        $stmt = $conn->prepare("INSERT INTO Personal_Trainer (id_istruttore) VALUES (?)");
        $stmt->bind_param("i", $id_utente);
        $stmt->execute(); $stmt->close();
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT u.id_utente, u.nome, u.cognome, u.email, i.specializzazione, i.qualifica, i.telefono 
                            FROM Utente u JOIN Istruttore i ON u.id_utente = i.id_utente 
                            WHERE u.id_utente = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) echo json_encode($result->fetch_assoc());
    else echo json_encode(['error' => 'Non trovato']);
    exit;
}

$tableRows = "";
$result = $conn->query("SELECT u.id_utente, u.nome, u.cognome, u.email FROM Utente u JOIN Personal_Trainer pt ON u.id_utente = pt.id_istruttore");
while($row = $result->fetch_assoc()) {
    $tableRows .= "<tr>";
    $tableRows .= "<td>" . htmlspecialchars($row['nome']) . "</td>";
    $tableRows .= "<td>" . htmlspecialchars($row['cognome']) . "</td>";
    $tableRows .= "<td>" . htmlspecialchars($row['email']) . "</td>";
    $tableRows .= "<td><button type='button' class='btn-modify' onclick='editUser(" . $row['id_utente'] . ")'>Modifica</button></td>";
    $tableRows .= "</tr>";
}

$top = file_get_contents(__DIR__ . "/../internal/utente/top.html");
$body = file_get_contents(__DIR__ . "/../internal/utente/lista-pt/body.html");
$bottom = file_get_contents(__DIR__ . "/../internal/utente/bottom.html");

$top = str_replace("[PageTitle]", "Gestione PT - Admin", $top);
$breadcrumb = "Ti trovi in: <a href='./home.php'>Home</a> >> <a href='./area-personale.php'>Area Personale</a> >> Gestione PT";
$top = str_replace("[Breadcrumb]", $breadcrumb, $top);

$body = str_replace("[TableRows]", $tableRows, $body);

$customScript = '<script src="../javascript/formLista.js"></script>';
$bottom = str_replace("</body>", $customScript . "</body>", $bottom);

renderFromHtml($top . $body . $bottom);
$conn->close();
?>