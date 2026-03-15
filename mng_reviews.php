<!DOCTYPE html>
<html lang="en">

<head>
<?php
        include 'config2.php';
        include 'header.php';
    ?>    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reviews</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        /* Specific styles for the reviews page */
        .reviews-container {
            padding: 20px;
        }

        .reviews-title {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .reviews-table {
            width: 100%;
            margin-bottom: 30px;
            border-collapse: collapse;
        }

        .reviews-table th,
        .reviews-table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .reviews-table th {
            background-color: #f8f9fa;
        }

        .reviews-approve-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
        }

        .reviews-approve-btn:hover {
            background-color: #218838;
        }

        .reviews-delete-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
        }

        .reviews-delete-btn:hover {
            background-color: #c82333;
        }

        .no-reviews {
            text-align: center;
            font-size: 18px;
            color: #6c757d;
        }
    </style>
</head>

<body>
    <div class="container reviews-container">
        <h2 class="reviews-title">Manage Visitor Reviews</h2>
        <?php
        // Fetch all reviews with approved and unapproved statuses
        $stmt = $pdo->query("SELECT id, visitor_name, review, rating, review_date, approved FROM reviews ORDER BY review_date DESC");
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($reviews) > 0) {
            echo '<table class="reviews-table">';
            echo '<thead><tr><th>Visitor Name</th><th>Review</th><th>Rating</th><th>Review Date</th><th>Approved</th><th>Action</th></tr></thead><tbody>';
            foreach ($reviews as $review) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($review['visitor_name']) . '</td>';
                echo '<td>' . htmlspecialchars($review['review']) . '</td>';
                echo '<td>' . htmlspecialchars($review['rating']) . ' / 5</td>';
                echo '<td>' . htmlspecialchars($review['review_date']) . '</td>';
                echo '<td>' . ($review['approved'] ? 'Yes' : 'No') . '</td>';
                echo '<td>';
                if (!$review['approved']) {
                    echo '<form method="POST" style="display:inline-block;">';
                    echo '<input type="hidden" name="review_id" value="' . htmlspecialchars($review['id']) . '">';
                    echo '<button type="submit" name="approve" class="reviews-approve-btn">Approve</button>';
                    echo '</form>';
                }
                echo '<form method="POST" style="display:inline-block;">';
                echo '<input type="hidden" name="review_id" value="' . htmlspecialchars($review['id']) . '">';
                echo '<button type="submit" name="delete" class="reviews-delete-btn">Delete</button>';
                echo '</form>';
                echo '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        } else {
            echo '<p class="no-reviews">No reviews found.</p>';
        }

        // Approve review
        if (isset($_POST['approve'])) {
            $review_id = $_POST['review_id'];
            $update_stmt = $pdo->prepare("UPDATE reviews SET approved = 1 WHERE id = ?");
            $update_stmt->execute([$review_id]);
            header("Refresh:0"); // Refresh the page to update the status
        }

        // Delete review
        if (isset($_POST['delete'])) {
            $review_id = $_POST['review_id'];
            $delete_stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ?");
            $delete_stmt->execute([$review_id]);
            header("Refresh:0"); // Refresh the page after deleting
        }
        ?>
        <a href="dash.php" class="btn btn-primary mt-4">Return to Dashboard</a>
    </div>
</body>

</html>