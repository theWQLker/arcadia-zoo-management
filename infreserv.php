<?php
// Include common header and database configuration
include 'header.php';
include 'config2.php';

try {
    // Fetch available services (excluding service with ID 1)
    $query = "SELECT id, name FROM services WHERE id != 1";
    $stmt = $pdo->query($query);
    $tours = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch contact information
    $stmt = $pdo->prepare("SELECT type, value FROM contact_info");
    $stmt->execute();
    $contact_info = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $contact_info[$row['type']] = nl2br(htmlspecialchars($row['value']));
    }

    // Check if the form has been submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Retrieve form data
        $visitor_username = $_POST['visitor_username'];
        $visitor_name = $_POST['visitor_name'];
        $visitor_lastname = $_POST['visitor_lastname'];
        $service_id = $_POST['service_id'];
        $booking_date = $_POST['booking_date'];
        $status = 'Pending';

        // Check if visitor already exists in the visitors table
        $query = "SELECT username FROM visitors WHERE username = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$visitor_username]);
        $visitor = $stmt->fetch();

        // If visitor doesn't exist, add them to the visitors table
        if (!$visitor) {
            $query = "INSERT INTO visitors (username, name, lastname) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$visitor_username, $visitor_name, $visitor_lastname]);
        }

        // Insert booking details into the bookings table
        $query = "INSERT INTO bookings (visitor_username, service_id, booking_date, status) 
                  VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$visitor_username, $service_id, $booking_date, $status]);

        echo "Réservation créée avec succès !";
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Tour and Contact</title>
</head>
<body>
    <div class="container">

        <section>
            <h2>Book your zoo tour</h2>
            <form method="post" onsubmit="return validateForm()">
                <label for="visitor_username">Reservation Name:</label>
                <input type="text" id="visitor_username" name="visitor_username" placeholder="team_simba, mowgli_crew, welovegiraffes" required>

                <label for="visitor_name">First name:</label>
                <input type="text" id="visitor_name" name="visitor_name" required>

                <label for="visitor_lastname">Last Name:</label>
                <input type="text" id="visitor_lastname" name="visitor_lastname" required>

                <label for="service_id">Choose Tour:</label>
                <select id="service_id" name="service_id" required>
                    <?php foreach ($tours as $tour): ?>
                        <option value="<?php echo htmlspecialchars($tour['id']); ?>"><?php echo htmlspecialchars($tour['name']); ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="booking_date">Date:</label>
                <input type="date" id="booking_date" name="booking_date" required>

                <label for="is_pair">
                    <input type="checkbox" id="is_pair" name="is_pair" value="1" onclick="checkOptions(this, 'is_group')">
                    Couple/Pair?
                </label>

                <label for="is_group">
                    <input type="checkbox" id="is_group" name="is_group" value="1" onclick="checkOptions(this, 'is_pair')">
                    Group Reservation?
                </label>

                <div id="group_size_container" style="display:none;">
                    <label for="group_size">Group count:</label>
                    <input type="number" id="group_size" name="group_size" min="2" max="8">
                </div>

                <button type="submit">Send Reservation</button>
            </form>
        </section>

        <section>
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
            <p><strong>Zoo Hours:</strong><br>
                <?php echo isset($contact_info['Hours']) ? $contact_info['Hours'] : 'Not available'; ?>
            </p>
        </section>

        <section>
            <h2>Follow Us</h2>
            <p>
                <a href="#">Facebook</a><br>
                <a href="#">Twitter</a><br>
                <a href="#">Instagram</a><br>
                <a href="#">LinkedIn</a>
            </p>
        </section>

        <section>
            <h2>Location Map</h2>
            <!-- Add Google Maps embed URL here -->
        </section>

        <section>
            <h2>Accessibility</h2>
            <p>Information on accessible entrances and facilities.</p>
        </section>

        <section>
            <h2>FAQ</h2>
            <p><a href="#">Click here</a> to view frequently asked questions.</p>
        </section>

        <section>
            <p>For more details on how we handle your information, please read our <a href="#">Privacy Policy</a>.</p>
        </section>

        <a href="services.php">Return to Services</a>
    </div>

    <script>
        function checkOptions(checkbox, otherOptionId) {
            var otherOption = document.getElementById(otherOptionId);
            if (checkbox.checked) {
                otherOption.checked = false;
            }
            toggleGroupSize();
        }

        function toggleGroupSize() {
            var isGroup = document.getElementById('is_group');
            var groupSizeContainer = document.getElementById('group_size_container');
            groupSizeContainer.style.display = isGroup.checked ? 'block' : 'none';
        }

        function validateForm() {
            var isGroup = document.getElementById('is_group');
            var groupSize = document.getElementById('group_size');

            if (isGroup.checked && (!groupSize.value || groupSize.value < 2)) {
                alert("Veuillez spécifier une taille de groupe valide (minimum 2 personnes).");
                return false;
            }
            return true;
        }
    </script>
</body>
<?php include("footer.php"); ?>
</html>