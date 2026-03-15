<?php
session_start(); // Start the session at the beginning

include 'config2.php';

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;

// Function to get the dashboard URL based on user role
function getDashboardUrl() {
    if (!$GLOBALS['isLoggedIn']) {
        return 'login.php'; // Redirect to login if not logged in
    }

    // Fetch user roles from the database
    global $pdo;
    $username = $_SESSION['username'];
    $query = "SELECT role_type FROM user_role_assignments WHERE username = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$username]);
    $userRoles = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (in_array('admin', $userRoles)) {
        return 'dash.php';
    } elseif (in_array('vet', $userRoles)) {
        return 'vet_dash.php';
    } elseif (in_array('emp', $userRoles)) {
        return 'emp_dashboard.php';
    } else {
        return 'default_dashboard.php'; // Fallback dashboard for users with no specific role
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/ric6.css">
</head>

<body background="./img/BGarcardia.jpg">
    <header class="header">
        <a href="homepage.php" class="logo">
            <img src="./img/ide-verte.png" alt="Zoo Logo">
            <span class="logo-text">Zoo Arcadia</span>
        </a>
        <nav class="nav-bar">
            <a href="services.php"><u>Services</u></a>
            <a href="animal_zone.php"><u>Habitats</u></a>
            <a href="Infreserv.php"><u>Infos et Réservations</u></a>
            <a href="cntform.php"><u>Contactez Nous</u></a>
            <div class="separator"></div>

            <?php if ($isLoggedIn): ?>
                <!-- Show logout icon/link -->
                <a href="logout.php" class="nav-link special-button">
                    Déconnexion
                </a>
                <a href="<?php echo getDashboardUrl(); ?>" style="font-weight: bold;">Dashboard</a>
            <?php else: ?>
                <!-- Show login link -->
                <a href="login.php" class="nav-link special-button">Connexion</a>
            <?php endif;?>
        </nav>
        <!-- Burger icon for mobile -->
        <div class="burger-container">
            <div class="burger-icon" onclick="toggleMenu()">
                <div class="bar"></div>
                <div class="bar"></div>
                <div class="bar"></div>
            </div>
        </div>
    </header>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const burgerIcon = document.querySelector('.burger-icon');
        const navBar = document.querySelector('.nav-bar');

        function toggleMenu() {
            navBar.classList.toggle('open');
        }

        function closeMenu(event) {
            // Check if the click is outside the burger menu and the nav bar
            if (!burgerIcon.contains(event.target) && !navBar.contains(event.target)) {
                navBar.classList.remove('open');
            }
        }

        // Toggle menu when burger icon is clicked
        burgerIcon.addEventListener('click', function(event) {
            event.stopPropagation(); // Prevent this click from immediately closing the menu
            toggleMenu();
        });

        // Close menu when clicking outside
        document.addEventListener('click', closeMenu);

        // Prevent menu from closing when clicking inside the nav bar
        navBar.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    });
</script>
</body>

</html>