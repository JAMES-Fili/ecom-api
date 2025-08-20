<?php
include 'db.php';
header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $result = $conn->query("SELECT * FROM site_settings LIMIT 1");
        $row = $result->fetch_assoc();
        echo json_encode($row ?: []);
        break;

    case 'POST':
        // Check if a row already exists
        $check = $conn->query("SELECT id FROM site_settings LIMIT 1");
        if ($check->num_rows > 0) {
            http_response_code(409);
            echo json_encode(['error' => 'Settings already exist. Use PUT to update.']);
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO site_settings 
            (whatsappNumber, businessName, businessAddress, businessEmail, deliverySettings, paymentSettings, featuredProducts)
            VALUES (?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param(
            "sssssss",
            $_POST['whatsappNumber'],
            $_POST['businessName'],
            $_POST['businessAddress'],
            $_POST['businessEmail'],
            $_POST['deliverySettings'],
            $_POST['paymentSettings'],
            $_POST['featuredProducts']
        );

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'id' => $stmt->insert_id]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Insert failed']);
        }

        $stmt->close();
        break;

    case 'PUT':
        parse_str(file_get_contents("php://input"), $_PUT);

        $stmt = $conn->prepare("UPDATE site_settings SET 
            whatsappNumber = ?, 
            businessName = ?, 
            businessAddress = ?, 
            businessEmail = ?, 
            deliverySettings = ?, 
            paymentSettings = ?, 
            featuredProducts = ?
            WHERE id = 1");

        $stmt->bind_param(
            "sssssss",
            $_PUT['whatsappNumber'],
            $_PUT['businessName'],
            $_PUT['businessAddress'],
            $_PUT['businessEmail'],
            $_PUT['deliverySettings'],
            $_PUT['paymentSettings'],
            $_PUT['featuredProducts']
        );

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Settings updated']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Update failed']);
        }

        $stmt->close();
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}

$conn->close();
