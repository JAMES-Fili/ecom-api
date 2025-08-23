<?php
include 'db.php';

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $result = $conn->query("SELECT * FROM inventory_logs WHERE id=$id");
            $data = $result->fetch_assoc();
            echo json_encode($data);
        } else {
            $result = $conn->query("SELECT * FROM inventory_logs");
            $users = [];
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
            echo json_encode($users);
        }
        break;

    case 'POST':
        $product_id =$input['product_id'];
        $quantityChange = $input['quantityChange'];
        $newQuantity= $input['newQuantity'];
        $reason = $input['reason'];
        $admin_id = $input['admin_id'];

        $conn->query("INSERT INTO inventory_logs (product_id,quantityChange,newQuantity,reason,admin_id) VALUES ('$product_id', '$quantityChange','$newQuantity','$admin_id')");
        echo json_encode(["message" => "Inventory added successfully"]);
        break;

    case 'PUT':
        $id = $_GET['id'];
        $product_id = $input['product_id'];
        $quantityChange = $input['quantityChange'];
        $newQuantity = $input['newQuantity'];
        $reason = $input['reason'];
        $admin_id = $input['admin_id'];

        $conn->query("UPDATE inventory_logs SET  product_id='$product_id',quantityChange='$quantityChange',newQuantity='$newQuantity',reason='$reason',admin_id='$admin_id' WHERE id=$id");
        echo json_encode(["message" => "Inventory updated successfully"]);
        break;

    case 'DELETE':
        $id = $_GET['id'];
        $conn->query("DELETE FROM inventory_logs WHERE id=$id");
        echo json_encode(["message" => "Inventory deleted successfully"]);
        break;

    default:
        echo json_encode(["message" => "Invalid request method"]);
        break;
}

$conn->close();