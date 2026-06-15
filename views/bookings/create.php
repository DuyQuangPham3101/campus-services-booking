<?php

session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../../public/login.php");
    exit();
}

require_once '../../controllers/BookingController.php';

$controller = new BookingController();
$users = $controller->getUsers();

$resources = $controller->getResources();

$timeSlots = $controller->getTimeSlots();

$message = "";
$class = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $result = $controller->store($_POST);

    if ($result === true) {

        $message = "Booking created successfully!";
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

    <title>Create Booking</title>

    <link rel="stylesheet" href="../../assets/style.css">

</head>

<body>

<div class="container">

    <h1>Create Booking</h1>

    <?php if($message): ?>

        <div class="message <?= $class ?>">
            <?= $message ?>
        </div>

    <?php endif; ?>

    <form method="POST">

        <label>User</label>

<select name="user_id" required>

    <option value="">
        Select User
    </option>

    <?php while($user = $users->fetch_assoc()): ?>

        <option value="<?= $user['id'] ?>">
            <?= $user['name'] ?>
        </option>

    <?php endwhile; ?>

</select>

        <label>Resource</label>

<select name="resource_id" required>

    <option value="">
        Select Resource
    </option>

    <?php while($resource = $resources->fetch_assoc()): ?>

        <option value="<?= $resource['id'] ?>">
            <?= $resource['name'] ?>
        </option>

    <?php endwhile; ?>

</select>

        <label>Time Slot</label>

<select name="time_slot_id" required>

    <option value="">
        Select Time Slot
    </option>

    <?php while($slot = $timeSlots->fetch_assoc()): ?>

        <option value="<?= $slot['id'] ?>">

            <?= $slot['slot_name'] ?>
            (<?= $slot['start_time'] ?>
            - <?= $slot['end_time'] ?>)

        </option>

    <?php endwhile; ?>

</select>

        <label>Booking Date</label>

        <input
            type="date"
            name="booking_date"
            required
        >

        <label>Status</label>

        <select name="status">

            <option value="pending">
                Pending
            </option>

            <option value="approved">
                Approved
            </option>

        </select>

        <button class="btn btn-submit" type="submit">
            Create Booking
        </button>

    </form>

    <br>

    <a href="index.php">
        Back to List
    </a>

</div>

</body>
</html>