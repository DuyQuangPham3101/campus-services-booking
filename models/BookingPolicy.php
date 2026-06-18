<?php

require_once __DIR__ . '/../config/database.php';

class BookingPolicy
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
        $sql = "SELECT bp.*, rc.name as category_name 
                FROM booking_policies bp 
                JOIN resource_categories rc ON bp.category_id = rc.id 
                ORDER BY bp.id ASC";
        return $this->conn->query($sql);
    }

    // GET BY ID
    public function getById($id)
    {
        $sql = "SELECT * FROM booking_policies WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // GET ALL BY CATEGORY
    public function getByCategoryId($category_id)
    {
        $sql = "SELECT * FROM booking_policies WHERE category_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    // GET SPECIFIC POLICY RULE FOR A CATEGORY
    public function getPolicyValue($category_id, $rule_type)
    {
        $sql = "SELECT value FROM booking_policies WHERE category_id = ? AND rule_type = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $category_id, $rule_type);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result ? (int)$result['value'] : null;
    }

    // CREATE
    public function create($category_id, $rule_type, $value)
    {
        $sql = "INSERT INTO booking_policies (category_id, rule_type, value) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("isi", $category_id, $rule_type, $value);
        return $stmt->execute();
    }

    // UPDATE
    public function update($id, $category_id, $rule_type, $value)
    {
        $sql = "UPDATE booking_policies SET category_id = ?, rule_type = ?, value = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("isii", $category_id, $rule_type, $value, $id);
        return $stmt->execute();
    }

    // DELETE
    public function delete($id)
    {
        $sql = "DELETE FROM booking_policies WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
