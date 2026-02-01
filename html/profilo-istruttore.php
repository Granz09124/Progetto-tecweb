<?php
require_once "../internal/db_connection.php";
require_once '../internal/header.php';

$templateIstruttore = file_get_contents('../internal/profilo-istruttore.html');

$idIstruttore = -1;

if (isset($_GET['id'])) {
    $idIstruttore = (int)$_GET['id'];
}

$query = "SELECT *
    FROM Istruttore
    JOIN Utente on Utente.id_utente = Istruttore.id_utente
    LEFT JOIN Personal_Trainer on Istruttore.id_utente = Personal_Trainer.id_istruttore
    WHERE Utente.id_utente = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $idIstruttore);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    require_once "error/404.php";
}

while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    $output = $templateIstruttore;

    $isPt = $row['id_istruttore'] == null ? '&cross;' : '&check;';

    $output = str_replace("[ID Istruttore]", $idIstruttore, $output);
    $output = str_replace("[Nome Istruttore]", htmlspecialchars($row['nome'] . ' ' . $row['cognome']), $output);
    $output = str_replace("[È PT]", $isPt, $output);
    $output = str_replace("[Specializzazione Istruttore]", htmlspecialchars($row['specializzazione']), $output);
    $output = str_replace("[Qualifica Istruttore]", htmlspecialchars($row['qualifica']), $output);
    $output = str_replace("[Presentazione Istruttore]", htmlspecialchars($row['presentazione']), $output);
    
    renderFromHtml($output);
}
