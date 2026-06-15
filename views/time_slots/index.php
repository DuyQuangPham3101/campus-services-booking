<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../../public/login.php");
    exit();
}

require_once '../../controllers/TimeSlotController.php';

$controller = new TimeSlotController();
$timeslots = $controller->index();

// Map numeric day of week to Vietnamese/English string
$days = [
    1 => 'Monday',
    2 => 'Tuesday',
    3 => 'Wednesday',
    4 => 'Thursday',
    5 => 'Friday',
    6 => 'Saturday',
    7 => 'Sunday'
];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Time Slots - CSB System</title>
    <link rel="stylesheet" href="../../assets/style.css?v=1.1">
    <meta name="viewport" content="width=device-width, initial-scale=device-width">
</head>
<body class="app-layout-body">

<div class="app-container">
    <!-- SIDEBAR -->
    <?php include __DIR__ . '/../sidebar.php'; ?>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="content-header">
            <h2>Time Slots Management</h2>
        </div>

        <div class="content-card">
            <div class="top-actions">
                <a href="create.php" class="btn btn-create">
                    <!-- Plus Icon -->
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Create Time Slot
                </a>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Slot Name</th>
                        <th>Day of Week</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Peak Hour?</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $timeslots->fetch_assoc()): ?>
                    <tr>
                        <td>#<?= $row['id'] ?></td>
                        <td><strong><?= htmlspecialchars($row['slot_name']) ?></strong></td>
                        <td><?= $days[$row['day_of_week']] ?? 'Unknown' ?></td>
                        <td><?= date("H:i", strtotime($row['start_time'])) ?></td>
                        <td><?= date("H:i", strtotime($row['end_time'])) ?></td>
                        <td>
                            <?php if ($row['is_peak_hour']): ?>
                                <span class="status pending">Peak Hour</span>
                            <?php else: ?>
                                <span class="status info" style="background-color: #f1f5f9; color: #64748b;">Off-Peak</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: right; display: flex; gap: 8px; justify-content: flex-end;">
                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-edit" style="padding: 6px 12px; font-size: 13px;">Edit</a>
                            <a href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this time slot?')" class="btn btn-delete" style="padding: 6px 12px; font-size: 13px;">
                                Delete
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if ($timeslots->num_rows === 0): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-light); padding: 30px;">
                                No time slots found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>