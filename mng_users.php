<?php
include 'config2.php';
include 'header.php';

if (!isset($_SESSION['loggedin']) && $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Initialize variables
$username = $password = $name = $lastname = $age = '';
$roles = [];

// Handle Add, Update, Delete operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_user'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $name = $_POST['name'];
        $lastname = $_POST['lastname'];
        $age = $_POST['age'];
        $roles = $_POST['roles'] ?? [];

        // Check if the user already exists
        $query = "SELECT COUNT(*) FROM users WHERE username = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$username]);
        $userExists = $stmt->fetchColumn();

        if ($userExists) {
            $message = 'Username already exists. Please choose a different one.';
        } else {
            // Insert into users table
            $query = "INSERT INTO users (username, password, name, lastname, age) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$username, $password, $name, $lastname, $age]);

            // Insert into user_role_assignments table
            foreach ($roles as $role) {
                $query = "INSERT INTO user_role_assignments (username, role_type) VALUES (?, ?)";
                $stmt = $pdo->prepare($query);
                $stmt->execute([$username, $role]);
            }

            // Reset form fields after successful insertion
            $username = $password = $name = $lastname = $age = '';
            $roles = [];
            $message = 'User added successfully!';
        }
    } elseif (isset($_POST['edit_user'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $name = $_POST['name'];
        $lastname = $_POST['lastname'];
        $age = $_POST['age'];
        $roles = $_POST['roles'] ?? [];

        // Update users table
        $query = "UPDATE users SET password = ?, name = ?, lastname = ?, age = ? WHERE username = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$password, $name, $lastname, $age, $username]);

        // Clear existing roles
        $query = "DELETE FROM user_role_assignments WHERE username = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$username]);

        // Insert new roles into user_role_assignments table
        foreach ($roles as $role) {
            $query = "INSERT INTO user_role_assignments (username, role_type) VALUES (?, ?)";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$username, $role]);
        }

        // Reset form fields after successful update
        $username = $password = $name = $lastname = $age = '';
        $roles = [];
        $message = 'User updated successfully!';
    } elseif (isset($_POST['delete_user'])) {
        $username = $_POST['username'];

        // Delete user from users table
        $query = "DELETE FROM users WHERE username = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$username]);

        // Reset form fields after successful deletion
        $username = $password = $name = $lastname = $age = '';
        $roles = [];
        $message = 'User deleted successfully!';
    }
}

// Fetch all users and their roles for display
$query = "SELECT u.username, u.name, u.lastname, u.age, GROUP_CONCAT(r.role_type SEPARATOR ', ') as roles
          FROM users u
          LEFT JOIN user_role_assignments r ON u.username = r.username
          GROUP BY u.username";
$stmt = $pdo->query($query);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Manage Users</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        /* Ensure full width of the container */
        table {
            width: 100%;
        }

        /* Centered text in table headers */
        th {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Manage Users</h1>
        <!-- Return to Admin Dashboard link -->
        <div style="text-align: right; margin-top: 10px; margin-right: 10px;">
            <a href="dash.php" style="text-decoration: none; font-weight: bold;">Return to Admin Dashboard</a>
        </div>

        <h2>User List</h2>

        <?php if (isset($message)): ?>
            <p><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
    
       

        <table class="users-table">
            <tr>
                <th>Username</th>
                <th>Name</th>
                <th>Lastname</th>
                <th>Age</th>
                <th>Roles</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                    <td><?php echo htmlspecialchars($user['lastname']); ?></td>
                    <td><?php echo htmlspecialchars($user['age']); ?></td>
                    <td><?php echo htmlspecialchars($user['roles']); ?></td>
                    <td>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="username" value="<?php echo htmlspecialchars($user['username']); ?>">
                            <button type="submit" name="edit_user_form">Edit</button>
                        </form>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="username" value="<?php echo htmlspecialchars($user['username']); ?>">
                            <button type="submit" name="delete_user">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h2><?php echo isset($_POST['edit_user_form']) ? 'Edit User' : 'Add User'; ?></h2>
        <form method="post">
            <?php if (isset($_POST['edit_user_form'])): ?>
                <input type="hidden" name="username" value="<?php echo htmlspecialchars($_POST['username']); ?>">
            <?php endif; ?>

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" <?php echo isset($_POST['edit_user_form']) ? 'readonly' : 'required'; ?>><br><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" value="<?php echo htmlspecialchars($password); ?>" required><br><br>

            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required><br><br>

            <label for="lastname">Lastname:</label>
            <input type="text" id="lastname" name="lastname" value="<?php echo htmlspecialchars($lastname); ?>" required><br><br>

            <label for="age">Age:</label>
            <input type="number" id="age" name="age" value="<?php echo htmlspecialchars($age); ?>"><br><br>

            <label for="roles">Roles:</label><br>
            <?php
            $roleTypes = ['admin', 'vet', 'emp'];
            foreach ($roleTypes as $roleType):
                $checked = in_array($roleType, $roles) ? 'checked' : '';
            ?>
                <input type="checkbox" id="<?php echo $roleType; ?>" name="roles[]" value="<?php echo $roleType; ?>" <?php echo $checked; ?>>
                <label for="<?php echo $roleType; ?>"><?php echo ucfirst($roleType); ?></label><br>
            <?php endforeach; ?><br>

            <button type="submit" name="<?php echo isset($_POST['edit_user_form']) ? 'edit_user' : 'add_user'; ?>">
                <?php echo isset($_POST['edit_user_form']) ? 'Update User' : 'Add User'; ?>
            </button>
        </form>
    </div>
</body>

</html>