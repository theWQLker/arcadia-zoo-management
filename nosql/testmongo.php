<?php
require_once __DIR__ . '/vendor/autoload.php';

try {
    // Connect to MongoDB
    $client = new MongoDB\Client("mongodb://localhost:27017");
    
    // Select a database and collection
    $database = $client->selectDatabase('zoo_database');
    $collection = $database->selectCollection('animal_stats');
    
    // Insert some test data
    $insertResult = $collection->insertMany([
        [
            'animal_id' => 1,
            'name' => 'Lion',
            'views' => 10,
            'clicks' => 5
        ],
        [
            'animal_id' => 2,
            'name' => 'Elephant',
            'views' => 15,
            'clicks' => 8
        ],
        [
            'animal_id' => 3,
            'name' => 'Penguin',
            'views' => 20,
            'clicks' => 12
        ]
    ]);
    
    echo "Inserted " . $insertResult->getInsertedCount() . " documents\n";
    
    // Retrieve and display the inserted documents
    $documents = $collection->find();
    foreach ($documents as $document) {
        echo "Animal: " . $document['name'] . ", Views: " . $document['views'] . ", Clicks: " . $document['clicks'] . "\n";
    }

} catch (Exception $e) {
    echo "An error occurred: " . $e->getMessage() . "\n";
}
?>