<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../../public/login.php");
    exit();
}

require_once '../../controllers/UserController.php';

$controller = new UserController();
$users = $controller->index();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Users - CSB System</title>
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
            <h2>Users Management</h2>
        </div>

        <div class="content-card">
            <div class="top-actions">
                <a href="create.php" class="btn btn-create">
                    <!-- Plus Icon -->
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Create User
                </a>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $users->fetch_assoc()): ?>
                    <tr>
                        <td>#<?= $row['id'] ?></td>
                        <td><strong><?= htmlspecialchars($row['name']) ?></strong></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td>
                            <?php if ($row['role'] === 'admin'): ?>
                                <span class="status danger" style="background-color: #fde8e8; color: #9b1c1c; font-weight: 500;">Admin</span>
                            <?php elseif ($row['role'] === 'lecturer'): ?>
                                <span class="status pending" style="background-color: #fdf6b2; color: #723b13; font-weight: 500;">Lecturer</span>
                            <?php else: ?>
                                <span class="status info" style="background-color: #e1effe; color: #1e429f; font-weight: 500;">Student</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: right; display: flex; gap: 8px; justify-content: flex-end;">
                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-edit" style="padding: 6px 12px; font-size: 13px;">Edit</a>
                            <a href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this user?')" class="btn btn-delete" style="padding: 6px 12px; font-size: 13px;">
                                Delete
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if ($users->num_rows === 0): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-light); padding: 30px;">
                                No users found.
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