


<?php
include 'db.php';

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $result = $conn->query("SELECT * FROM products WHERE id=$id");
            $data = $result->fetch_assoc();
            echo json_encode($data);
        } else {
            $result = $conn->query("SELECT * FROM products");
            $products = [];
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
            echo json_encode($products);
        }
        break;

    case 'POST':
        $name = $input['name'];
        $description = $input['description'];
        $price = $input['price'];
        $imageUrl = $input['imageUrl'];
        $cat_id = $input['cat_id'];
        $brand = $input['brand'];
        $model = $input['model'];
        $specifications = $input['specifications'];
        $stockQuantity = $input['stockQuantity'];
        $isFeatured = $input['isFeatured'];

        $conn->query("INSERT INTO products (name, description, price,imageUrl,cat_id,brand, model, specifications, stockQuantity, isFeatured) VALUES ('$name', '$description', $price, '$imageUrl', '$cat_id', '$brand','$model','$specifications', '$stockQuantity', '$isFeatured')");
        echo json_encode(["message" => "User added successfully"]);
        break;

    case 'PUT':
        $id = $_GET['id'];
        $name = $input['name'];
        $description = $input['description'];
        $price = $input['price'];
        $imageUrl = $input['imageUrl'];
        $cat_id = $input['cat_id'];
        $brand = $input['brand'];
        $model = $input['model'];
        $specifications = $input['specifications'];
        $stockQuantity = $input['stockQuantity'];
        $isFeatured = $input['isFeatured'];
        $conn->query("UPDATE products SET name='$name',
                     description='$description', price=$price, imageUrl=$imageUrl, cat_id=$cat_id, brand=$brand, model=$model, specification=$specifications,stockQuantity=$stockQuantity, isFeatured=$isFeatured WHERE id=$id");
        echo json_encode(["message" => "User updated successfully"]);
        break;

    case 'DELETE':
        $id = $_GET['id'];
        $conn->query("DELETE FROM products WHERE id=$id");
        echo json_encode(["message" => "User deleted successfully"]);
        break;

    default:
        echo json_encode(["message" => "Invalid request method"]);
        break;
}

$conn->close();