<?php
// Include common header and database configuration
include 'header.php';
include 'config2.php';

try {
    // Fetch contact information
    $stmt = $pdo->prepare("SELECT type, value FROM contact_info");
    $stmt->execute();
    $contact_info = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $contact_info[$row['type']] = nl2br(htmlspecialchars($row['value']));
    }

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
</head>

<body>
    <div class="container">
        <main>
            <section>
                <div class="container">
                    <h2>Contact Information</h2>
                    <p><strong>Address:</strong><br>
                        <?php echo isset($contact_info['Address']) ? $contact_info['Address'] : 'Not available'; ?>
                    </p>
                    <p><strong>Phone Number:</strong><br>
                        <?php echo isset($contact_info['Phone']) ? $contact_info['Phone'] : 'Not available'; ?>
                    </p>
                    <p><strong>Email:</strong><br>
                        <?php echo isset($contact_info['Email']) ? $contact_info['Email'] : 'Not available'; ?>
                    </p>
                </div>
            </section>

            <section>
                <div class="container">
                    <h2>Business Hours</h2>
                    <p><strong>Zoo Hours:</strong><br>
                        <?php echo isset($contact_info['Hours']) ? $contact_info['Hours'] : 'Not available'; ?>
                    </p>
                </div>
            </section>

            <section>
                <div class="container">
                    <h2>Follow Us</h2>
                    <p>
                        <a href="#">Facebook</a><br>
                        <a href="#">Twitter</a><br>
                        <a href="#">Instagram</a><br>
                        <a href="#">LinkedIn</a>
                    </p>
                </div>
            </section>

            <section>
                <div class="container">
                    <h2>Location Map</h2>
                    <!-- Add Google Maps embed URL here -->
                </div>
            </section>

            <section>
                <h2>Accessibility</h2>
                <p>Information on accessible entrances and facilities.</p>
            </section>

            <section>
                <h2>FAQ</h2>
                <p><a href="faq.php">Click here</a> to view frequently asked questions.</p>
            </section>

            <section>
                <p>For more details on how we handle your information, please read our <a href="privacy_policy.php">Privacy Policy</a>.</p>
            </section>
        </main>
    </div>

    
</body>

<?php
    include 'footer.php'
    ?>
</html>
