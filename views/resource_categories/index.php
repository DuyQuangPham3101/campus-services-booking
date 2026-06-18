<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../../public/login.php");
    exit();
}

$user = $_SESSION['user'];

require_once '../../controllers/ResourceCategoryController.php';

$controller = new ResourceCategoryController();
$categories = $controller->index();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Resource Categories - CSB System</title>
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
            <h2>Resource Categories Management</h2>
        </div>

        <div class="content-card">
            <?php if ($user['role'] === 'admin'): ?>
            <div class="top-actions">
                <a href="create.php" class="btn btn-create">
                    <!-- Plus Icon -->
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Create Category
                </a>
            </div>
            <?php endif; ?>

            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Location</th>
                            <th>Max Capacity</th>
                            <th>Requires Approval</th>
                            <th>Max Booking/Week</th>
                            <th>Open Time</th>
                            <th>Close Time</th>
                            <?php if ($user['role'] === 'admin'): ?>
                                <th style="text-align: right;">Actions</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $categories->fetch_assoc()): ?>
                        <tr>
                            <td>#<?= $row['id'] ?></td>
                            <td><strong><?= htmlspecialchars($row['name']) ?></strong></td>
                            <td><?= htmlspecialchars($row['description']) ?></td>
                            <td><?= htmlspecialchars($row['location']) ?></td>
                            <td><?= htmlspecialchars($row['max_capacity']) ?></td>
                            <td>
                                <?php if ($row['requires_approval']): ?>
                                    <span class="status warning">Yes</span>
                                <?php else: ?>
                                    <span class="status info">No</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['max_booking_per_week']) ?></td>
                            <td><?= htmlspecialchars($row['open_time']) ?></td>
                            <td><?= htmlspecialchars($row['close_time']) ?></td>
                            <?php if ($user['role'] === 'admin'): ?>
                            <td style="text-align: right; display: flex; gap: 8px; justify-content: flex-end;">
                                <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-edit" style="padding: 6px 12px; font-size: 13px;">Edit</a>
                                <a href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this category?')" class="btn btn-delete" style="padding: 6px 12px; font-size: 13px;">
                                    Delete
                                </a>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endwhile; ?>
                        <?php if ($categories->num_rows === 0): ?>
                            <tr>
                                <td colspan="10" style="text-align: center; color: var(--text-light); padding: 30px;">
                                    No resource categories found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>
