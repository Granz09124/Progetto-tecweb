<?php
require_once __DIR__ . '/config.php';
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