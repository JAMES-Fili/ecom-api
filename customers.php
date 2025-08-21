<?php
include 'db.php';

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $result = $conn->query("SELECT * FROM customers WHERE id=$id");
            $data = $result->fetch_assoc();
            echo json_encode($data);
        } else {
            $result = $conn->query("SELECT * FROM customers");
            $customers = [];
            while ($row = $result->fetch_assoc()) {
                $customers[] = $row;
            }
            echo json_encode($customers);
        }
        break;

    case 'POST':
        $name = $input['name'];
        $phone = $input['phone'];
        $email = $input['email'];
        $address = $input['address'];


        $conn->query("INSERT INTO customers (name, phone, email,address) VALUES ('$name', '$phone', '$email', '$address')");
        echo json_encode(["message" => "Customers added successfully"]);
        break;

    case 'PUT':
        $id = $_GET['id'];
        $name = $input['name'];
        $phone = $input['phone'];
        $email = $input['email'];
        $address = $input['address'];
        $conn->query("UPDATE customers SET name='$name',
                     phone='$phone', email=$email, address='$address' WHERE id=$id");
        echo json_encode(["message" => "Customer updated successfully"]);
        break;

    case 'DELETE':
        $id = $_GET['id'];
        $conn->query("DELETE FROM products WHERE id=$id");
        echo json_encode(["message" => "Customer deleted successfully"]);
        break;

    default:
        echo json_encode(["message" => "Invalid request method"]);
        break;
}

$conn->close();