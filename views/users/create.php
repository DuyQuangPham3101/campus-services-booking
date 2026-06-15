<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../../public/login.php");
    exit();
}

require_once '../../controllers/UserController.php';

$controller = new UserController();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $controller->store($_POST);
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create User - CSB System</title>
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
            <h2>Create New User</h2>
        </div>

        <div class="content-card" style="max-width: 800px;">
            <form method="POST">
                <!-- Name -->
                <label>Full Name</label>
                <input type="text" name="name" placeholder="e.g. Nguyen Van A" required>

                <!-- Email -->
                <label>Email Address</label>
                <input type="email" name="email" placeholder="username@campus.edu.vn" required>

                <!-- Password -->
                <label>Password</label>
                <input type="password" name="password" placeholder="••••••••" required>

                <!-- Role dropdown matching DB role constraints -->
                <label>Role</label>
                <select name="role" required>
                    <option value="student">Student</option>
                    <option value="lecturer">Lecturer</option>
                    <option value="admin">Admin</option>
                </select>

                <div style="display: flex; gap: 15px; margin-top: 25px;">
                    <button class="btn btn-primary" type="submit" style="flex: 1;">Create User</button>
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>