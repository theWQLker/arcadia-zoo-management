<?php
include 'config2.php';  // Include database connection
include 'header.php';     // Include header if needed

$query = "
SELECT s.id, s.name, s.description, s.price, s.availability_schedule, s.image
FROM services s
WHERE s.name != 'Gift Shop'  -- Exclude Gift Shop from display
ORDER BY s.id
";

$stmt = $pdo->query($query);
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zoo Services</title>
</head>

<body>
    <div class="container">
        <div class="explore-services">
            <img src="./img/wild.png" alt="Wildlife Experience">
            <h1>Explore Our Wide Range of Services</h1>
        </div>

        <?php if (!empty($services)): ?>
            <?php foreach ($services as $service): ?>
                <div class="service-block">
                    <?php if ($service['image']): ?>
                        <?php
                        $img = 'data:image/jpeg;base64,' . base64_encode($service['image']);
                        ?>
                        <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($service['name']); ?>">
                    <?php endif; ?>
                    <div>
                        <h2><?php echo htmlspecialchars($service['name']); ?></h2>
                        <p><?php echo htmlspecialchars($service['description']); ?></p>
                        <p class="price">Price: $<?php echo number_format($service['price'], 2); ?></p>
                        <p>Available: <?php echo htmlspecialchars($service['availability_schedule']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-services">No services available.</p>
        <?php endif; ?>

    </div>
</body>
<?php
include 'footer.php'
?>
</html>