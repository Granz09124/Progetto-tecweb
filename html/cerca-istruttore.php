<?php
$conn = new mysqli("db", "root", "example", "palestra_db", 3306);

$top = file_get_contents("internal/cerca-istruttore/top.html");
$templateResult = file_get_contents("internal/cerca-istruttore/stack.html");
$bottom = file_get_contents("internal/cerca-istruttore/bottom.html");

$nome = '';
if (isset($_GET['nome'])) {
    $nome = $_GET['nome'];
    $nome = trim($nome);
    $nome = htmlspecialchars($nome);
}

$top = str_replace("[Valore Ricerca]", $nome, $top);

echo $top;

$query = "SELECT *
    FROM Utente
    JOIN Istruttore ON Utente.id_utente = Istruttore.id_utente
    LEFT JOIN Personal_Trainer ON Utente.id_utente = Personal_Trainer.id_istruttore
    WHERE nome LIKE ?";
$stmt = $conn->prepare($query);

$param = "%" . $nome . "%";
$stmt->bind_param("s", $param);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Nessun risultato trovato.";
}

while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    $output = $templateResult;

    $isPt = $row['id_istruttore'] == null ? '&cross;' : '&check;';

    $output = str_replace("[Nome Istruttore]", $row['nome'] . ' ' . $row['cognome'], $output);
    $output = str_replace("[È PT]", $isPt, $output);
    $output = str_replace("[Specializzazione Istruttore]", $row['specializzazione'], $output);
    $output = str_replace("[ID Istruttore]", $row['id_utente'], $output);
    
    echo $output;
}

echo $bottom;
