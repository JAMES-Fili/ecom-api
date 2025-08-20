<?php
include 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Retrieve all products
        $sql = "SELECT * FROM products";
        $result = $conn->query($sql);

        $products = [];
        while ($row = $result->fetch_assoc()) {
            $row['imageUrl'] = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/uploads/' . $row['imageUrl'];
            $products[] = $row;
        }

        echo json_encode($products);
        break;

    case 'POST':
        // Handle image upload and data insert
        if (!isset($_FILES['image']) || !isset($_POST['name'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            exit;
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $image = $_FILES['image'];

        if (!in_array($image['type'], $allowedTypes)) {
            http_response_code(400);
            echo json_encode(['error' => 'Unsupported image type']);
            exit;
        }

        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
        $filename = uniqid('prod_', true) . '.' . $ext;
        $targetPath = $uploadDir . $filename;

        if (!move_uploaded_file($image['tmp_name'], $targetPath)) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to upload image']);
            exit;
        }

        // Sanitize and bind values
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, imageUrl, cat_id, brand, model, specifications, stockQuantity)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param(
            "ssisiissi",
            $_POST['name'],
            $_POST['description'],
            $_POST['price'],
            $filename,
            $_POST['cat_id'],
            $_POST['brand'],
            $_POST['model'],
            $_POST['specifications'],
            $_POST['stockQuantity']
        );

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'id' => $stmt->insert_id]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Insert failed']);
        }

        $stmt->close();
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}
$conn->close();
