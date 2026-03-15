<?php
include 'header.php'; 
include 'config2.php'; // Assumes you have a config file with DB connection settings

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare SQL query to check if the user exists and retrieve their roles
    $query = "SELECT u.username, u.password, r.role_type 
              FROM users u
              JOIN user_role_assignments r ON u.username = r.username 
              WHERE u.username = ?";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$username]);
    $userRoles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($userRoles && $password === $userRoles[0]['password']) { // Simple password check for demo; replace with password hashing in production
        $_SESSION['username'] = $username;
        $_SESSION['loggedin'] = true;

        // Check user roles and redirect accordingly
        $roles = array_column($userRoles, 'role_type');
        if (in_array('admin', $roles)) {
            header('Location: dash.php');
        } elseif (in_array('vet', $roles)) {
            header('Location: vet_dash.php');
        } elseif (in_array('emp', $roles)) {
            header('Location: emp_dashboard.php');
        } else {
            $message = 'No valid roles found for your account.';
        }
        exit;
    } else {
        $message = 'Invalid username or password.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Zoo Arcadia</title>
    <style>
        /* Center the login section on the page */
        .login-container {
            max-width: 400px;
            margin: 100px auto; /* Center on the page */
            padding: 20px;
            background-color: #f9f9f9;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        
        .login-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .login-container label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }

        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: 80%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .login-container button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            border: none;
            color: white;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .login-container button:hover {
            background-color: #45a049;
        }

        .login-container .error {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if ($message) : ?>
            <p class="error"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <form method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
