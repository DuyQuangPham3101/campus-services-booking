<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../../public/login.php");
    exit();
}

$user = $_SESSION['user'];

if ($user['role'] !== 'admin') {
    die("Access denied. Only admins can manage booking policies.");
}

require_once '../../controllers/BookingPolicyController.php';

$controller = new BookingPolicyController();
$policies = $controller->index();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Booking Policies - CSB System</title>
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
            <h2>Booking Policies Management</h2>
        </div>

        <div class="content-card">
            <div class="top-actions">
                <a href="create.php" class="btn btn-create">
                    <!-- Plus Icon -->
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Create Policy
                </a>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Category Name</th>
                        <th>Rule Type</th>
                        <th>Value</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $policies->fetch_assoc()): ?>
                    <tr>
                        <td>#<?= $row['id'] ?></td>
                        <td>
                            <span class="status info" style="background-color: var(--primary-light); color: var(--primary); font-weight: 500;">
                                <?= htmlspecialchars($row['category_name']) ?>
                            </span>
                        </td>
                        <td><strong><?= htmlspecialchars($row['rule_type']) ?></strong></td>
                        <td><?= htmlspecialchars($row['value']) ?></td>
                        <td style="text-align: right; display: flex; gap: 8px; justify-content: flex-end;">
                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-edit" style="padding: 6px 12px; font-size: 13px;">Edit</a>
                            <a href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this policy?')" class="btn btn-delete" style="padding: 6px 12px; font-size: 13px;">
                                Delete
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if ($policies->num_rows === 0): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-light); padding: 30px;">
                                No booking policies found.
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
