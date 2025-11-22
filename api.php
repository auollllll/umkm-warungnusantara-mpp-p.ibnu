<?php
header('Content-Type: application/json');
require_once 'koneksi.php';

// Fungsi untuk mengirim respons JSON
function sendResponse($status, $message, $data = null) {
    $response = [
        'status' => $status,
        'message' => $message
    ];
    if ($data !== null) {
        $response['data'] = $data;
    }
    echo json_encode($response);
    exit;
}

// ... (kode login, logout, checkAuth, getOrders, dll. tetap sama) ...

// GET PRODUCTS (untuk user dan admin)
if ($method === 'GET' && $action === 'getProducts') {
    $sql = "SELECT * FROM products ORDER BY id DESC";
    $result = $conn->query($sql);
    $products = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
    sendResponse(true, 'Produk berhasil diambil', $products);
}

// ADD PRODUCT (dengan upload gambar)
if ($method === 'POST' && $action === 'addProduct') {
    session_start();
    if (!isset($_SESSION['admin_id'])) {
        sendResponse(false, 'Anda tidak memiliki izin');
    }

    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    
    $imagePath = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'admin/uploads/products/';
        $fileName = basename($_FILES['image']['name']);
        $targetFilePath = $uploadDir . time() . '_' . $fileName; // Nama file unik
        
        // Validasi tipe file
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
        $allowTypes = array('jpg', 'jpeg', 'png', 'gif');
        if (in_array($fileType, $allowTypes)) {
            // Pindahkan file ke folder upload
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
                $imagePath = $targetFilePath;
            } else {
                sendResponse(false, 'Gagal mengupload gambar.');
            }
        } else {
            sendResponse(false, 'Hanya file JPG, JPEG, PNG, & GIF yang diizinkan.');
        }
    } else {
        // Jika tidak ada gambar diupload, gunakan gambar default
        $imagePath = 'https://picsum.photos/seed/default/300/200.jpg';
    }

    $sql = "INSERT INTO products (name, category, price, image, description) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiss", $name, $category, $price, $imagePath, $description);
    
    if ($stmt->execute()) {
        sendResponse(true, 'Produk berhasil ditambahkan');
    } else {
        sendResponse(false, 'Gagal menambah produk: ' . $conn->error);
    }
}

// UPDATE PRODUCT (dengan upload gambar)
if ($method === 'POST' && $action === 'updateProduct') {
    session_start();
    if (!isset($_SESSION['admin_id'])) {
        sendResponse(false, 'Anda tidak memiliki izin');
    }

    $id = $_POST['id'];
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    
    // Ambil data gambar lama
    $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $oldProduct = $result->fetch_assoc();
    $imagePath = $oldProduct['image'];

    // Cek apakah ada gambar baru yang diupload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'admin/uploads/products/';
        $fileName = basename($_FILES['image']['name']);
        $targetFilePath = $uploadDir . time() . '_' . $fileName;
        
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
        $allowTypes = array('jpg', 'jpeg', 'png', 'gif');
        
        if (in_array($fileType, $allowTypes)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
                // Hapus gambar lama jika bukan gambar default
                if ($oldProduct['image'] !== 'https://picsum.photos/seed/default/300/200.jpg' && file_exists($oldProduct['image'])) {
                    unlink($oldProduct['image']);
                }
                $imagePath = $targetFilePath;
            } else {
                sendResponse(false, 'Gagal mengupload gambar baru.');
            }
        } else {
            sendResponse(false, 'Hanya file JPG, JPEG, PNG, & GIF yang diizinkan.');
        }
    }

    $sql = "UPDATE products SET name=?, category=?, price=?, image=?, description=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssissi", $name, $category, $price, $imagePath, $description, $id);
    
    if ($stmt->execute()) {
        sendResponse(true, 'Produk berhasil diperbarui');
    } else {
        sendResponse(false, 'Gagal memperbarui produk: ' . $conn->error);
    }
}

// ... (kode deleteProduct, addOrder, dll. tetap sama) ...

// Tutup koneksi
 $conn->close();
?>