<?php
include 'db.php';

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $result = $conn->query("SELECT * FROM admin_users WHERE id=$id");
            $data = $result->fetch_assoc();
            echo json_encode($data);
        } else {
            $result = $conn->query("SELECT * FROM admin_users");
            $admin_users = [];
            while ($row = $result->fetch_assoc()) {
                $admin_users[] = $row;
            }
            echo json_encode($admin_users);
        }
        break;

    case 'POST':
        $name = $input['name'];
        $email = $input['email'];
        $password = $input['password'];
        $role = $input['role'];



        $conn->query("INSERT INTO admin_users (name,email,password,role) VALUES ('$name','$email','$password' '$role')");
        echo json_encode(["message" => "Admin User added successfully"]);
        break;

    case 'PUT':
        $id = $_GET['id'];
        $name = $input['name'];
        $email = $input['email'];
        $password = $input['password'];
        $role = $input['role'];
        $conn->query("UPDATE admin_user SET name='$name',
         email='$email',password='$password', role='$role' WHERE id=$id");
        echo json_encode(["message" => "Admin User updated successfully"]);
        break;

    case 'DELETE':
        $id = $_GET['id'];
        $conn->query("DELETE FROM admin_user WHERE id=$id");
        echo json_encode(["message" => "Admin User deleted successfully"]);
        break;

    default:
        echo json_encode(["message" => "Invalid request method"]);
        break;
}

$conn->close();