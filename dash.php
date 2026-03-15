<?php
include 'header.php';
include 'config2.php';

if (!isset($_SESSION['loggedin']) && $_SESSION['role'] !== 'admin') {
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
            <h2>Manage User Accounts</h2>
            <p>View, add, edit, or delete user accounts.</p>
            <a href="mng_users.php" class="btn">Manage Users</a>
        </section>
        <section class="card">
            <h2>Manage Services</h2>
            <p>View, add, edit, or delete services offered by the zoo.</p>
            <a href="mng_services.php" class="btn">Manage Services</a>
        </section>
        <section class="card">
            <h2>Manage Animals</h2>
            <p>View, add, edit, or delete animal records.</p>
            <a href="mng_animals.php" class="btn">Manage Animals</a>
        </section>
        <section class="card">
            <h2>View Veterinary Reports</h2>
            <p>Access and manage veterinary reports for animals.</p>
            <a href="mng_vet_reports.php" class="btn">View Vet Reports</a>
        </section>
        <section class="card">
            <h2>Animal View statistics</h2>
            <p>Access and view animal popularity.</p>
            <a href="stat_dash.php" class="btn">View Vet Reports</a>
        </section>
    </main>
</div>