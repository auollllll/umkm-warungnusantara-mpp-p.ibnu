<?php
// Koneksi ke database
 $host = "localhost";
 $user = "root"; // Ganti dengan username database Anda
 $password = ""; // Ganti dengan password database Anda
 $dbname = "warung_nusantara";

// Membuat koneksi
 $conn = new mysqli($host, $user, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Set charset ke utf8mb4
 $conn->set_charset("utf8mb4");

// Set header untuk允许跨域请求
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}
?>