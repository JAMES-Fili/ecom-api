<?php
include 'db.php';

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $result = $conn->query("SELECT * FROM categories WHERE id=$id");
            $data = $result->fetch_assoc();
            echo json_encode($data);
        } else {
            $result = $conn->query("SELECT * FROM categories");
            $users = [];
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
            echo json_encode($users);
        }
        break;

    case 'POST':
        $name = $input['name'];
        $description = $input['description'];
        $conn->query("INSERT INTO categories (name, description) VALUES ('$name', '$description')");
        echo json_encode(["message" => "Category added successfully"]);
        break;

    case 'PUT':
        $id = $_GET['id'];
        $name = $input['name'];
        $description = $input['description'];
        $isActive = $input['isActive'];
        $sortOrder = $input['sortOrder'];
        $conn->query("UPDATE categories SET name='$name',
                     description='$description' WHERE id=$id");
        echo json_encode(["message" => "Category updated successfully"]);
        break;

    case 'DELETE':
        $id = $_GET['id'];
        $conn->query("DELETE FROM categories WHERE id=$id");
        echo json_encode(["message" => "Category deleted successfully"]);
        break;

    default:
        echo json_encode(["message" => "Invalid request method"]);
        break;
}

$conn->close();