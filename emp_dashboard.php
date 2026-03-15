<?php
include 'header.php';
include 'config2.php';

if (!isset($_SESSION['loggedin']) && $_SESSION['role'] !== 'emp') {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" type="text/css" href="ric6.css">
</head>

<div class="container-dash">
    <main class="dashboard-grid">
        <section class="card">
            <h2>Manage Reviews</h2>
            <p>View, add, edit, or delete site reviews.</p>
            <a href="mngreview.php" class="btn">See reviews</a>
        </section>
        <section class="card">
            <h2>Manage Services</h2>
            <p>View, add, edit, or delete services offered by the zoo.</p>
            <a href="mng_services.php" class="btn">Manage Services</a>
        </section>
        <section class="card">
            <h2>Log Animal feeding</h2>
            <p>Add, edit, or delete animal feeding records by name zone or weight.</p>
            <a href="log_feed.php" class="btn">Manage Animals</a>
        </section>
    </main>
</div>