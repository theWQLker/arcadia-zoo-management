<?php

include 'header.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit a Review</title>
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center">Submit Your Review</h2>

        <?php
        include('config2.php'); // Include your database connection file

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $visitor_name = trim($_POST['visitor_name']);
            $review = trim($_POST['review']);
            $rating = (int)$_POST['rating'];

            if (empty($visitor_name) || empty($review) || $rating < 1 || $rating > 5) {
                echo '<div class="alert alert-danger">Please fill in all fields and provide a valid rating.</div>';
            } else {
                try {
                    $sql = "INSERT INTO reviews (visitor_name, review, rating) VALUES (:visitor_name, :review, :rating)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute(['visitor_name' => $visitor_name, 'review' => $review, 'rating' => $rating]);

                    echo '<div class="alert alert-success">Thank you for your review!</div>';
                } catch (Exception $e) {
                    echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                }
            }
        }
        ?>


        <form action="submit_review.php" method="POST">
            <div class="form-group">
                <label for="visitor_name">Your Name</label>
                <input type="text" class="form-control" id="visitor_name" name="visitor_name" required>
            </div>
            <div class="form-group">
                <label for="review">Your Review</label>
                <textarea class="form-control" id="review" name="review" rows="5" required></textarea>
            </div>
            <div class="form-group">
                <label for="rating">Rating</label>
                <select class="form-control" id="rating" name="rating" required>
                    <option value="5">5 - Excellent</option>
                    <option value="4">4 - Very Good</option>
                    <option value="3">3 - Good</option>
                    <option value="2">2 - Fair</option>
                    <option value="1">1 - Poor</option>
                </select>
            </div>
            <button type="submit" class="btn btn-lg btn-block">Submit Review</button>
        </form>

        <div class="text-center mt-4">
            <a href="homepage.php" class="btn btn-secondary">Back to Home</a>
        </div>
    </div>
</body>

</html>