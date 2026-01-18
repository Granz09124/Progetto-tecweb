<?php

$host = 'db'; 
$db   = 'palestra_db';
$user = 'root'; 
$pass = 'example'; 

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function uploadScheda($file, $id_cliente, $id_pt, $conn) {

    $target_dir = "uploads/"; 
    
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $fileName = basename($file["name"]);
    $target_file = $target_dir . time() . "_" . $fileName; 
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    $allowed = ['pdf', 'jpg', 'jpeg', 'txt'];
    if(!in_array($fileType, $allowed)) {
        return "Errore: Solo file PDF, JPG, JPEG e TXT sono permessi.";
    }

    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO Scheda_Allenamento (id_pt, id_cliente, nome_file, percorso_file) VALUES (?, ?, ?, ?)");
        
        $stmt->bind_param("iiss", $id_pt, $id_cliente, $fileName, $target_file);
        
        if($stmt->execute()) {
            $stmt->close();
            return "Successo: File caricato correttamente.";
        } else {
            return "Errore DB: " . $stmt->error;
        }
    } else {
        return "Errore nel caricamento fisico del file.";
    }
}
?>