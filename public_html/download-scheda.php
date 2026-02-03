<?php
session_start();

require __DIR__ . "/../internal/db_connection.php";

if (!isset($_SESSION['user_id']))
{
    header('Location: login.php');
    exit();
}

if (!isset($_SESSION['user_tipo']) || $_SESSION['user_tipo'] !== 'cliente')
{
    header('Location: area-personale.php');
    exit();
}

$stmt = $conn->prepare(
    "SELECT id_pt, id_cliente
    FROM Assegnazione_PT
    WHERE id_cliente = ?"
);

$userIdInt = filter_var($_SESSION['user_id'], FILTER_VALIDATE_INT);

$stmt->bind_param('i', $userIdInt);

if ($stmt->execute())
{
    $result = $stmt->get_result();

    $idPt = $result->fetch_assoc()['id_pt'];
    

    $fileScheda = __DIR__ . "/../internal/utente/utente-semplice/uploads/schede/" . $_SESSION['user_id'] . '_' . $idPt . '.pdf';

    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="scheda-personal-trainer.pdf"');
    header('Content-Length: ' . filesize($fileScheda));
    readFile($fileScheda);
}
else
{
    http_response_code(500);
}

?>
