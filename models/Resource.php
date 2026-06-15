<?php

require_once __DIR__ . '/../config/database.php';

class Resource
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
        $sql = "SELECT r.*, rc.name as category_name 
                FROM resources r
                JOIN resource_categories rc ON r.category_id = rc.id
                ORDER BY r.id";
        return $this->conn->query($sql);
    }

    // GET BY ID
    public function getById($id)
    {
        $sql = "SELECT * FROM resources WHERE id = ?";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param("i", $id);

        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    // CREATE
    public function create($category_id, $name, $location, $capacity, $status)
    {
        $sql = "INSERT INTO resources
                (category_id, name, location, capacity, status)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param(
            "issis",
            $category_id,
            $name,
            $location,
            $capacity,
            $status
        );

        return $stmt->execute();
    }

    // UPDATE
    public function update($id, $category_id, $name, $location, $capacity, $status)
    {
        $sql = "UPDATE resources
                SET
                    category_id = ?,
                    name = ?,
                    location = ?,
                    capacity = ?,
                    status = ?
                WHERE id = ?";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param(
            "issisi",
            $category_id,
            $name,
            $location,
            $capacity,
            $status,
            $id
        );

        return $stmt->execute();
    }

    // DELETE
    public function delete($id)
    {
        $sql = "DELETE FROM resources WHERE id = ?";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }
}
?>