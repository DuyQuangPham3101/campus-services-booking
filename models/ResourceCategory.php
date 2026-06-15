<?php

require_once __DIR__ . '/../config/database.php';

class ResourceCategory
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
        $sql = "SELECT * FROM resource_categories ORDER BY name ASC";
        return $this->conn->query($sql);
    }

    // GET BY ID
    public function getById($id)
    {
        $sql = "SELECT * FROM resource_categories WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // CREATE
    public function create(
        $name,
        $description,
        $location,
        $max_capacity,
        $requires_approval,
        $max_booking_per_week,
        $open_time,
        $close_time
    ) {
        $sql = "INSERT INTO resource_categories
                (name, description, location, max_capacity, requires_approval, max_booking_per_week, open_time, close_time)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "sssiisss",
            $name,
            $description,
            $location,
            $max_capacity,
            $requires_approval,
            $max_booking_per_week,
            $open_time,
            $close_time
        );
        return $stmt->execute();
    }

    // UPDATE
    public function update(
        $id,
        $name,
        $description,
        $location,
        $max_capacity,
        $requires_approval,
        $max_booking_per_week,
        $open_time,
        $close_time
    ) {
        $sql = "UPDATE resource_categories
                SET
                    name = ?,
                    description = ?,
                    location = ?,
                    max_capacity = ?,
                    requires_approval = ?,
                    max_booking_per_week = ?,
                    open_time = ?,
                    close_time = ?
                WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "sssiisssi",
            $name,
            $description,
            $location,
            $max_capacity,
            $requires_approval,
            $max_booking_per_week,
            $open_time,
            $close_time,
            $id
        );
        return $stmt->execute();
    }

    // DELETE
    public function delete($id)
    {
        $sql = "DELETE FROM resource_categories WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
