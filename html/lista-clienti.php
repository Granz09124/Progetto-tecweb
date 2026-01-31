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
    $codice_fiscale = strtoupper(trim($_POST['codice_fiscale'] ?? ''));
    $telefono = trim($_POST['telefono'] ?? '');

    $errori = [];

    if (empty($nome) || strlen($nome) < 2) {
        $errori[] = "Il nome deve contenere almeno 2 caratteri.";
    } elseif (!preg_match("/^[a-zA-ZÀ-ÿ' -]+$/u", $nome)) {
        $errori[] = "Il nome contiene caratteri non validi.";
    } elseif (strlen($nome) > 50) {
        $errori[] = "Il nome è troppo lungo.";
    }

    if (empty($cognome) || strlen($cognome) < 2) {
        $errori[] = "Il cognome deve contenere almeno 2 caratteri.";
    } elseif (!preg_match("/^[a-zA-ZÀ-ÿ' -]+$/u", $cognome)) {
        $errori[] = "Il cognome contiene caratteri non validi.";
    } elseif (strlen($cognome) > 50) {
        $errori[] = "Il cognome è troppo lungo.";
    }

    if (empty($email)) {
        $errori[] = "L'email è obbligatoria.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errori[] = "Formato email non valido.";
    } elseif (strlen($email) > 100) {
        $errori[] = "L'email è troppo lunga.";
    }

    if (empty($codice_fiscale)) {
        $errori[] = "Il codice fiscale è obbligatorio.";
    } elseif (!preg_match("/^[A-Z0-9]{16}$/", $codice_fiscale)) {
        $errori[] = "Il codice fiscale deve essere di esattamente 16 caratteri alfanumerici.";
    }

    if ($id == 0 && empty($password)) {
        $errori[] = "La password è obbligatoria per i nuovi utenti.";
    }
    if (!empty($password)) {
        if (strlen($password) < 8) {
            $errori[] = "La password deve essere di almeno 8 caratteri.";
        } elseif (strlen($password) > 72) {
            $errori[] = "La password è troppo lunga.";
        } elseif (!preg_match("/^[\x20-\x7E]+$/", $password)) {
            $errori[] = "La password contiene caratteri non validi.";
        }
    }

    if (empty($errori)) {
        $sqlEmail = "SELECT id_utente FROM Utente WHERE email = ? AND id_utente != ?";
        $stmtEmail = $conn->prepare($sqlEmail);
        $stmtEmail->bind_param("si", $email, $id);
        $stmtEmail->execute();
        if ($stmtEmail->get_result()->num_rows > 0) {
            $errori[] = "Questa email è già registrata.";
        }
        $stmtEmail->close();

        $sqlCF = "SELECT id_utente FROM Cliente WHERE codice_fiscale = ? AND id_utente != ?";
        $stmtCF = $conn->prepare($sqlCF);
        $stmtCF->bind_param("si", $codice_fiscale, $id);
        $stmtCF->execute();
        if ($stmtCF->get_result()->num_rows > 0) {
            $errori[] = "Questo codice fiscale è già registrato.";
        }
        $stmtCF->close();
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

        $stmt = $conn->prepare("UPDATE Cliente SET codice_fiscale = ?, telefono = ? WHERE id_utente = ?");
        $stmt->bind_param("ssi", $codice_fiscale, $telefono, $id);
        $stmt->execute(); $stmt->close();

    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT u.id_utente, u.nome, u.cognome, u.email, c.codice_fiscale, c.telefono FROM Utente u JOIN Cliente c ON u.id_utente = c.id_utente WHERE u.id_utente = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) echo json_encode($result->fetch_assoc());
    else echo json_encode(['error' => 'Non trovato']);
    exit;
}

$tableRows = "";
$result = $conn->query("SELECT u.id_utente, u.nome, u.cognome, u.email FROM Utente u JOIN Cliente c ON u.id_utente = c.id_utente");
while($row = $result->fetch_assoc()) {
    $tableRows .= "<tr>";
    $tableRows .= "<td>" . htmlspecialchars($row['nome']) . "</td>";
    $tableRows .= "<td>" . htmlspecialchars($row['cognome']) . "</td>";
    $tableRows .= "<td>" . htmlspecialchars($row['email']) . "</td>";
    $tableRows .= "<td><button type='button' class='btn-modify' onclick='editUser(" . $row['id_utente'] . ")'>Modifica</button></td>";
    $tableRows .= "</tr>";
}

$top = file_get_contents(__DIR__ . "/../internal/utente/top.html");
$body = file_get_contents(__DIR__ . "/../internal/utente/lista-clienti/body.html");
$bottom = file_get_contents(__DIR__ . "/../internal/utente/bottom.html");

$top = str_replace("[PageTitle]", "Gestione Clienti - Admin", $top);
$breadcrumb = "Ti trovi in: <a href='./home.php'>Home</a> >> <a href='./area-personale.php'>Area Personale</a> >> Gestione Clienti";
$top = str_replace("[Breadcrumb]", $breadcrumb, $top);

$body = str_replace("[TableRows]", $tableRows, $body);

$customScript = '<script src="../javascript/formLista.js"></script>';
$bottom = str_replace("</body>", $customScript . "</body>", $bottom);

renderFromHtml($top . $body . $bottom);
$conn->close();
?>