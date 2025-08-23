<?php
include 'db.php';

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $result = $conn->query("SELECT * FROM orders WHERE id=$id");
            $data = $result->fetch_assoc();
            echo json_encode($data);
        } else {
            $result = $conn->query("SELECT * FROM orders");
            $users = [];
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
            echo json_encode($users);
        }
        break;

    case 'POST':
        $customer_id = $input['customer_id'];
        $status = $input['status'];
        $paymentMethod = $input['paymentMethod'];
        $whatsappMessageId = $input['whatsappMessageId'];

        $conn->query("INSERT INTO orders (customer_id, status, paymentMethod, whatsappMessageId) VALUES ('$customer_id', '$status','$paymentMethod','$whatsappMessageId')");
        echo json_encode(["message" => "Order added successfully"]);
        break;

    case 'PUT':
        $id = $_GET['id'];
        $customer_id = $input['customer_id'];
        $status = $input['status'];
        $paymentMethod = $input['paymentMethod'];
        $whatsappMessageId = $input['whatsappMessageId'];

        $conn->query("UPDATE orders SET  customer_id='$customer_id',status='$status',paymentMethod='$paymentMethod',whatsappMessageId='$whatsappMessageId' WHERE id=$id");
        echo json_encode(["message" => "Order updated successfully"]);
        break;

    case 'DELETE':
        $id = $_GET['id'];
        $conn->query("DELETE FROM categories WHERE id=$id");
        echo json_encode(["message" => "Order deleted successfully"]);
        break;

    default:
        echo json_encode(["message" => "Invalid request method"]);
        break;
}

$conn->close();