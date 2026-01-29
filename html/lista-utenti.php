<?php
require_once 'config.php';
require 'header.php';

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM Utente WHERE id_utente = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute(); $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $cognome = $_POST['cognome'];
    $email = $_POST['email'];
    $password = $_POST['password'];

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
    }
    $stmt->execute(); $stmt->close();
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

$top = file_get_contents("internal/utente/top.html");
$body = file_get_contents("internal/utente/lista-utenti/body.html");
$bottom = file_get_contents("internal/utente/bottom.html");

$top = str_replace("[PageTitle]", "Lista Utenti Totali - Admin", $top);
$breadcrumb = "Ti trovi in: <a href='./home.php'>Home</a> >> <a href='./area-personale.php'>Area Personale</a> >> Lista Utenti Totali";
$top = str_replace("[Breadcrumb]", $breadcrumb, $top);

$body = str_replace("[TableRows]", $tableRows, $body);

$customScript = '<script src="../javascript/formLista.js"></script>';
$bottom = str_replace("</body>", $customScript . "</body>", $bottom);

renderFromHtml($top . $body . $bottom);
$conn->close();
?>