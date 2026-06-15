<?php

require_once __DIR__ . '/../config/database.php';

class Booking
{
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    // =========================================
    // READ ALL BOOKINGS
    // =========================================
    public function getAll()
    {
        $sql = "SELECT * FROM bookings";

        return $this->conn->query($sql);
    }

    // =========================================
    // CREATE BOOKING
    // =========================================
    public function create(
        $user_id,
        $resource_id,
        $time_slot_id,
        $booking_date,
        $status
    )
    {
        // 1. Fetch user role
        $user_sql = "SELECT role FROM users WHERE id = ?";
        $user_stmt = $this->conn->prepare($user_sql);
        $user_stmt->bind_param("i", $user_id);
        $user_stmt->execute();
        $user_res = $user_stmt->get_result()->fetch_assoc();
        $user_role = $user_res ? $user_res['role'] : 'student';

        // 2. Fetch slot details (peak hour)
        $slot_sql = "SELECT is_peak_hour FROM time_slots WHERE id = ?";
        $slot_stmt = $this->conn->prepare($slot_sql);
        $slot_stmt->bind_param("i", $time_slot_id);
        $slot_stmt->execute();
        $slot_res = $slot_stmt->get_result()->fetch_assoc();
        $is_peak_hour = $slot_res ? (int)$slot_res['is_peak_hour'] : 0;

        // 3. Enforce Policy: Students cannot book > 2 peak slots per week
        if ($is_peak_hour && $user_role === 'student') {
            $week_sql = "SELECT COUNT(*) as count 
                         FROM bookings b
                         JOIN time_slots t ON b.time_slot_id = t.id
                         WHERE b.user_id = ? 
                           AND t.is_peak_hour = 1 
                           AND b.status IN ('approved', 'pending') 
                           AND YEARWEEK(b.booking_date, 1) = YEARWEEK(?, 1)";
            $week_stmt = $this->conn->prepare($week_sql);
            $week_stmt->bind_param("is", $user_id, $booking_date);
            $week_stmt->execute();
            $week_count = $week_stmt->get_result()->fetch_assoc()['count'];
            if ($week_count >= 2) {
                return "Policy violation: Students cannot book more than 2 peak hour slots per week!";
            }
        }

        // 4. Fetch resource category policy details (requires approval)
        $res_sql = "SELECT rc.requires_approval 
                    FROM resources r 
                    JOIN resource_categories rc ON r.category_id = rc.id 
                    WHERE r.id = ?";
        $res_stmt = $this->conn->prepare($res_sql);
        $res_stmt->bind_param("i", $resource_id);
        $res_stmt->execute();
        $res_cat = $res_stmt->get_result()->fetch_assoc();
        $requires_approval = $res_cat ? (int)$res_cat['requires_approval'] : 0;

        $final_status = $status;
        if ($requires_approval) {
            $final_status = 'pending';
        } else {
            $final_status = 'approved';
        }

        // 5. BUSINESS RULE: Prevents double booking for the same resource, slot, and date
        $check = "SELECT * FROM bookings
                  WHERE resource_id = ?
                  AND time_slot_id = ?
                  AND booking_date = ?
                  AND status IN ('approved', 'pending')";

        $stmt = $this->conn->prepare($check);
        $stmt->bind_param(
            "iis",
            $resource_id,
            $time_slot_id,
            $booking_date
        );
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return "This time slot is already booked!";
        }

        // 6. INSERT BOOKING
        $sql = "INSERT INTO bookings
                (
                    user_id,
                    resource_id,
                    time_slot_id,
                    booking_date,
                    status
                )
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "iiiss",
            $user_id,
            $resource_id,
            $time_slot_id,
            $booking_date,
            $final_status
        );

        if ($stmt->execute()) {
            return true;
        } else {
            return $stmt->error;
        }
    }

    // =========================================
    // GET BOOKING BY ID
    // =========================================
    public function getById($id)
    {
        $sql = "SELECT * FROM bookings WHERE id = ?";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param("i", $id);

        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    // =========================================
    // UPDATE BOOKING
    // =========================================
    public function update(
        $id,
        $user_id,
        $resource_id,
        $time_slot_id,
        $booking_date,
        $status
    )
    {

        $sql = "UPDATE bookings
                SET
                    user_id = ?,
                    resource_id = ?,
                    time_slot_id = ?,
                    booking_date = ?,
                    status = ?
                WHERE id = ?";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param(
            "iiissi",
            $user_id,
            $resource_id,
            $time_slot_id,
            $booking_date,
            $status,
            $id
        );

        if ($stmt->execute()) {

            return true;

        } else {

            return $stmt->error;
        }
    }

    // =========================================
    // DELETE BOOKING
    // =========================================
    public function delete($id)
    {

        $sql = "DELETE FROM bookings WHERE id = ?";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {

            return true;

        } else {

            return $stmt->error;
        }
    }
    // =========================================
// GET ALL USERS
// =========================================
public function getUsers()
{
    return $this->conn->query("
        SELECT id, name
        FROM users
        ORDER BY name
    ");
}

// =========================================
// GET ALL RESOURCES
// =========================================
public function getResources()
{
    return $this->conn->query("
        SELECT id, name
        FROM resources
        ORDER BY name
    ");
}

// =========================================
// GET ALL TIME SLOTS
// =========================================
public function getTimeSlots()
{
    return $this->conn->query("
        SELECT
            id,
            slot_name,
            start_time,
            end_time
        FROM time_slots
        ORDER BY id
    ");
}
}

?>