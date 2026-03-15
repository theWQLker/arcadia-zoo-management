<?php
// Database connection
include 'header.php';
include 'config2.php';
// Fetch animal details

// Fetch the animal information from the database
$animal_id = isset($_GET['id']) ? (int)$_GET['id'] : 0; // Replace with dynamic ID as needed
$query = $pdo->prepare("SELECT * FROM animals WHERE id = :id");
$query->execute(['id' => $animal_id]);
$animal = $query->fetch(PDO::FETCH_ASSOC);

if (!$animal) {
    echo "Animal not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($animal['prenom']); ?> - Zoo Arcadia</title>

</head>

<body>

    <main class="full-width-container">
        <section class="animal-header">
            <h2 class="anim"><?php echo htmlspecialchars($animal['prenom']); ?></h2>
            <img src="data:image/jpeg;base64,<?php echo base64_encode($animal['image']); ?>" alt="<?php echo htmlspecialchars($animal['prenom']); ?>">
        </section>

        <section class="animal-info">
            <h3>Identity Card</h3>
            <p><strong>Class, Order, and Family:</strong> <?php echo htmlspecialchars($animal['race']); ?></p>
            <p><strong>Species:</strong> <?php echo htmlspecialchars($animal['species']); ?></p>
            <p><strong>Lifespan:</strong> <?php echo htmlspecialchars($animal['lifespan']); ?></p>
            <p><strong>Size & Weight:</strong> <?php echo htmlspecialchars($animal['size_weight']); ?></p>
            <p><strong>Gestation:</strong> <?php echo htmlspecialchars($animal['gestation']); ?></p>
            <p><strong>Natural Habitat:</strong> <?php echo htmlspecialchars($animal['natural_habitat']); ?></p>
            <p><strong>Diet:</strong> <?php echo htmlspecialchars($animal['diet']); ?></p>
            <p><strong>Native Region:</strong> <?php echo htmlspecialchars($animal['native_region']); ?></p>
        </section>

        <section class="animal-lifestyle">
            <h3>Lifestyle</h3>
            <p><?php echo nl2br(htmlspecialchars($animal['lifestyle'])); ?></p>
        </section>

        <section class="animal-distinctive-features">
            <h3>Distinctive Features</h3>
            <p><?php echo nl2br(htmlspecialchars($animal['distinctive_features'])); ?></p>
        </section>
        
    </main>
    <footer>
    <p>&copy; 2024 Zoo Arcadia. All rights reserved.</p>
    </footer>
</body>

</html>