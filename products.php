<?php
require_once 'config.php';

// Mendapatkan metode request
 $method = $_SERVER['REQUEST_METHOD'];

// Mendapatkan ID produk jika ada
 $id = isset($_GET['id']) ? $_GET['id'] : null;

switch ($method) {
    case 'GET':
        if ($id) {
            // Mendapatkan produk berdasarkan ID
            $sql = "SELECT * FROM products WHERE id = $id";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                $product = $result->fetch_assoc();
                echo json_encode($product);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Produk tidak ditemukan"));
            }
        } else {
            // Mendapatkan semua produk
            $sql = "SELECT * FROM products ORDER BY id DESC";
            $result = $conn->query($sql);
            
            $products = array();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $products[] = $row;
                }
            }
            
            echo json_encode($products);
        }
        break;
        
    case 'POST':
        // Menambah produk baru
        $data = json_decode(file_get_contents("php://input"));
        
        $name = $data->name;
        $category = $data->category;
        $price = $data->price;
        $description = $data->description;
        $image = isset($data->image) ? $data->image : '';
        
        $sql = "INSERT INTO products (name, category, price, description, image) VALUES ('$name', '$category', $price, '$description', '$image')";
        
        if ($conn->query($sql) === TRUE) {
            $last_id = $conn->insert_id;
            echo json_encode(array("id" => $last_id, "message" => "Produk berhasil ditambahkan"));
        } else {
            http_response_code(500);
            echo json_encode(array("message" => "Error: " . $sql . "<br>" . $conn->error));
        }
        break;
        
    case 'PUT':
        // Mengupdate produk
        $data = json_decode(file_get_contents("php://input"));
        
        $id = $data->id;
        $name = $data->name;
        $category = $data->category;
        $price = $data->price;
        $description = $data->description;
        $image = isset($data->image) ? $data->image : '';
        
        $sql = "UPDATE products SET name='$name', category='$category', price=$price, description='$description', image='$image' WHERE id=$id";
        
        if ($conn->query($sql) === TRUE) {
            echo json_encode(array("message" => "Produk berhasil diperbarui"));
        } else {
            http_response_code(500);
            echo json_encode(array("message" => "Error: " . $sql . "<br>" . $conn->error));
        }
        break;
        
    case 'DELETE':
        // Menghapus produk
        $sql = "DELETE FROM products WHERE id = $id";
        
        if ($conn->query($sql) === TRUE) {
            echo json_encode(array("message" => "Produk berhasil dihapus"));
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