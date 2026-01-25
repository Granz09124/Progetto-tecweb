<?php
// TEMP
session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    require "./error/405.php";
    exit();
}

require_once "db_connection.php";

// if (
//     !isset($_SESSION["user_id"]) ||
//     !isset($_SESSION["user_tipo"]) ||
//     $_SESSION["user_tipo"] !== "pt"
// ) {
//     http_response_code(403);
//     echo "Solo un personal trainer può caricare schede";
//     exit();
// }

if (!isset($_FILES["scheda_file"])) {
    http_response_code(400);
    echo "Non è stata caricata alcuna scheda";
    exit();
}

if (!isset($_POST["id_cliente"])) {
    http_response_code(400);
    echo "Non è stato specificato il cliente destinatario";
    exit();
}

$idUtente = $_SESSION["user_id"];
$idCliente = $_POST["id_cliente"];
$file = $_FILES["scheda_file"];
$fileSize = $file["size"];
$maxFileSize = 1_048_576; // 1MiB

if ($fileSize > $maxFileSize || $file["size"] === 0) {
    http_response_code(413);
    echo "Il file caricato è troppo grande (" .
        $file["size"] .
        "). La sua dimensione deve essere inferiore a 1MB.";
    exit();
}

$fileName = $file["name"];
$target_dir = "uploads/";

$allowedFileType = "pdf";
$allowedMimeType = "application/pdf";

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file["tmp_name"]);
$extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

if ($extension !== $allowedFileType || $mimeType !== $allowedMimeType) {
    http_response_code(400);
    echo "Il file caricato non è un file PDF";
    exit();
}

$uploadedFileName = $_POST["id_cliente"] . ".pdf";

if (move_uploaded_file($file["tmp_name"], $target_dir . $uploadedFileName)) {
    // Se è presente una scheda per il cliente, cancellala
    // Carica la scheda per il cliente
    $stmt = $conn->prepare(
        "UPDATE Assegnazione_PT
        SET data_caricamento = ?, dimensione_file = ?
        WHERE id_cliente = ?",
    );

    $uploadTime = date("Y-m-d H:i:s");
    $stmt->bind_param("sii", $uploadTime, $fileSize, $idCliente);

    if ($stmt->execute()) {
        $stmt->close();
    } else {
        http_response_code(500);
        exit();
    }
} else {
    http_response_code(500);
    exit();
}
