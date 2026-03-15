
<!DOCTYPE html>
<html lang="en">
<?php
include("header.php"); 
// Connect to the database
include 'config2.php';
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Zoo Arcadia </title>
</head>
<body>

    <!-- About the Zoo Section -->
    <section class="about py-1">
        <div class="container_homepage text-center">
            <h2 class="mb-4">About Our Zoo</h2>
            <p class="lead">Our zoo is home to a wide variety of animals from all around the globe. We strive to create a safe, enriching environment for both our animals and visitors. Visit us and experience the magic of wildlife up close.</p>
        </div>
    </section>

    <!-- Featured Animals Section -->
    <section id="animals" class="featured-animals py-2 bg-grey">
        <div class="container_homepage">
            <h2 class="text-center  mb-4">Meet Our Featured Animals</h2>
            <div class="row">
                <?php
                // Query to select 3 random animals
                $sql = "SELECT prenom, race, description, image FROM animals ORDER BY RAND() LIMIT 3";
                $stmt = $pdo->query($sql);

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '<div class="col-md-4 mb-4">';
                    echo '<div class="card">';
                    echo '<img src="data:image/jpeg;base64,' . base64_encode($row['image']) . '" class="card-img-top" alt="' . htmlspecialchars($row['race']) . '" style="object-fit: contain; height: 200px;">';
                    echo '<div class="card-body">';
                    echo '<h5 class="card-title">' . htmlspecialchars($row['prenom']) . '</h5>';
                    echo '<p class="card-text">' . htmlspecialchars($row['description']) . '</p>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
                ?>
            </div>
            <a href="animal_zone.php" class="btn btn-success btn-lg mt-3">Visualisez nos animaux</a>
        </div>
    </section>

    <!-- Reviews Section -->
    <section id="reviews" class="reviews bg-grey">
        <div class="container_homepage">
            <h2 class="text-center  mb-4">Visitor Reviews</h2>
            <div class="row">
                <?php
                // Query to select 3 random reviews
                $sql = "SELECT visitor_name, review, rating FROM reviews WHERE approved = 1 ORDER BY RAND() LIMIT 3";
                $stmt = $pdo->query($sql);

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '<div class="col-md-4 mb-4">';
                    echo '<div class="card">';
                    echo '<div class="card-bodyrv">';
                    echo '<h5 class="card-titlerv">' . htmlspecialchars($row['visitor_name']) . '</h5>';
                    echo '<p class="card-textrv">' . htmlspecialchars($row['review']) . '</p>';
                    echo '<p class="text-warning">Rating: ' . str_repeat('★', $row['rating']) . '</p>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
                ?>
            </div>
            <!-- <a href="submit_review.php" class="btn btn-success btn-lg mt-3">Leave a Review</a> -->
        </div>
    </section>

    <!-- Habitats Section -->
    <section id="habitats" class="habitats py-1">
        <div class="container_homepage">
            <h2 class="text-center  mb-4">Explore Our Habitats</h2>
            <div class="row">
                <?php
                // Query to select 3 random habitats
                $sql = "SELECT name, image FROM habitats ORDER BY RAND() LIMIT 3";
                $stmt = $pdo->query($sql);

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '<div class="col-md-4 mb-4">';
                    echo '<a href="animal_zone.php#' . strtolower(str_replace(' ', '-', $row['name'])) . '">';
                    echo '<div class="card">';
                    echo '<img src="data:image/jpeg;base64,' . base64_encode($row['image']) . '" class="card-img-top" alt="' . htmlspecialchars($row['name']) . '" style="object-fit: contain; height: 200px;">';
                    echo '<div class="card-body">';
                    echo '<h5 class="card-title">' . htmlspecialchars($row['name']) . '</h5>';
                    echo '</div>';
                    echo '</div>';
                    echo '</a>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </section>


    <!-- Visit Us Section -->
    <section class="visit-us">
        <div class="container_homepage text-center">
            <h2>Plan Your Visit</h2>
            <p>Our zoo is open every day from 9 AM to 6 PM. Come and explore the wild! Get your tickets online or at the gate.</p>
            <!-- <a href="book.php" class="btn btn-success btn-lg">Buy Tickets Now</a> -->
        </div>
    </section>

    <!-- Footer -->

  
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
<?php
    include("footer.php"); 
   
    ?>
</html>
