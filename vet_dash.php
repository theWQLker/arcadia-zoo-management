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
            <h2>VET Reports</h2>
            <p>View, add, edit, animal reports.</p>
            <a href="vet_reports.php" class="btn">Log Report</a>
        </section>
        
    </main>
</div>