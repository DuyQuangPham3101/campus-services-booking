<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../../public/login.php");
    exit();
}

$user = $_SESSION['user'];
if ($user['role'] !== 'admin' && $user['role'] !== 'lecturer') {
    die("Access denied. Only admins and lecturers can view reports.");
}

require_once '../../controllers/ReportController.php';
$reportController = new ReportController();

$generalStats = $reportController->getGeneralStats();
$resourceUsage = $reportController->getResourceUsageFrequency();
$peakUsage = $reportController->getPeakHourUsage();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reports - CSB System</title>
    <link rel="stylesheet" href="../../assets/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=device-width">
    <style>
        .report-section {
            margin-top: 40px;
        }
        .dashboard-card.cancelled-card::before {
            background: linear-gradient(90deg, var(--danger) 0%, #dc2626 100%);
        }
    </style>
</head>
<body class="app-layout-body">

<div class="app-container">
    <!-- SIDEBAR -->
    <?php include __DIR__ . '/../sidebar.php'; ?>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="content-header">
            <h2>Resource Utilization Reports</h2>
        </div>

        <!-- STATS SUMMARY CARDS -->
        <div class="dashboard-grid">
            <div class="dashboard-card">
                <h3>Total Booking Requests</h3>
                <p><?= $generalStats['total'] ?></p>
            </div>
            <div class="dashboard-card approved-card">
                <h3>Approved Bookings</h3>
                <p><?= $generalStats['approved'] ?></p>
            </div>
            <div class="dashboard-card pending-card">
                <h3>Pending Bookings</h3>
                <p><?= $generalStats['pending'] ?></p>
            </div>
            <div class="dashboard-card cancelled-card">
                <h3>Cancelled Bookings</h3>
                <p><?= $generalStats['cancelled'] ?></p>
            </div>
        </div>

        <!-- RESOURCE USAGE FREQUENCY -->
        <div class="report-section">
            <h3 style="font-family: 'Outfit'; font-size: 18px; color: var(--text-main); margin-bottom: 15px;">Resource Booking Frequency (Approved Bookings only)</h3>
            <div class="content-card" style="margin-top: 0; padding: 0; overflow: hidden; border-radius: 12px;">
                <table style="margin-top: 0; border: none;">
                    <thead>
                        <tr>
                            <th>Resource ID</th>
                            <th>Resource Name</th>
                            <th>Category</th>
                            <th>Location</th>
                            <th style="text-align: right;">Times Booked</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $resourceUsage->fetch_assoc()): ?>
                        <tr>
                            <td>#<?= $row['id'] ?></td>
                            <td><strong><?= htmlspecialchars($row['name']) ?></strong></td>
                            <td>
                                <span class="status info" style="background-color: var(--primary-light); color: var(--primary); font-weight: 500;">
                                    <?= htmlspecialchars($row['category_name']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($row['location']) ?></td>
                            <td style="text-align: right; font-weight: 700; color: var(--primary); font-family: 'Outfit'; font-size: 16px; padding-right: 25px;">
                                <?= $row['booking_count'] ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- PEAK HOUR UTILIZATION -->
        <div class="report-section" style="margin-bottom: 45px;">
            <h3 style="font-family: 'Outfit'; font-size: 18px; color: var(--text-main); margin-bottom: 15px;">Peak Hour Slots Usage</h3>
            <div class="content-card" style="margin-top: 0; padding: 0; overflow: hidden; border-radius: 12px;">
                <table style="margin-top: 0; border: none;">
                    <thead>
                        <tr>
                            <th>Slot Name</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th style="text-align: right;">Approved Bookings Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $peakUsage->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($row['slot_name']) ?></strong></td>
                            <td><?= date("H:i", strtotime($row['start_time'])) ?></td>
                            <td><?= date("H:i", strtotime($row['end_time'])) ?></td>
                            <td style="text-align: right; font-weight: 700; color: var(--warning-text); font-family: 'Outfit'; font-size: 16px; padding-right: 25px;">
                                <?= $row['usage_count'] ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>
