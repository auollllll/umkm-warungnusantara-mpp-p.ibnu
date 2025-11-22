<?php
require_once 'config.php';

// Mendapatkan metode request
 $method = $_SERVER['REQUEST_METHOD'];

// Mendapatkan ID pesanan jika ada
 $id = isset($_GET['id']) ? $_GET['id'] : null;

switch ($method) {
    case 'GET':
        if ($id) {
            // Mendapatkan pesanan berdasarkan ID
            $sql = "SELECT * FROM orders WHERE id = $id";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                $order = $result->fetch_assoc();
                echo json_encode($order);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Pesanan tidak ditemukan"));
            }
        } else {
            // Mendapatkan semua pesanan
            $sql = "SELECT * FROM orders ORDER BY date DESC";
            $result = $conn->query($sql);
            
            $orders = array();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $orders[] = $row;
                }
            }
            
            echo json_encode($orders);
        }
        break;
        
    case 'POST':
        // Menambah pesanan baru
        $data = json_decode(file_get_contents("php://input"));
        
        $customerName = $data->customerName;
        $customerWhatsapp = $data->customerWhatsapp;
        $items = json_encode($data->items);
        $total = $data->total;
        $status = $data->status;
        $date = $data->date;
        
        $sql = "INSERT INTO orders (customerName, customerWhatsapp, items, total, status, date) VALUES ('$customerName', '$customerWhatsapp', '$items', $total, '$status', '$date')";
        
        if ($conn->query($sql) === TRUE) {
            $last_id = $conn->insert_id;
            echo json_encode(array("id" => $last_id, "message" => "Pesanan berhasil ditambahkan"));
        } else {
            http_response_code(500);
            echo json_encode(array("message" => "Error: " . $sql . "<br>" . $conn->error));
        }
        break;
        
    case 'PUT':
        // Mengupdate pesanan
        $data = json_decode(file_get_contents("php://input"));
        
        $id = $data->id;
        $status = $data->status;
        
        $sql = "UPDATE orders SET status='$status' WHERE id=$id";
        
        if ($conn->query($sql) === TRUE) {
            echo json_encode(array("message" => "Status pesanan berhasil diperbarui"));
        } else {
            http_response_code(500);
            echo json_encode(array("message" => "Error: " . $sql . "<br>" . $conn->error));
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(array("message" => "Metode tidak diizinkan"));
        break;
}

 $conn->close();
?>