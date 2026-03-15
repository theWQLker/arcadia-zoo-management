<!DOCTYPE html>
<html lang="en">
<?php include("header.php"); ?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zoo Animals</title>

    <style>
        /* Existing CSS styles remain unchanged */
        .card_hb {
            margin: 10px;
            display: flex;
            flex-direction: column;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fff;
        }

        .card_hb img {
            width: 100%;
            height: auto;
            max-height: 220px;
            object-fit: cover;
        }

        .card-body_hb {
            text-align: center;
            flex-grow: 1;
            padding: 15px;
        }

        .habitat-title_hb {
            color: #006400;
            font-size: 58px;
            font-weight: bold;
            border-color: #45a049;
            margin-top: 20px;
        }

        .habitat-image {
            display: block;
            width: 100%;
            height: auto;
            object-fit: cover;
            max-height: 360px;
            margin-bottom: 20px;
        }

        .card-deck_hb {
            display: flex;
            flex-wrap: wrap;
            margin-left: -10px;
            margin-right: -10px;
        }

        .card-deck_hb .card_hb {
            flex: 1 1 calc(33.333% - 20px);
            margin: 10px;
            max-width: calc(33.333% - 20px);
        }

        @media (max-width: 768px) {
            .card-deck_hb .card_hb {
                flex: 1 1 calc(50% - 20px);
                max-width: calc(50% - 20px);
            }
        }

        @media (max-width: 576px) {
            .card-deck_hb .card_hb {
                flex: 1 1 calc(100% - 20px);
                max-width: calc(100% - 20px);
            }
        }

        .text-center {
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
        }

        h5 {
            font-size: 1.25rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .animal-info {
            font-size: 1rem;
            font-weight: normal;
            color: #555;
        }

        .btn-learn-more {
            margin-top: auto;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            text-decoration: none;
        }

        .btn-learn-more:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <div class="container back" style="padding-left: 25px;">
        <?php
        include 'config2.php'; // Include the database configuration file

        // Function to log statistics to a JSON file
        function logStatistics($action, $data)
        {
            $filename = 'statistics.json';
            $statistics = [];

            if (!file_exists($filename)) {
                file_put_contents($filename, json_encode([])); // Initialize with an empty array
            }
            // Push new log entry
            $statistics[] = [
                'action' => $action,
                'data' => $data,
                'timestamp' => date('Y-m-d H:i:s'),
            ];

            // Save back to JSON
            file_put_contents($filename, json_encode($statistics, JSON_PRETTY_PRINT));
        }

    
        
        try {
            $sql = "SELECT h.name as habitat, h.image as habitat_image, a.id, a.prenom, a.race, a.diet, a.description, a.characteristics, a.image as animal_image
                    FROM animals a
                    JOIN habitats h ON a.habitat_id = h.id
                    ORDER BY h.name";

            $stmt = $pdo->query($sql);
            $current_habitat = '';
            $first_row = true;

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($current_habitat != $row['habitat']) {
                    if (!$first_row) {
                        echo '</div>'; // Close the card deck for the previous habitat
                    }
                    $first_row = false;
                    $current_habitat = $row['habitat'];

                    // Start a new habitat section
                    echo '<div id="' . strtolower(str_replace(' ', '-', $row['habitat'])) . '" class="row habitat-title_hb">';
                    echo '<div class="col-12">';
                    echo '<p class="text-center">' . htmlspecialchars($row["habitat"]) . '</p>';
                    if (!empty($row['habitat_image'])) {
                        echo '<div class="text-center"><img src="data:image/jpeg;base64,' . base64_encode($row['habitat_image']) . '" alt="' . htmlspecialchars($row["habitat"]) . ' Habitat" class="habitat-image" /></div>';
                    }
                    echo '</div></div>'; // Close the row for the habitat title and image

                    // Start a new card deck for the cards
                    echo '<div class="card-deck_hb">';
                }

                // Card content (for animals)
                echo '<div class="card_hb">';
                echo '<img src="data:image/jpeg;base64,' . base64_encode($row['animal_image']) . '" alt="' . htmlspecialchars($row["prenom"]) . '" />';
                echo '<div class="card-body_hb">';
                echo '<h5>' . htmlspecialchars($row["prenom"]) . '</h5>';
                echo '<p class="animal-info">(' . htmlspecialchars($row["race"]) . ' - ' . htmlspecialchars($row["diet"]) . ')</p>';
                echo '<p>' . htmlspecialchars(substr($row["description"], 0, 40)) . '...</p>'; // Show only first 40 chars
                echo '<a href="animal_det.php?id=' . htmlspecialchars($row["id"]) . '" class="btn-learn-more" onclick="logClick(' . htmlspecialchars($row["id"]) . ')">Learn More</a>';
                echo '</div>';
                echo '</div>';
            }

            echo '</div>'; // Close the last card deck
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
        ?>
        
        
        <script>
            function logClick(animalId) {
                fetch('log_statistics.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ action: 'click', animalId: animalId })

                });
            }
        </script>
    </div>
</body>

</html>