<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../../public/login.php");
    exit();
}

$user = $_SESSION['user'];

if ($user['role'] !== 'admin') {
    die("Access denied. Only admins can create booking policies.");
}

require_once '../../controllers/BookingPolicyController.php';
require_once '../../models/ResourceCategory.php';

$controller = new BookingPolicyController();
$rc = new ResourceCategory();
$categories = $rc->getAll();

$message = "";
$class = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $result = $controller->store($_POST);

    if ($result === true) {
        $message = "Policy created successfully!";
        $class = "success";
    } else {
        $message = $result;
        $class = "error";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Booking Policy - CSB System</title>
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
            <h2>Create New Booking Policy</h2>
        </div>

        <div class="content-card" style="max-width: 800px;">
            <?php if($message): ?>
                <div class="message <?= $class ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <form method="POST" id="createPolicyForm">
                <label>Select Category</label>
                <select name="category_id" id="category_id" required>
                    <option value="">Choose a category...</option>
                    <?php while($category = $categories->fetch_assoc()): ?>
                        <option value="<?= $category['id'] ?>">
                            <?= htmlspecialchars($category['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <label>Rule Type</label>
                <input type="text" name="rule_type" id="rule_type" required placeholder="e.g. max_peak_slots_per_week">

                <label>Value</label>
                <input type="number" name="value" id="value" required placeholder="e.g. 2">

                <div style="display: flex; gap: 15px; margin-top: 25px;">
                    <button class="btn btn-primary" type="submit" style="flex: 1;">Create Policy</button>
                    <a href="index.php" class="btn btn-secondary">Back to List</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('createPolicyForm').addEventListener('submit', function(e) {
    let category_id = document.getElementById('category_id').value;
    let rule_type = document.getElementById('rule_type').value.trim();
    let value = document.getElementById('value').value;

    if (category_id === '') {
        e.preventDefault();
        alert('Please select a category.');
        return;
    }
    if (rule_type === '') {
        e.preventDefault();
        alert('Rule type cannot be empty.');
        return;
    }
    if (value === '') {
        e.preventDefault();
        alert('Value cannot be empty.');
        return;
    }
});
</script>

</body>
</html>
