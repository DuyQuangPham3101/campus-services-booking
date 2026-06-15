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

        // BUSINESS RULE:
        // Không cho đặt trùng phòng cùng khung giờ cùng ngày

        $check = "SELECT * FROM bookings
                  WHERE resource_id = ?
                  AND time_slot_id = ?
                  AND booking_date = ?";

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

        // INSERT BOOKING

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
            $status
        );

        // DEBUG REAL MYSQL ERROR

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