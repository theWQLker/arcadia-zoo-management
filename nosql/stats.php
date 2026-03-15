<!DOCTYPE html>
<html lang="en">
<?php include("header.php"); ?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Animal Statistics</title>
</head>
<body>
    <div class="container">
        <h2>Animal View Statistics</h2>
        <?php
        require 'vendor/autoload.php'; // Include Composer autoload

        // Connect to MongoDB
        $client = new MongoDB\Client("mongodb://localhost:27017");
        $database = $client->selectDatabase('zoo');
        $collection = $database->selectCollection('animal_statistics');

        // Fetch statistics
        $statistics = $collection->find();

        // Display statistics
        echo '<table class="table">';
        echo '<thead><tr><th>Animal ID</th><th>Views</th><th>Date</th><th>Season</th></tr></thead>';
        echo '<tbody>';
        foreach ($statistics as $stat) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($stat['animal_id']) . '</td>';
            echo '<td>' . htmlspecialchars($stat['views']) . '</td>';
            echo '<td>' . htmlspecialchars($stat['date'] ?? 'N/A') . '</td>';
            echo '<td>' . htmlspecialchars($stat['season'] ?? 'N/A') . '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
        ?>
    </div>
</body>
</html>
