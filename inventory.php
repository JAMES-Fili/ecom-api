<?php
include 'db.php';
header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    case 'GET':
        $sql = "SELECT oi.*, p.name AS product_name FROM order_items oi
                JOIN products p ON oi.product_id = p.id";

        if (isset($_GET['order_id'])) {
            $order_id = intval($_GET['order_id']);
            $sql .= " WHERE oi.order_id = $order_id";
        }

        $result = $conn->query($sql);
        $items = [];

        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }

        echo json_encode($items);
        break;

    case 'POST':
        if (!isset($_POST['order_id'], $_POST['product_id'], $_POST['quantity'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $_POST['order_id'], $_POST['product_id'], $_POST['quantity']);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'id' => $stmt->insert_id]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Insert failed']);
        }

        $stmt->close();
        break;

    case 'PUT':
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing order item ID']);
            exit;
        }

        parse_str(file_get_contents("php://input"), $_PUT);

        if (!isset($_PUT['quantity'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing quantity']);
            exit;
        }

        $id = intval($_GET['id']);
        $quantity = intval($_PUT['quantity']);

        $stmt = $conn->prepare("UPDATE order_items SET quantity = ? WHERE id = ?");
        $stmt->bind_param("ii", $quantity, $id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Update failed']);
        }

        $stmt->close();
        break;

    case 'DELETE':
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing order item ID']);
            exit;
        }

        $id = intval($_GET['id']);
        $stmt = $conn->prepare("DELETE FROM order_items WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Delete failed']);
        }

        $stmt->close();
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}

$conn->close();
