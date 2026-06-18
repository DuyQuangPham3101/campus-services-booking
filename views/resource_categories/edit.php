<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../../public/login.php");
    exit();
}

$user = $_SESSION['user'];

if ($user['role'] !== 'admin') {
    die("Access denied. Only admins can edit resource categories.");
}

require_once '../../controllers/ResourceCategoryController.php';

$controller = new ResourceCategoryController();

$id = $_GET['id'] ?? 0;
$category = $controller->getCategory($id);

if (!$category) {
    die("Category not found.");
}

$message = "";
$class = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $result = $controller->updateCategory($id, $_POST);

    if ($result === true) {
        header("Location: index.php");
        exit();
    } else {
        $message = $result;
        $class = "error";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Resource Category - CSB System</title>
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
            <h2>Edit Resource Category #<?= htmlspecialchars($id) ?></h2>
        </div>

        <div class="content-card" style="max-width: 800px;">
            <?php if($message): ?>
                <div class="message <?= $class ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <form method="POST" id="editCategoryForm">
                <label>Category Name</label>
                <input type="text" name="name" id="name" required value="<?= htmlspecialchars($category['name']) ?>">

                <label>Description</label>
                <textarea name="description" id="description" rows="3"><?= htmlspecialchars($category['description']) ?></textarea>

                <label>Location</label>
                <input type="text" name="location" id="location" value="<?= htmlspecialchars($category['location']) ?>">

                <label>Max Capacity</label>
                <input type="number" name="max_capacity" id="max_capacity" required min="1" value="<?= htmlspecialchars($category['max_capacity']) ?>">

                <label>Requires Approval</label>
                <select name="requires_approval" id="requires_approval">
                    <option value="1" <?= $category['requires_approval'] ? 'selected' : '' ?>>Yes</option>
                    <option value="0" <?= !$category['requires_approval'] ? 'selected' : '' ?>>No</option>
                </select>

                <label>Max Booking per Week</label>
                <input type="number" name="max_booking_per_week" id="max_booking_per_week" required min="1" value="<?= htmlspecialchars($category['max_booking_per_week']) ?>">

                <label>Open Time</label>
                <input type="time" name="open_time" id="open_time" value="<?= htmlspecialchars($category['open_time']) ?>">

                <label>Close Time</label>
                <input type="time" name="close_time" id="close_time" value="<?= htmlspecialchars($category['close_time']) ?>">

                <div style="display: flex; gap: 15px; margin-top: 25px;">
                    <button class="btn btn-primary" type="submit" style="flex: 1;">Update Category</button>
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('editCategoryForm').addEventListener('submit', function(e) {
    let name = document.getElementById('name').value.trim();
    let max_capacity = document.getElementById('max_capacity').value;
    let max_booking = document.getElementById('max_booking_per_week').value;

    if (name === '') {
        e.preventDefault();
        alert('Category name cannot be empty.');
        return;
    }
    if (max_capacity < 1) {
        e.preventDefault();
        alert('Max capacity must be at least 1.');
        return;
    }
    if (max_booking < 1) {
        e.preventDefault();
        alert('Max booking per week must be at least 1.');
        return;
    }
});
</script>

</body>
</html>
