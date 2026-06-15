<?php

require_once '../../controllers/TimeSlotController.php';

$controller = new TimeSlotController();

$id = $_GET['id'];

$timeslot = $controller->show($id);

$message = "";
$class = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $result = $controller->update($id, $_POST);

    if ($result) {

        $message = "Time slot updated successfully!";
        $class = "success";

        $timeslot = $controller->show($id);

    } else {

        $message = "Failed to update time slot!";
        $class = "error";
    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Edit Time Slot</title>

    <link rel="stylesheet" href="../../assets/style.css">

</head>

<body>

<div class="container">

    <h1>Edit Time Slot</h1>

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
            value="<?= $timeslot['slot_name'] ?>"
            required
        >

        <label>Start Time</label>

        <input
            type="time"
            name="start_time"
            value="<?= $timeslot['start_time'] ?>"
            required
        >

        <label>End Time</label>

        <input
            type="time"
            name="end_time"
            value="<?= $timeslot['end_time'] ?>"
            required
        >

        <label>Day Of Week</label>

        <select name="day_of_week">

            <option value="Monday" <?= $timeslot['day_of_week'] == 'Monday' ? 'selected' : '' ?>>
                Monday
            </option>

            <option value="Tuesday" <?= $timeslot['day_of_week'] == 'Tuesday' ? 'selected' : '' ?>>
                Tuesday
            </option>

            <option value="Wednesday" <?= $timeslot['day_of_week'] == 'Wednesday' ? 'selected' : '' ?>>
                Wednesday
            </option>

            <option value="Thursday" <?= $timeslot['day_of_week'] == 'Thursday' ? 'selected' : '' ?>>
                Thursday
            </option>

            <option value="Friday" <?= $timeslot['day_of_week'] == 'Friday' ? 'selected' : '' ?>>
                Friday
            </option>

            <option value="Saturday" <?= $timeslot['day_of_week'] == 'Saturday' ? 'selected' : '' ?>>
                Saturday
            </option>

            <option value="Sunday" <?= $timeslot['day_of_week'] == 'Sunday' ? 'selected' : '' ?>>
                Sunday
            </option>

        </select>

        <label>Peak Hour</label>

        <select name="is_peak_hour">

            <option value="1" <?= $timeslot['is_peak_hour'] == 1 ? 'selected' : '' ?>>
                Yes
            </option>

            <option value="0" <?= $timeslot['is_peak_hour'] == 0 ? 'selected' : '' ?>>
                No
            </option>

        </select>

        <button class="btn btn-submit" type="submit">
            Update Time Slot
        </button>

    </form>

    <br>

    <a href="index.php">
        Back to List
    </a>

</div>

</body>
</html>