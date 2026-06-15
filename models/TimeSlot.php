<?php

require_once __DIR__ . '/../config/database.php';

class TimeSlot
{
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    // GET ALL
    public function getAll()
    {
        $sql = "SELECT * FROM time_slots ORDER BY start_time ASC";

        return $this->conn->query($sql);
    }

    // GET BY ID
    public function getById($id)
    {
        $sql = "SELECT * FROM time_slots WHERE id = ?";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param("i", $id);

        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    // CREATE
    public function create(
        $slot_name,
        $start_time,
        $end_time,
        $day_of_week,
        $is_peak_hour
    ) {

        $sql = "INSERT INTO time_slots
                (
                    slot_name,
                    start_time,
                    end_time,
                    day_of_week,
                    is_peak_hour
                )
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param(
            "sssii",
            $slot_name,
            $start_time,
            $end_time,
            $day_of_week,
            $is_peak_hour
        );

        return $stmt->execute();
    }

    // UPDATE
    public function update(
        $id,
        $slot_name,
        $start_time,
        $end_time,
        $day_of_week,
        $is_peak_hour
    ) {

        $sql = "UPDATE time_slots
                SET
                    slot_name = ?,
                    start_time = ?,
                    end_time = ?,
                    day_of_week = ?,
                    is_peak_hour = ?
                WHERE id = ?";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param(
            "sssiii",
            $slot_name,
            $start_time,
            $end_time,
            $day_of_week,
            $is_peak_hour,
            $id
        );

        return $stmt->execute();
    }

    // DELETE
    public function delete($id)
    {
        $sql = "DELETE FROM time_slots WHERE id = ?";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }
}

?>