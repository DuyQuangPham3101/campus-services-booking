<?php

require_once '../../controllers/TimeSlotController.php';

$controller = new TimeSlotController();

$message = "";
$class = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $result = $controller->store($_POST);

    if ($result === true) {

        $message = "Time slot created successfully!";
        $class = "success";

    } else {

        $message = "Failed to create time slot!";
        $class = "error";
    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Create Time Slot</title>

    <link rel="stylesheet" href="../../assets/style.css">

</head>

<body>

<div class="container">

    <h1>Create Time Slot</h1>

    <?php if($message): ?>

        <div class="message <?= $class ?>">
            <?= $message ?>
        </div>

    <?php endif; ?>

    <form method="POST">

        <label>Slot Name</label>

        <input
            type="text"
            name="slot_name"
            required
        >

        <label>Start Time</label>

        <input
            type="time"
            name="start_time"
            required
        >

        <label>End Time</label>

        <input
            type="time"
            name="end_time"
            required
        >

        <label>Day Of Week</label>

        <select name="day_of_week">

            <option value="Monday">Monday</option>
            <option value="Tuesday">Tuesday</option>
            <option value="Wednesday">Wednesday</option>
            <option value="Thursday">Thursday</option>
            <option value="Friday">Friday</option>
            <option value="Saturday">Saturday</option>
            <option value="Sunday">Sunday</option>

        </select>

        <label>Peak Hour</label>

        <select name="is_peak_hour">

            <option value="1">Yes</option>
            <option value="0">No</option>

        </select>

        <button class="btn btn-submit" type="submit">
            Create Time Slot
        </button>

    </form>

    <br>

    <a href="index.php">
        Back to List
    </a>

</div>

</body>
</html>