<?php
include 'header.php';
include 'config2.php';

if (!isset($_SESSION['loggedin']) && $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Handle Add, Update, Delete operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_service'])) {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $availability_schedule = $_POST['availability_schedule'];

        $query = "INSERT INTO services (name, description, price, availability_schedule) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$name, $description, $price, $availability_schedule]);
    } elseif (isset($_POST['edit_service'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $availability_schedule = $_POST['availability_schedule'];

        $query = "UPDATE services SET name = ?, description = ?, price = ?, availability_schedule = ? WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$name, $description, $price, $availability_schedule, $id]);
    } elseif (isset($_POST['delete_service'])) {
        $id = $_POST['id'];

        $query = "DELETE FROM services WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$id]);
    }
}

// Fetch Services
$query = "SELECT * FROM services";
$stmt = $pdo->query($query);
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Manage Services</title>
    <link rel="stylesheet" type="text/css" href="ric6.css">
</head>

<body>
    <div class="container">
        <h1>Manage Services</h1>

        <table class="services-table">
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Availability Schedule</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($services as $service) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($service['name']); ?></td>
                    <td><?php echo htmlspecialchars($service['description']); ?></td>
                    <td><?php echo htmlspecialchars($service['price']); ?></td>
                    <td><?php echo htmlspecialchars($service['availability_schedule']); ?></td>
                    <td>
                        <form method="post" style="display: inline-block;">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($service['id']); ?>">
                            <input type="hidden" name="name" value="<?php echo htmlspecialchars($service['name']); ?>">
                            <input type="hidden" name="description" value="<?php echo htmlspecialchars($service['description']); ?>">
                            <input type="hidden" name="price" value="<?php echo htmlspecialchars($service['price']); ?>">
                            <input type="hidden" name="availability_schedule" value="<?php echo htmlspecialchars($service['availability_schedule']); ?>">
                            <button type="submit" name="edit_service_form" class="edit-button">Edit</button>
                        </form>
                        <form method="post" style="display: inline-block;">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($service['id']); ?>">
                            <button type="submit" name="delete_service" class="delete-button">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h2><?php echo isset($_POST['edit_service_form']) ? 'Edit Service' : 'Add Service'; ?></h2>
        <form method="post" class="service-form">
            <!-- Include hidden ID field only when editing a service -->
            <?php if (isset($_POST['edit_service_form'])): ?>
                <input type="hidden" name="id" value="<?php echo isset($_POST['id']) ? htmlspecialchars($_POST['id']) : ''; ?>">
            <?php endif; ?>

            <label for="name">Service Name:</label>
            <input type="text" id="name" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required><br><br>

            <label for="description">Description:</label>
            <textarea id="description" name="description" required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea><br><br>

            <label for="price">Price:</label>
            <input type="text" id="price" name="price" value="<?php echo isset($_POST['price']) ? htmlspecialchars($_POST['price']) : ''; ?>" required><br><br>

            <label for="availability_schedule">Availability Schedule:</label>
            <input type="text" id="availability_schedule" name="availability_schedule" value="<?php echo isset($_POST['availability_schedule']) ? htmlspecialchars($_POST['availability_schedule']) : ''; ?>" required><br><br>

            <button type="submit" name="<?php echo isset($_POST['edit_service_form']) ? 'edit_service' : 'add_service'; ?>" class="submit-button">
                <?php echo isset($_POST['edit_service_form']) ? 'Update Service' : 'Add Service'; ?>
            </button>
        </form>
    </div>
</body>

</html>