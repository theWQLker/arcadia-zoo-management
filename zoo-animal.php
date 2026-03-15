<?php
require_once __DIR__ . '/vendor/autoload.php'; // Include Composer's autoloader
include 'config2.php'; // Include the database configuration file



// MongoDB connection
$client = new MongoDB\Client("mongodb://localhost:27017");
$database = $client->selectDatabase('zoo_database');
$collection = $database->selectCollection('animal_stats');

// Function to track animal view
function trackAnimalView($animalId) {
    global $collection;
    
    $currentDate = new DateTime();
    $season = getSeason($currentDate);
    $isSchoolHoliday = checkSchoolHoliday($currentDate);
    $weather = getWeather(); // You'll need to implement this function to fetch weather data

    $result = $collection->updateOne(
        ['animal_id' => $animalId, 'date' => $currentDate->format('Y-m-d')],
        [
            '$inc' => ['views' => 1],
            '$setOnInsert' => [
                'clicks' => 0,
                'season' => $season,
                'is_school_holiday' => $isSchoolHoliday,
                'weather' => $weather
            ]
        ],
        ['upsert' => true]
    );
    
    return $result->getModifiedCount() > 0 || $result->getUpsertedCount() > 0;
}

// Helper functions
function getSeason($date) {
    $month = $date->format('n');
    if ($month >= 3 && $month <= 5) return 'Spring';
    if ($month >= 6 && $month <= 8) return 'Summer';
    if ($month >= 9 && $month <= 11) return 'Autumn';
    return 'Winter';
}

function checkSchoolHoliday($date) {
    // Implement logic to check if the date is a school holiday
    // This is a placeholder function
    return false;
}

function getWeather() {
    // Implement logic to fetch current weather
    // This is a placeholder function
    return 'Sunny';
}

?>

<!DOCTYPE html>
<html lang="en">
<?php include("header.php"); ?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zoo Animals</title>
    <style>
        /* ... (existing styles) ... */
    </style>
</head>

<body>
    <div class="container back" style="padding-left: 25px;">
        <?php
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
                echo '<p>' . htmlspecialchars(substr($row["description"], 0, 40)) . '...</p>';
                // Modified Learn More button with click tracking
                echo '<a href="animal_det.php?id=' . htmlspecialchars($row["id"]) . '" class="btn-learn-more" onclick="trackAnimalClick(' . htmlspecialchars($row["id"]) . '); return true;">Learn More</a>';
                echo '</div>';
                echo '</div>';
            }

            echo '</div>'; // Close the last card deck
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
        ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    function trackAnimalClick(animalId) {
        $.ajax({
            url: 'track_click.php',
            method: 'POST',
            data: { animal_id: animalId },
            success: function(response) {
                console.log('Click tracked successfully');
            },
            error: function(xhr, status, error) {
                console.error('Error tracking click:', error);
            }
        });
    }
    </script>
</body>
</html>