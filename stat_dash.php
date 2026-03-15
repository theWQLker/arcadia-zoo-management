<!DOCTYPE html>
<html lang="en">
<?php include("header.php"); ?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zoo Animal Statistics</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
      
        .filters {
            background-color: #f4f4f4;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .filters input[type="date"],
        .filters input[type="submit"] {
            padding: 5px;
            margin-right: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #2c3e50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .chart-container {
            margin-top: 30px;
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container_homepage">
        <h1>Zoo Animal Statistics Dashboard</h1>
        <div class="filters">
            <form id="filter-form" method="GET">
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date"
                    value="<?php echo isset($_GET['start_date']) ? htmlspecialchars($_GET['start_date']) : ''; ?>">

                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date"
                    value="<?php echo isset($_GET['end_date']) ? htmlspecialchars($_GET['end_date']) : ''; ?>">

                <input type="submit" value="Apply Filter">
            </form>
        </div>

        <h2>Click Statistics by Animal</h2>
        <table>
            <thead>
                <tr>
                    <th>Animal Name</th>
                    <th>Total Clicks</th>
                    <th>First Click</th>
                    <th>Last Click</th>
                    <th>Avg. Clicks/Day</th>
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

                // Date filtering
                $start_date = isset($_GET['start_date']) ? date(strtotime($_GET['start_date']) * 1000) : null;
                $end_date = isset($_GET['end_date']) ? date((strtotime($_GET['end_date']) + 86400) * 1000) : null; // End of the day
                
                $match_stage = [];

                // Add date range to filter if provided
                if ($start_date && $end_date) {
                    $match_stage['last_click_timestamp'] = ['$gte' => $start_date, '$lte' => $end_date];
                }

                // Fetch clicks by animal
                $pipeline = [];

                // Only add $match stage if there are filters
                if (!empty($match_stage)) {
                    $pipeline[] = ['$match' => $match_stage];
                }

                $pipeline = array_merge($pipeline, [
                    [
                        '$group' => [
                            '_id' => '$animal_id',
                            'animal_prenom' => ['$first' => '$animal_prenom'],
                            'total_clicks' => ['$sum' => '$click_count'],
                            'first_click' => ['$min' => '$first_click_timestamp'],
                            'last_click' => ['$max' => '$last_click_timestamp']
                        ]
                    ],
                    ['$sort' => ['total_clicks' => -1]] // Sort by total clicks in descending order
                ]);

                $cursor = $clicksCollection->aggregate($pipeline);

                $animal_stats = [];
                foreach ($cursor as $doc) {
                    $animal_name = $doc['animal_prenom'];
                    $total_clicks = $doc['total_clicks'];

                    // Handle date formatting
                    $first_click = formatDate($doc['first_click']);
                    $last_click = formatDate($doc['last_click']);

                    $date_diff = (strtotime($last_click) - strtotime($first_click)) / (60 * 60 * 24);
                    $average_clicks = $date_diff > 0 ? $total_clicks / $date_diff : $total_clicks;

                    echo "<tr>";
                    echo "<td>{$animal_name}</td>";
                    echo "<td>{$total_clicks}</td>";
                    echo "<td>{$first_click}</td>";
                    echo "<td>{$last_click}</td>";
                    echo "<td>" . round($average_clicks, 2) . "</td>";
                    echo "</tr>";

                    $animal_stats[] = [
                        'animal' => $animal_name,
                        'total_clicks' => $total_clicks
                    ];
                }

                // Helper function to format dates
                function formatDate($date)
                {
                    if ($date instanceof MongoDB\BSON\UTCDateTime) {
                        return $date->toDateTime()->format('Y-m-d');
                    } elseif (is_string($date)) {
                        return date('Y-m-d', strtotime($date));
                    } else {
                        return 'N/A'; // or any default value
                    }
                }
                ?>

            </tbody>
        </table>

        <div class="chart-container">
            <canvas id="animalChart"></canvas>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var ctx = document.getElementById('animalChart').getContext('2d');
            var animalChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode(array_column($animal_stats, 'animal')); ?>,
                    datasets: [{
                        label: 'Total Clicks',
                        data: <?php echo json_encode(array_column($animal_stats, 'total_clicks')); ?>,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of Clicks'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Animals'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Total Clicks per Animal'
                        }
                    }
                }
            });
        });
    </script>
</body>

</html>