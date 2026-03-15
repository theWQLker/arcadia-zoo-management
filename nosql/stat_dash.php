<!DOCTYPE html>
<html lang="en">
<?php include("header.php"); ?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zoo Animal Statistics</title>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Include Chart.js -->
    
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .chart-container {
            width: 80%;
            margin: 20px auto;
        }

        .filters {
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Animal Click Statistics</h2>
        
        <!-- Filter for Date Range -->
        <div class="filters">
            <form id="filter-form" method="GET">
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date" value="<?php echo isset($_GET['start_date']) ? htmlspecialchars($_GET['start_date']) : ''; ?>">

                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date" value="<?php echo isset($_GET['end_date']) ? htmlspecialchars($_GET['end_date']) : ''; ?>">

                <input type="submit" value="Filter">
            </form>
        </div>

        <!-- Table to show statistics -->
        <h3>Click Statistics by Animal</h3>
        <table>
            <thead>
                <tr>
                    <th>Animal Name</th>
                    <th>Clicks</th>
                    <th>Average Clicks (Per Day)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Include MongoDB client
                require 'vendor/autoload.php';
                
                // MongoDB connection
                $client = new MongoDB\Client("mongodb://localhost:27017");
                $db = $client->zoo_database;
                $clicksCollection = $db->clicks;
                $animalsCollection = $db->animals; // Assuming you have this collection

                // Date filtering
                $start_date = isset($_GET['start_date']) ? date(strtotime($_GET['start_date']) * 1000) : null;
                $end_date = isset($_GET['end_date']) ? date((strtotime($_GET['end_date']) + 86400) * 1000) : null; // End of the day

                $filter = [];

                // Add date range to filter if provided
                if ($start_date && $end_date) {
                    $filter['timestamp'] = ['$gte' => $start_date, '$lte' => $end_date];
                }

                // Fetch clicks by animal
                $cursor = $clicksCollection->aggregate([
                    ['$match' => $filter],
                    ['$group' => [
                        '_id' => '$animal_id',
                        'total_clicks' => ['$sum' => '$click_count'],
                        'first_click' => ['$min' => '$timestamp'],
                        'last_click' => ['$max' => '$timestamp']
                    ]],
                    ['$lookup' => [
                        'from' => 'animals', // Assuming you have an animals collection
                        'localField' => '_id',
                        'foreignField' => 'id',
                        'as' => 'animal_info'
                    ]],
                    ['$unwind' => '$animal_info'],
                ]);

                $animal_stats = [];
                foreach ($cursor as $doc) {
                    $animal_name = $doc['animal_info']['prenom'];
                    $total_clicks = $doc['total_clicks'];
                    $first_click = $doc['first_click']->toDateTime()->format('Y-m-d');
                    $last_click = $doc['last_click']->toDateTime()->format('Y-m-d');
                    $date_diff = (strtotime($last_click) - strtotime($first_click)) / (60 * 60 * 24);
                    $average_clicks = $date_diff > 0 ? $total_clicks / $date_diff : $total_clicks;

                    echo "<tr>";
                    echo "<td>{$animal_name}</td>";
                    echo "<td>{$total_clicks}</td>";
                    echo "<td>" . round($average_clicks, 2) . "</td>";
                    echo "</tr>";

                    $animal_stats[] = [
                        'animal' => $animal_name,
                        'total_clicks' => $total_clicks
                    ];
                }
                ?>
            </tbody>
        </table>

        <!-- Chart showing clicks by animal -->
        <div class="chart-container">
            <canvas id="animalChart"></canvas>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var ctx = document.getElementById('animalChart').getContext('2d');
            var animalChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode(array_column($animal_stats, 'animal')); ?>,
                    datasets: [{
                        label: 'Clicks',
                        data: <?php echo json_encode(array_column($animal_stats, 'total_clicks')); ?>,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>

</body>

</html>
