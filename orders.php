<?php
include 'db.php';
header("Content-Type: application/json");


$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $sql = "SELECT * FROM order_items";
        if (isset($_GET['order_id'])) {
            $order_id = intval($_GET['order_id']);
            $sql .= " WHERE order_id = $order_id";
        }

        $result = $conn->query($sql);
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        echo json_encode($items);
        break;

    case 'POST':
        // Validate input
        if (!isset($_POST['order_id'], $_POST['product_id'], $_POST['quantity'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing order_id, product_id, or quantity']);
            exit;
        }

        $order_id = intval($_POST['order_id']);
        $product_id = intval($_POST['product_id']);
        $quantity = intval($_POST['quantity']);

        // Insert into DB
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $order_id, $product_id, $quantity);

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
