<?php
require 'vendor/autoload.php'; // MongoDB client

// MongoDB Connection
$client = new MongoDB\Client("mongodb://localhost:27017");
$db = $client->zoo_database;  // Replace with your actual database name
$collection = $db->clicks;    // Replace with your actual collection name

// Function to log clicks to MongoDB
function logClick($animalId, $name) {
    global $collection;

    // Check if there's an existing record for this animal
    $record = $collection->findOne(['animal_id' => (int)$animalId]);

    if ($record) {
        // Increment the click count for existing records
        $collection->updateOne( 
        ['animal_id' => (int)$animalId],
        [
            '$inc' => ['click_count' => 1],
            '$set' => ['animal_prenom' => $name],
            '$currentDate' => ['last_click_timestamp' => true]
        ]
        );
    } else {
        // Create a new record if one doesn't exist
        $collection->insertOne([
            'animal_id' => (int)$animalId,
            'animal_prenom' => $name,
            'click_count' => 1,
            'first_click_timestamp' => date("Y-m-d H:i:s"),
            'last_click_timestamp' => date("Y-m-d H:i:s"), // This will add the current timestamp
        ]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (isset($input['animalId']) && isset($input['name'])) {
        logClick($input['animalId'], $input['name']);
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
    }
}
?>
