<!DOCTYPE html>
<html lang="en">
<?php include("header.php"); ?>
<?php include 'config2.php'; ?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Animal Statistics</title>
</head>

<body>
    <div class="container back" style="padding-left: 25px;">
        <h2>Animal Click Statistics</h2>
        <form method="GET" action="">
            <label for="start">Start Date:</label>
            <input type="date" name="start" required>
            <label for="end">End Date:</label>
            <input type="date" name="end" required>
            <button type="submit">Filter</button>
        </form>

        <?php
        $filename = 'statistics.json';
        $clicks = [];
        $data = json_decode(file_get_contents('php://input'), true);

        // Check if the "action" key exists
        if (isset($data['action'])) {
            $action = $data['action'];

            // Proceed based on the action
            if ($action === 'click' && isset($data['animalId'])) {
                $animalId = $data['animalId'];

                // Your logging logic here
                logStatistics($action, ['animalId' => $animalId]);
            } else {
                // Handle invalid action or missing animalId
                http_response_code(400); // Bad Request
                echo json_encode(['error' => 'Invalid action or missing animal ID']);
            }
        } else {
            // Handle missing action
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'Missing action']);
        }


        if (file_exists($filename)) {
            $statistics = json_decode(file_get_contents($filename), true);
            $startDate = isset($_GET['start']) ? $_GET['start'] : null;
            $endDate = isset($_GET['end']) ? $_GET['end'] : null;

            foreach ($statistics as $entry) {
                if ($entry['action'] === 'click') {
                    $timestamp = $entry['timestamp'];

                    // Filter by date range if set
                    if (($startDate && $timestamp < $startDate) || ($endDate && $timestamp > $endDate)) {
                        continue;
                    }

                    $animalId = $entry['data']['animalId'];
                    $clicks[$animalId] = isset($clicks[$animalId]) ? $clicks[$animalId] + 1 : 1;
                }
            }

            // Get animal names for display
            if (!empty($clicks)) {
                $animalIds = implode(',', array_keys($clicks));
                $stmt = $pdo->query("SELECT id, prenom FROM animals WHERE id IN ($animalIds)");
                $animalNames = [];
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $animalNames[$row['id']] = $row['prenom'];
                }

                // Display the statistics in a table
                echo '<table border="1">';
                echo '<tr><th>Animal Name</th><th>Clicks</th></tr>';
                foreach ($clicks as $animalId => $count) {
                    $animalName = htmlspecialchars($animalNames[$animalId] ?? 'Unknown');
                    echo "<tr><td>{$animalName}</td><td>{$count}</td></tr>";
                }
                echo '</table>';

                // Calculate total and average clicks
                $totalClicks = array_sum($clicks);
                $averageClicks = count($clicks) > 0 ? $totalClicks / count($clicks) : 0;

                // Display total and average
                echo "<p>Total Clicks: {$totalClicks}</p>";
                echo "<p>Average Clicks per Animal: " . number_format($averageClicks, 2) . "</p>";
            } else {
                echo '<p>No statistics found for the selected date range.</p>';
            }
        } else {
            echo '<p>No statistics file found.</p>';
        }
        ?>
    </div>
</body>

</html>