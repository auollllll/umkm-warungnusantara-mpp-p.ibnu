<?php
require_once 'config.php';

// Cek jika file diunggah
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['image'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];
    $fileType = $file['type'];
    
    // Validasi tipe file
    $allowedTypes = array('jpg', 'jpeg', 'png', 'gif');
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    if (!in_array($fileExt, $allowedTypes)) {
        http_response_code(400);
        echo json_encode(array("message" => "Format file tidak diizinkan"));
        exit();
    }
    
    // Validasi ukuran file (maksimal 2MB)
    if ($fileSize > 2 * 1024 * 1024) {
        http_response_code(400);
        echo json_encode(array("message" => "Ukuran file terlalu besar"));
        exit();
    }
    
    // Buat nama file unik
    $newFileName = uniqid('product_', true) . '.' . $fileExt;
    
    // Tentukan direktori upload
    $uploadDir = 'uploads/';
    
    // Buat direktori jika belum ada
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // Pindahkan file ke direktori upload
    $destination = $uploadDir . $newFileName;
    
    if (move_uploaded_file($fileTmpName, $destination)) {
        // Kembalikan URL file
        $fileUrl = $uploadDir . $newFileName;
        echo json_encode(array("message" => "File berhasil diunggah", "url" => $fileUrl));
    } else {
        http_response_code(500);
        echo json_encode(array("message" => "Gagal mengunggah file"));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Tidak ada file yang diunggah"));
}
?>