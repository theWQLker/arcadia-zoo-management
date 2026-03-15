<?php
// Include common header and database configuration
include 'header.php';
include 'config2.php';

try {
    // Initialize error and success message variables
    $error_message = "";
    $success_message = "";

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = htmlspecialchars(trim($_POST['name']));
        $email = htmlspecialchars(trim($_POST['email']));
        $phone = htmlspecialchars(trim($_POST['phone']));
        $subject = htmlspecialchars(trim($_POST['subject']));
        $message = htmlspecialchars(trim($_POST['message']));

        // Validate input
        if (empty($name) || empty($email) || empty($message)) {
            $error_message = "Name, email, and message are required.";
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = "Invalid email format.";
        } else {
            // Store the form submission in the database
            $stmt = $pdo->prepare("INSERT INTO contact_submissions (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$name, $email, $phone, $subject, $message])) {
                $success_message = "Your message has been sent successfully.";
            } else {
                $error_message = "There was an error sending your message. Please try again later.";
            }
        }
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
    <title>Contact Form</title>
</head>

<body>
    <div class="container">
        <main>
            <section>
                <div class="container">
                    <h2>Contact Form</h2>
                    <?php
                    if ($error_message) {
                        echo "<p style='color: red;'>$error_message</p>";
                    }
                    if ($success_message) {
                        echo "<p style='color: green;'>$success_message</p>";
                    }
                    ?>
                    <form action="cntform.php" method="post">
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" required><br>

                        <label for="email">Email Address:</label>
                        <input type="email" id="email" name="email" required><br>

                        <label for="phone">Phone Number (optional):</label>
                        <input type="text" id="phone" name="phone"><br>

                        <label for="subject">Subject:</label>
                        <input type="text" id="subject" name="subject"><br>

                        <label for="message">Message:</label>
                        <textarea id="message" name="message" rows="5" required></textarea><br>

                        <button type="submit">Submit</button>
                    </form>
                </div>
            </section>
        </main>
    </div>
    <?php
   include 'footer.php'
   ?>
</body>

</html>
