<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../../public/login.php");
    exit();
}

require_once '../../controllers/ResourceController.php';
require_once '../../config/database.php';

$controller = new ResourceController();
$id = $_GET['id'];
$resource = $controller->getResource($id);

// Fetch resource categories for dropdown list
$categories = $conn->query("SELECT * FROM resource_categories ORDER BY name");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $controller->updateResource($id, $_POST);
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Resource - CSB System</title>
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
            <h2>Edit Resource #<?= htmlspecialchars($id) ?></h2>
        </div>

        <div class="content-card" style="max-width: 800px;">
            <form method="POST">
                <!-- Select Category -->
                <label>Select Category</label>
                <select name="category_id" required>
                    <option value="">Choose a category...</option>
                    <?php while($cat = $categories->fetch_assoc()): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($resource['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <!-- Name -->
                <label>Resource Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($resource['name']) ?>" required>

                <!-- Location -->
                <label>Location</label>
                <input type="text" name="location" value="<?= htmlspecialchars($resource['location']) ?>" required>

                <!-- Capacity -->
                <label>Capacity</label>
                <input type="number" name="capacity" value="<?= htmlspecialchars($resource['capacity']) ?>" required>

                <!-- Status -->
                <label>Status</label>
                <select name="status">
                    <option value="available" <?= ($resource['status'] == 'available') ? 'selected' : '' ?>>Available</option>
                    <option value="maintenance" <?= ($resource['status'] == 'maintenance') ? 'selected' : '' ?>>Maintenance</option>
                    <option value="unavailable" <?= ($resource['status'] == 'unavailable') ? 'selected' : '' ?>>Unavailable</option>
                </select>

                <div style="display: flex; gap: 15px; margin-top: 25px;">
                    <button class="btn btn-primary" type="submit" style="flex: 1;">Update Resource</button>
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>