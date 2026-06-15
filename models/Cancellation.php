<?php

require_once __DIR__ . '/../config/database.php';

class Cancellation
{
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    // GET ALL CANCELLATIONS
    public function getAll()
    {
        $sql = "SELECT c.*, b.booking_date, u.name as cancelled_by_name 
                FROM cancellations c
                JOIN bookings b ON c.booking_id = b.id
                JOIN users u ON c.cancelled_by = u.id
                ORDER BY c.cancelled_at DESC";
        return $this->conn->query($sql);
    }

    // CREATE CANCELLATION
    public function create($booking_id, $reason, $cancelled_by)
    {
        $this->conn->begin_transaction();

        try {
            // 1. Get current booking status for logging
            $sql_status = "SELECT status FROM bookings WHERE id = ?";
            $stmt_status = $this->conn->prepare($sql_status);
            $stmt_status->bind_param("i", $booking_id);
            $stmt_status->execute();
            $curr_status_res = $stmt_status->get_result()->fetch_assoc();
            $old_status = $curr_status_res ? $curr_status_res['status'] : 'unknown';

            // 2. Insert cancellation log
            $sql = "INSERT INTO cancellations (booking_id, reason, cancelled_by) VALUES (?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("isi", $booking_id, $reason, $cancelled_by);
            $stmt->execute();

            // 3. Update booking status to 'cancelled'
            $sql_booking = "UPDATE bookings SET status = 'cancelled' WHERE id = ?";
            $stmt_booking = $this->conn->prepare($sql_booking);
            $stmt_booking->bind_param("i", $booking_id);
            $stmt_booking->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return $e->getMessage();
        }
    }
}
