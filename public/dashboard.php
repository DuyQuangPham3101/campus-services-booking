<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];
require_once '../config/database.php';

// Fetch dynamic database counts for dashboard cards
$total_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings")->fetch_assoc()['count'];
$total_resources = $conn->query("SELECT COUNT(*) as count FROM resources")->fetch_assoc()['count'];
$total_time_slots = $conn->query("SELECT COUNT(*) as count FROM time_slots")->fetch_assoc()['count'];
$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$pending_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'pending'")->fetch_assoc()['count'];
$approved_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'approved'")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - CSB System</title>
    <link rel="stylesheet" href="../assets/style.css?v=1.1">
    <meta name="viewport" content="width=device-width, initial-scale=device-width">
</head>
<body class="app-layout-body">

<div class="app-container">
    <!-- SIDEBAR -->
    <?php include '../views/sidebar.php'; ?>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="content-header">
            <h2>Dashboard Overview</h2>
            <div style="font-family: 'Outfit'; font-weight: 500; font-size: 15px; color: var(--text-muted);">
                Welcome back, <strong style="color: var(--primary);"><?= htmlspecialchars($user['name']) ?></strong> (<?= ucfirst($user['role']) ?>)
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="dashboard-card">
                <h3>Total Bookings</h3>
                <p><?= $total_bookings ?></p>
            </div>

            <?php if ($user['role'] === 'admin' || $user['role'] === 'lecturer'): ?>
            <div class="dashboard-card">
                <h3>Total Resources</h3>
                <p><?= $total_resources ?></p>
            </div>

            <div class="dashboard-card">
                <h3>Total Time Slots</h3>
                <p><?= $total_time_slots ?></p>
            </div>

            <div class="dashboard-card">
                <h3>Total Users</h3>
                <p><?= $total_users ?></p>
            </div>
            <?php endif; ?>

            <div class="dashboard-card pending-card">
                <h3>Pending Bookings</h3>
                <p><?= $pending_bookings ?></p>
            </div>

            <div class="dashboard-card approved-card">
                <h3>Approved Bookings</h3>
                <p><?= $approved_bookings ?></p>
            </div>
        </div>
    </div>
</div>

</body>
</html>