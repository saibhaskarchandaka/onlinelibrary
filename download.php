<?php
session_start();
// Database Connection
$host = "localhost";
$username = "root";
$password = "";
$database = "library";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

if (!isset($_GET['file']) || empty($_GET['file'])) {
    die("Invalid file request");
}

try {
    $stmt = $pdo->prepare("SELECT * FROM ebooks WHERE file_path = ?");
    $stmt->execute([$_GET['file']]);
    $ebook = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$ebook) {
        die("E-book not found");
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

$file_path = "uploads/ebooks/" . $ebook['file_path'];
if (!file_exists($file_path)) {
    die("File not found on server");
}

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $ebook['title'] . '.pdf"');
header('Content-Length: ' . filesize($file_path));
header('Content-Transfer-Encoding: binary');
header('Accept-Ranges: bytes');

@readfile($file_path);