<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    require './error/405.php';
    exit;
}

require_once 'db_connection.php';

// TODO controllare il ruolo di chi sta cercando di caricare la scheda e relazione con chi la riceve
// if (!isset($_SESSION['id_utente'])) {
    // http_response_code(403);
    // echo "Solo un personal trainer può caricare schede";
    // exit;
// }

$idUtente = $_SESSION['id_utente'] ?? 9;

if (!isset($_FILES['scheda_file'])) {
    http_response_code(400);
    echo 'Non è stata caricata alcuna scheda';
    exit;
}
if (!isset($_POST['id_cliente'])) {
    http_response_code(400);
    echo 'Non è stato specificato il cliente destinatario';
    exit;
}
$idCliente = $_POST['id_cliente'];

$file = $_FILES['scheda_file'];

$maxFileSize = 1_048_576; // 1MiB

if ($file['size'] > $maxFileSize || $file['size'] === 0) {
    http_response_code(413);
    echo "Il file caricato è troppo grande (" . $file['size'] . "). La sua dimensione deve essere inferiore a 1MB.";
    exit;
}

$fileName = $file['name'];
$target_dir = "uploads/";

$allowedFileType = 'pdf';
$allowedMimeType = 'application/pdf';

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file["tmp_name"]);
$extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

if($extension !== $allowedFileType || $mimeType !== $allowedMimeType) {
    http_response_code(400);
    echo "Il file caricato non è un file PDF";
    exit;
}

$uploadedFileName = $_POST['id_cliente'] . '.pdf';

if (move_uploaded_file($file["tmp_name"], $target_dir . $uploadedFileName)) {
    // Se è presente una scheda per il cliente, cancellala
    $deleteScheda = $conn->prepare("DELETE FROM Scheda_Allenamento WHERE id_cliente = ?");
    $deleteScheda->bind_param('i', $_POST['id_cliente']);

    if ($deleteScheda->execute()) {
        $deleteScheda->close();
    } else {
        http_response_code(500);
        exit;
    }

    // Carica la scheda per il cliente
    $stmt = $conn->prepare("INSERT INTO Scheda_Allenamento (id_pt, id_cliente, nome_file, percorso_file) VALUES (?, ?, ?, ?)");
    
    $stmt->bind_param("iiss", $idUtente, $idCliente, $uploadedFileName, $target_dir);
    
    if($stmt->execute()) {
        $stmt->close();
    } else {
        http_response_code(500);
        exit;
    }
} else {
    http_response_code(500);
    exit;
}

header('Location: /lista-clienti-pt.php');