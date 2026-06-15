<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../../public/login.php");
    exit();
}

$user = $_SESSION['user'];

require_once '../../controllers/ResourceController.php';

$controller = new ResourceController();
$resources = $controller->index();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Resources - CSB System</title>
    <link rel="stylesheet" href="../../assets/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=device-width">
</head>
<body class="app-layout-body">

<div class="app-container">
    <!-- SIDEBAR -->
    <?php include __DIR__ . '/../sidebar.php'; ?>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="content-header">
            <h2>Resources Management</h2>
        </div>

        <div class="content-card">
            <?php if ($user['role'] === 'admin'): ?>
            <div class="top-actions">
                <a href="create.php" class="btn btn-create">
                    <!-- Plus Icon -->
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Create Resource
                </a>
            </div>
            <?php endif; ?>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Location</th>
                        <th>Capacity</th>
                        <th>Status</th>
                        <?php if ($user['role'] === 'admin'): ?>
                            <th style="text-align: right;">Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $resources->fetch_assoc()): ?>
                    <tr>
                        <td>#<?= $row['id'] ?></td>
                        <td><strong><?= htmlspecialchars($row['name']) ?></strong></td>
                        <td>
                            <span class="status info" style="background-color: var(--primary-light); color: var(--primary); font-weight: 500;">
                                <?= htmlspecialchars($row['category_name'] ?? 'Category ID: ' . $row['category_id']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($row['location']) ?></td>
                        <td><?= htmlspecialchars($row['capacity']) ?> people</td>
                        <td>
                            <span class="status <?= $row['status'] ?>">
                                <?= ucfirst($row['status']) ?>
                            </span>
                        </td>
                        <?php if ($user['role'] === 'admin'): ?>
                        <td style="text-align: right; display: flex; gap: 8px; justify-content: flex-end;">
                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-edit" style="padding: 6px 12px; font-size: 13px;">Edit</a>
                            <a href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this resource?')" class="btn btn-delete" style="padding: 6px 12px; font-size: 13px;">
                                Delete
                            </a>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endwhile; ?>
                    <?php if ($resources->num_rows === 0): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-light); padding: 30px;">
                                No resources found.
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