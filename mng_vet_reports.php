<?php
include 'config2.php';
include 'header.php';

// Handle Add, Update, Delete operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_report'])) {
        $animal_id = $_POST['animal_id'];
        $vet_username = $_POST['vet_username'];
        $report_date = $_POST['report_date'];
        $health_status = $_POST['health_status'];
        $treatment = $_POST['treatment'];
        $follow_up_date = $_POST['follow_up_date'];

        $query = "INSERT INTO veterinary_reports (animal_id, vet_username, report_date, health_status, treatment, follow_up_date) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$animal_id, $vet_username, $report_date, $health_status, $treatment, $follow_up_date]);
    } elseif (isset($_POST['edit_report'])) {
        $report_id = $_POST['report_id'];
        $animal_id = $_POST['animal_id'];
        $vet_username = $_POST['vet_username'];
        $report_date = $_POST['report_date'];
        $health_status = $_POST['health_status'];
        $treatment = $_POST['treatment'];
        $follow_up_date = $_POST['follow_up_date'];

        $query = "UPDATE veterinary_reports 
                  SET animal_id = ?, vet_username = ?, report_date = ?, health_status = ?, treatment = ?, follow_up_date = ? 
                  WHERE report_id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$animal_id, $vet_username, $report_date, $health_status, $treatment, $follow_up_date, $report_id]);
    } elseif (isset($_POST['delete_report'])) {
        $report_id = $_POST['report_id'];

        $query = "DELETE FROM veterinary_reports WHERE report_id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$report_id]);
    }
}

// Fetching the reports
$query = "SELECT vr.report_id, vr.animal_id, a.prenom AS animal_name, vr.vet_username, u.name AS vet_name, 
          vr.report_date, vr.health_status, vr.treatment, vr.follow_up_date 
          FROM veterinary_reports vr
          JOIN animals a ON vr.animal_id = a.id
          JOIN users u ON vr.vet_username = u.username";
$stmt = $pdo->query($query);
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Veterinary Reports</title>
    <link rel="stylesheet" type="text/css" href="ric6.css">
</head>
<body>
    <div class="container">
        <h1>Manage Veterinary Reports</h1>

        <!-- Add Return to Admin Dashboard Link -->
        <div style="text-align: right;">
            <a href="dash.php">Return to Admin Dashboard</a>
        </div>

        <h2>Reports</h2>
        <table class="services-table">
            <tr>
                <th>Animal Name</th>
                <th>Vet</th>
                <th>Report Date</th>
                <th>Health Status</th>
                <th>Treatment</th>
                <th>Follow-Up Date</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($reports as $report) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($report['animal_name']); ?></td>
                    <td><?php echo htmlspecialchars($report['vet_name']); ?></td>
                    <td><?php echo htmlspecialchars($report['report_date']); ?></td>
                    <td><?php echo htmlspecialchars($report['health_status']); ?></td>
                    <td><?php echo htmlspecialchars($report['treatment']); ?></td>
                    <td><?php echo htmlspecialchars($report['follow_up_date']); ?></td>
                    <td>
                        <form method="post" style="display: inline-block;">
                            <input type="hidden" name="report_id" value="<?php echo htmlspecialchars($report['report_id']); ?>">
                            <button type="submit" name="edit_report_form" class="edit-button">Edit</button>
                        </form>
                        <form method="post" style="display: inline-block;">
                            <input type="hidden" name="report_id" value="<?php echo htmlspecialchars($report['report_id']); ?>">
                            <button type="submit" name="delete_report" class="delete-button">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h2><?php echo isset($_POST['edit_report_form']) ? 'Edit Report' : 'Add Report'; ?></h2>
        <form method="post" class="form-container">
            <?php if (isset($_POST['edit_report_form'])) : ?>
                <input type="hidden" name="report_id" value="<?php echo htmlspecialchars($_POST['report_id']); ?>">
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

            <label for="vet_username">Veterinarian:</label>
            <select id="vet_username" name="vet_username" required>
                <?php
                $vet_query = "SELECT username, name FROM users 
                              WHERE username IN (SELECT username FROM user_role_assignments WHERE role_type = 'vet')";
                $vet_stmt = $pdo->query($vet_query);
                while ($vet = $vet_stmt->fetch(PDO::FETCH_ASSOC)) {
                    $selected = isset($_POST['vet_username']) && $_POST['vet_username'] == $vet['username'] ? 'selected' : '';
                    echo "<option value=\"{$vet['username']}\" $selected>{$vet['name']}</option>";
                }
                ?>
            </select><br><br>

            <label for="report_date">Report Date:</label>
            <input type="date" id="report_date" name="report_date" value="<?php echo isset($_POST['report_date']) ? htmlspecialchars($_POST['report_date']) : ''; ?>" required><br><br>

            <label for="health_status">Health Status:</label>
            <textarea id="health_status" name="health_status" required><?php echo isset($_POST['health_status']) ? htmlspecialchars($_POST['health_status']) : ''; ?></textarea><br><br>

            <label for="treatment">Treatment:</label>
            <textarea id="treatment" name="treatment"><?php echo isset($_POST['treatment']) ? htmlspecialchars($_POST['treatment']) : ''; ?></textarea><br><br>

            <label for="follow_up_date">Follow-Up Date:</label>
            <input type="date" id="follow_up_date" name="follow_up_date" value="<?php echo isset($_POST['follow_up_date']) ? htmlspecialchars($_POST['follow_up_date']) : ''; ?>"><br><br>

            <button type="submit" name="<?php echo isset($_POST['edit_report_form']) ? 'edit_report' : 'add_report'; ?>" class="submit-button">
                <?php echo isset($_POST['edit_report_form']) ? 'Update Report' : 'Add Report'; ?>
            </button>
        </form>
    </div>
</body>
</html>
