<?php
include 'db.php';
header("Content-Type: application/json");


$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Optional: ?id= to get a specific admin
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $stmt = $conn->prepare("SELECT id, name, email, role FROM admin_users WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            echo json_encode($user ?: ['error' => 'Admin user not found']);
            $stmt->close();
        } else {
            $result = $conn->query("SELECT id, name, email, role FROM admin_users");
            $users = [];
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
            echo json_encode($users);
        }
        break;

    case 'POST':
        // Check for required fields
        $required = ['name', 'email', 'password', 'role'];
        foreach ($required as $field) {
            if (!isset($_POST[$field])) {
                http_response_code(400);
                echo json_encode(['error' => "Missing field: $field"]);
                exit;
            }
        }

        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $role = $_POST['role'];

        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM admin_users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            http_response_code(409);
            echo json_encode(['error' => 'Email already registered']);
            $stmt->close();
            exit;
        }
        $stmt->close();

        // Insert new admin user
        $stmt = $conn->prepare("INSERT INTO admin_users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $password, $role);

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
