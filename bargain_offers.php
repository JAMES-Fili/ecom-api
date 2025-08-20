<?php
include 'db.php';
header("Content-Type: application/json");


$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Get all bargain offers (join with product and customer if needed)
        $sql = "SELECT * FROM bargain_offers ORDER BY createdAt DESC";
        $result = $conn->query($sql);

        $offers = [];
        while ($row = $result->fetch_assoc()) {
            $offers[] = $row;
        }

        echo json_encode($offers);
        break;

    case 'POST':
        // Validate required fields
        $required = ['product_id', 'customer_id', 'offeredPrice', 'quantity'];

        foreach ($required as $field) {
            if (!isset($_POST[$field])) {
                http_response_code(400);
                echo json_encode(['error' => "Missing field: $field"]);
                exit;
            }
        }

        // Optional fields
        $message = isset($_POST['message']) ? $_POST['message'] : '';
        $status = isset($_POST['status']) ? $_POST['status'] : 0;
        $adminResponse = isset($_POST['adminResponse']) ? $_POST['adminResponse'] : '';

        $stmt = $conn->prepare("INSERT INTO bargain_offers (product_id, customer_id, offeredPrice, quantity, message, status, adminResponse)
                                VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "iiiisis",
            $_POST['product_id'],
            $_POST['customer_id'],
            $_POST['offeredPrice'],
            $_POST['quantity'],
            $message,
            $status,
            $adminResponse
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
