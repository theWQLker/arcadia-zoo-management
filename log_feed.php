<?php
include 'config2.php';
include 'header.php';

// Handle Add, Update, Delete operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_record'])) {
        $animal_id = $_POST['animal_id'];
        $meal_preparation = $_POST['meal_preparation'];
        $weight = $_POST['weight'];

        $query = "INSERT INTO feeding_records (animal_id, meal_preparation, weight) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$animal_id, $meal_preparation, $weight]);
    } elseif (isset($_POST['edit_record'])) {
        $record_id = $_POST['record_id'];
        $animal_id = $_POST['animal_id'];
        $meal_preparation = $_POST['meal_preparation'];
        $weight = $_POST['weight'];

        $query = "UPDATE feeding_records SET animal_id = ?, meal_preparation = ?, weight = ? WHERE record_id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$animal_id, $meal_preparation, $weight, $record_id]);
    } elseif (isset($_POST['delete_record'])) {
        $record_id = $_POST['record_id'];

        $query = "DELETE FROM feeding_records WHERE record_id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$record_id]);
    }
}

// Fetching all feeding records
$query = "SELECT fr.record_id, a.prenom AS animal_name, fr.meal_preparation, fr.weight, fr.feeding_time 
          FROM feeding_records fr
          JOIN animals a ON fr.animal_id = a.id";
$stmt = $pdo->query($query);
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Feeding Records</title>
    <link rel="stylesheet" type="text/css" href="ric6.css">
</head>
<body>
    <div class="container">
        <h1>Manage Feeding Records</h1>

        <div style="text-align: right;">
            <a href="dash.php">Return to Admin Dashboard</a>
        </div>

        <h2>Feeding Records</h2>
        <table class="services-table">
            <tr>
                <th>Animal Name</th>
                <th>Meal</th>
                <th>Weight (kg)</th>
                <th>Feeding Time</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($records as $record) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($record['animal_name']); ?></td>
                    <td><?php echo htmlspecialchars($record['meal_preparation']); ?></td>
                    <td><?php echo htmlspecialchars($record['weight']); ?></td>
                    <td><?php echo htmlspecialchars($record['feeding_time']); ?></td>
                    <td>
                        <form method="post" style="display: inline-block;">
                            <input type="hidden" name="record_id" value="<?php echo htmlspecialchars($record['record_id']); ?>">
                            <button type="submit" name="edit_record_form" class="edit-button">Edit</button>
                        </form>
                        <form method="post" style="display: inline-block;">
                            <input type="hidden" name="record_id" value="<?php echo htmlspecialchars($record['record_id']); ?>">
                            <button type="submit" name="delete_record" class="delete-button">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h2><?php echo isset($_POST['edit_record_form']) ? 'Edit Feeding Record' : 'Add Feeding Record'; ?></h2>
        <form method="post" class="form-container">
            <?php if (isset($_POST['edit_record_form'])) : ?>
                <input type="hidden" name="record_id" value="<?php echo htmlspecialchars($_POST['record_id']); ?>">
            <?php endif; ?>

            <label for="animal_id">Animal:</label>
            <select id="animal_id" name="animal_id" required>
                <?php
                $animal_query = "SELECT id, prenom FROM animals";
                $animal_stmt = $pdo->query($animal_query);
                while ($animal = $animal_stmt->fetch(PDO::FETCH_ASSOC)) {
                    $selected = isset($_POST['animal_id']) && $_POST['animal_id'] == $animal['id'] ? 'selected' : '';
                    echo "<option value=\"{$animal['id']}\" $selected>{$animal['prenom']}</option>";
                }
                ?>
            </select><br><br>

            <label for="meal_preparation">Meal:</label>
            <input type="text" id="meal_preparation" name="meal_preparation" value="<?php echo isset($_POST['meal_preparation']) ? htmlspecialchars($_POST['meal_preparation']) : ''; ?>" required><br><br>

            <label for="weight">Weight (kg):</label>
            <input type="number" id="weight" name="weight" step="0.01" value="<?php echo isset($_POST['weight']) ? htmlspecialchars($_POST['weight']) : ''; ?>" ><br><br>

            <button type="submit" name="<?php echo isset($_POST['edit_record_form']) ? 'edit_record' : 'add_record'; ?>" class="submit-button">
                <?php echo isset($_POST['edit_record_form']) ? 'Update Record' : 'Add Record'; ?>
            </button>
        </form>
    </div>
</body>
</html>
