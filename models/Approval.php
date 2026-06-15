<?php

require_once __DIR__ . '/../config/database.php';

class Approval
{
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    // GET ALL APPROVALS
    public function getAll()
    {
        $sql = "SELECT a.*, b.booking_date, u.name as approved_by_name 
                FROM approvals a
                JOIN bookings b ON a.booking_id = b.id
                JOIN users u ON a.approved_by = u.id
                ORDER BY a.created_at DESC";
        return $this->conn->query($sql);
    }

    // CREATE APPROVAL
    public function create($booking_id, $approved_by, $status, $note)
    {
        $this->conn->begin_transaction();

        try {
            // 1. Insert approval log
            $sql = "INSERT INTO approvals (booking_id, approved_by, status, note) VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("iiss", $booking_id, $approved_by, $status, $note);
            $stmt->execute();

            // 2. Update booking status
            $sql_booking = "UPDATE bookings SET status = ? WHERE id = ?";
            $stmt_booking = $this->conn->prepare($sql_booking);
            $stmt_booking->bind_param("si", $status, $booking_id);
            $stmt_booking->execute();

            // 3. Log booking change
            $sql_log = "INSERT INTO booking_logs (booking_id, action, old_status, new_status, notes, changed_by) 
                        VALUES (?, 'update_status', 'pending', ?, ?, ?)";
            $stmt_log = $this->conn->prepare($sql_log);
            $stmt_log->bind_param("issi", $booking_id, $status, $note, $approved_by);
            $stmt_log->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            return $e->getMessage();
        }
    }
}
