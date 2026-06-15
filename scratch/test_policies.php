<?php

// Test integration of booking policies, approvals, and cancellations.
// Make sure database has been initialized with database.sql first.

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../models/Approval.php';
require_once __DIR__ . '/../models/Cancellation.php';

echo "=== CAMPUS SERVICES BOOKING INTEGRATION TESTS ===\n\n";

// Clear previous bookings/logs to ensure clean slate for tests
global $conn;
$conn->query("SET FOREIGN_KEY_CHECKS = 0");
$conn->query("TRUNCATE TABLE cancellations");
$conn->query("TRUNCATE TABLE approvals");
$conn->query("TRUNCATE TABLE bookings");
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

$bookingModel = new Booking();
$approvalModel = new Approval();
$cancellationModel = new Cancellation();

// Test IDs based on sample data inserted in database.sql
$student_id = 3;  // Student Quang
$lecturer_id = 2; // Lecturer A

$study_room_id = 1; // regular study room (requires_approval = 0)
$lab_room_id = 2;   // specialized lab room (requires_approval = 1)

$slot_1 = 1; // Slot 1 (Monday, is_peak_hour = 0)
$slot_2 = 2; // Slot 2 (Monday, is_peak_hour = 0)
$slot_3 = 3; // Slot 3 (Monday, is_peak_hour = 1 - PEAK)
$slot_4 = 4; // Slot 4 (Monday, is_peak_hour = 1 - PEAK)

$booking_date = "2026-06-15"; // A Monday in 2026

// ============================================================
// TEST 1: Booking a regular room (should be auto approved)
// ============================================================
echo "Test 1: Booking regular room (requires_approval = 0)...\n";
$res1 = $bookingModel->create($student_id, $study_room_id, $slot_1, $booking_date, 'pending');
if ($res1 === true) {
    // Check status in DB
    $b = $conn->query("SELECT status FROM bookings WHERE user_id = $student_id AND resource_id = $study_room_id AND time_slot_id = $slot_1")->fetch_assoc();
    if ($b['status'] === 'approved') {
        echo "[SUCCESS] Regular booking was automatically APPROVED.\n\n";
    } else {
        echo "[FAILED] Regular booking status is: " . $b['status'] . "\n\n";
    }
} else {
    echo "[FAILED] Error: $res1\n\n";
}

// ============================================================
// TEST 2: Booking a specialized lab (should go to pending)
// ============================================================
echo "Test 2: Booking specialized lab (requires_approval = 1)...\n";
$res2 = $bookingModel->create($student_id, $lab_room_id, $slot_2, $booking_date, 'pending');
if ($res2 === true) {
    // Check status in DB
    $b = $conn->query("SELECT id, status FROM bookings WHERE user_id = $student_id AND resource_id = $lab_room_id AND time_slot_id = $slot_2")->fetch_assoc();
    $lab_booking_id = $b['id'];
    if ($b['status'] === 'pending') {
        echo "[SUCCESS] Lab booking is PENDING approval.\n\n";
    } else {
        echo "[FAILED] Lab booking status is: " . $b['status'] . "\n\n";
    }
} else {
    echo "[FAILED] Error: $res2\n\n";
}

// ============================================================
// TEST 3: Booking peak hours (Limit = 2/week)
// ============================================================
echo "Test 3: Booking first peak slot (Slot 3)...\n";
$res_peak1 = $bookingModel->create($student_id, $study_room_id, $slot_3, $booking_date, 'pending');
if ($res_peak1 === true) {
    echo "[SUCCESS] First peak slot booked successfully.\n";
} else {
    echo "[FAILED] First peak slot failed: $res_peak1\n";
}

echo "Booking second peak slot (Slot 4)...\n";
$res_peak2 = $bookingModel->create($student_id, $study_room_id, $slot_4, $booking_date, 'pending');
if ($res_peak2 === true) {
    echo "[SUCCESS] Second peak slot booked successfully.\n";
} else {
    echo "[FAILED] Second peak slot failed: $res_peak2\n";
}

echo "Booking third peak slot on a different day of the same week (say, next day Tuesday slot 3)...\n";
// Let's assume slot_3 is also used for Tuesday (it will succeed policy if same week check works)
// Note: day_of_week check isn't strictly checked in Booking, it checks booking_date's week
$tuesday_date = "2026-06-16";
$res_peak3 = $bookingModel->create($student_id, $study_room_id, $slot_3, $tuesday_date, 'pending');
if ($res_peak3 === true) {
    echo "[FAILED] Third peak slot in the same week should have been rejected!\n\n";
} else {
    echo "[SUCCESS] Rejected successfully with error message: \n         \"$res_peak3\"\n\n";
}

// ============================================================
// TEST 4: Approve the pending lab booking as lecturer
// ============================================================
echo "Test 4: Approving the pending lab booking (ID: $lab_booking_id)...\n";
$app_res = $approvalModel->create($lab_booking_id, $lecturer_id, 'approved', 'Approved for research project.');
if ($app_res === true) {
    $b = $conn->query("SELECT status FROM bookings WHERE id = $lab_booking_id")->fetch_assoc();
    $app_log = $conn->query("SELECT * FROM approvals WHERE booking_id = $lab_booking_id")->fetch_assoc();
    if ($b['status'] === 'approved' && $app_log) {
        echo "[SUCCESS] Lab booking APPROVED. Notes: " . $app_log['note'] . "\n\n";
    } else {
        echo "[FAILED] Status: " . $b['status'] . "\n\n";
    }
} else {
    echo "[FAILED] Error: $app_res\n\n";
}

// ============================================================
// TEST 5: Cancel a booking
// ============================================================
// Let's get the regular room booking ID
$reg_booking = $conn->query("SELECT id FROM bookings WHERE user_id = $student_id AND resource_id = $study_room_id AND time_slot_id = $slot_1")->fetch_assoc();
$reg_booking_id = $reg_booking['id'];

echo "Test 5: Cancelling regular room booking (ID: $reg_booking_id)...\n";
$cancel_res = $cancellationModel->create($reg_booking_id, 'Group meeting cancelled.', $student_id);
if ($cancel_res === true) {
    $b = $conn->query("SELECT status FROM bookings WHERE id = $reg_booking_id")->fetch_assoc();
    $c_log = $conn->query("SELECT * FROM cancellations WHERE booking_id = $reg_booking_id")->fetch_assoc();
    if ($b['status'] === 'cancelled' && $c_log) {
        echo "[SUCCESS] Booking status is CANCELLED. Reason: " . $c_log['reason'] . "\n\n";
    } else {
        echo "[FAILED] Status: " . $b['status'] . "\n\n";
    }
} else {
    echo "[FAILED] Error: $cancel_res\n\n";
}

echo "=== ALL TESTS COMPLETED ===\n";
