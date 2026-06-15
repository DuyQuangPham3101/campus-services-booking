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
    <title>Campus Resource Usage Reports</title>
    <link rel="stylesheet" href="../../assets/style.css">
    <style>
        .report-section {
            margin-top: 40px;
        }
        .stats-summary {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .stat-box {
            flex: 1;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 0 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .stat-box h3 {
            margin: 0;
            color: #666;
            font-size: 16px;
        }
        .stat-box .number {
            font-size: 36px;
            font-weight: bold;
            color: #007bff;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="top-bar">
        <h1>Resource Utilization Reports</h1>
        <div>
            <a href="../../public/dashboard.php">
                <button class="btn btn-edit">Dashboard</button>
            </a>
            <a href="../../public/logout.php">
                <button class="btn btn-delete">Logout</button>
            </a>
        </div>
    </div>

    <!-- STATS SUMMARY CARDS -->
    <div class="stats-summary">
        <div class="stat-box">
            <h3>Total Booking Requests</h3>
            <div class="number"><?= $generalStats['total'] ?></div>
        </div>
        <div class="stat-box">
            <h3>Approved Bookings</h3>
            <div class="number" style="color: #28a745;"><?= $generalStats['approved'] ?></div>
        </div>
        <div class="stat-box">
            <h3>Pending Bookings</h3>
            <div class="number" style="color: #fd7e14;"><?= $generalStats['pending'] ?></div>
        </div>
        <div class="stat-box">
            <h3>Cancelled Bookings</h3>
            <div class="number" style="color: #dc3545;"><?= $generalStats['cancelled'] ?></div>
        </div>
    </div>

    <!-- RESOURCE USAGE FREQUENCY -->
    <div class="report-section">
        <h2>Resource Booking Frequency (Approved Bookings only)</h2>
        <table>
            <tr>
                <th>Resource ID</th>
                <th>Resource Name</th>
                <th>Category</th>
                <th>Location</th>
                <th>Times Booked</th>
            </tr>
            <?php while ($row = $resourceUsage->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['category_name']) ?></td>
                <td><?= htmlspecialchars($row['location']) ?></td>
                <td style="font-weight: bold; color: #007bff;"><?= $row['booking_count'] ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <!-- PEAK HOUR UTILIZATION -->
    <div class="report-section" style="margin-bottom: 45px;">
        <h2>Peak Hour Slots Usage</h2>
        <table>
            <tr>
                <th>Slot Name</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Approved Bookings Count</th>
            </tr>
            <?php while ($row = $peakUsage->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['slot_name']) ?></td>
                <td><?= $row['start_time'] ?></td>
                <td><?= $row['end_time'] ?></td>
                <td style="font-weight: bold; color: #fd7e14;"><?= $row['usage_count'] ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>

</body>
</html>
