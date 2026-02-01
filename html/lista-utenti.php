<?php
session_start();
require_once __DIR__ . '/../internal/db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require __DIR__ . '/../internal/header.php';

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($id == $_SESSION['user_id']) {
        echo "<script>alert('Non puoi eliminare il tuo account da qui.'); window.location.href='lista-utenti.php';</script>";
        exit;
    }
    
    $stmt = $conn->prepare("DELETE FROM Utente WHERE id_utente = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute(); $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = !empty($_POST['id']) ? intval($_POST['id']) : 0;
    $nome = trim($_POST['nome'] ?? '');
    $cognome = trim($_POST['cognome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $errori = [];

    if (empty($nome) || strlen($nome) < 2 || !preg_match("/^[a-zA-ZÀ-ÿ' -]+$/u", $nome)) {
        $errori[] = "Nome non valido (min 2 caratteri, solo lettere).";
    } elseif (strlen($nome) > 50) $errori[] = "Nome troppo lungo.";

    if (empty($cognome) || strlen($cognome) < 2 || !preg_match("/^[a-zA-ZÀ-ÿ' -]+$/u", $cognome)) {
        $errori[] = "Cognome non valido (min 2 caratteri, solo lettere).";
    } elseif (strlen($cognome) > 50) $errori[] = "Cognome troppo lungo.";

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errori[] = "Email non valida.";
    } elseif (strlen($email) > 100) $errori[] = "Email troppo lunga.";

    if ($id == 0 && empty($password)) $errori[] = "Password obbligatoria per nuovi utenti.";
    if (!empty($password)) {
        if (strlen($password) < 8 || strlen($password) > 72 || !preg_match("/^[\x20-\x7E]+$/", $password)) {
            $errori[] = "Password non valida (min 8 char).";
        }
    }

    if (empty($errori)) {
        $stmtCheck = $conn->prepare("SELECT id_utente FROM Utente WHERE email = ? AND id_utente != ?");
        $stmtCheck->bind_param("si", $email, $id);
        $stmtCheck->execute();
        if ($stmtCheck->get_result()->num_rows > 0) {
            $errori[] = "Email già in uso.";
        }
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
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT id_utente, nome, cognome, email FROM Utente WHERE id_utente = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) echo json_encode($result->fetch_assoc());
    else echo json_encode(['error' => 'Non trovato']);
    exit;
}

$tableRows = "";
$result = $conn->query("SELECT id_utente, nome, cognome, email FROM Utente");
while($row = $result->fetch_assoc()) {
    $tableRows .= "<tr>";
    $tableRows .= "<td>" . htmlspecialchars($row['nome']) . "</td>";
    $tableRows .= "<td>" . htmlspecialchars($row['cognome']) . "</td>";
    $tableRows .= "<td>" . htmlspecialchars($row['email']) . "</td>";
    $tableRows .= "<td><button type='button' class='btn-modify' onclick='editUser(" . $row['id_utente'] . ")'>Modifica</button></td>";
    $tableRows .= "</tr>";
}

$top = file_get_contents(__DIR__ . "/../internal/utente/top.html");
$body = file_get_contents(__DIR__ . "/../internal/utente/lista-utenti/body.html");
$bottom = file_get_contents(__DIR__ . "/../internal/utente/bottom.html");

$top = str_replace("[PageTitle]", "Lista Utenti Totali - Admin", $top);
$breadcrumb = '<li><a lang="en" href="./home.php">Home</a></li>';
$breadcrumb .= '<li><a href="./area-personale.php">Area Personale</a></li>';
$breadcrumb .= '<li aria-current="page">Lista Utenti Totali</li>';
$top = str_replace("[Breadcrumb]", $breadcrumb, $top);

$body = str_replace("[TableRows]", $tableRows, $body);

$customScript = '<script src="../javascript/formLista.js"></script>';
$bottom = str_replace("</body>", $customScript . "</body>", $bottom);

renderFromHtml($top . $body . $bottom);
$conn->close();
?>