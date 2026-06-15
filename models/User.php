<?php

require_once __DIR__ . '/../config/database.php';

class User
{
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    // GET ALL USERS
    public function getAll()
    {
        $sql = "SELECT * FROM users";
        return $this->conn->query($sql);
    }

    // GET USER BY ID
    public function getById($id)
    {
        $sql = "SELECT * FROM users WHERE id = ?";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param("i", $id);

        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    // CREATE USER
    public function create($name, $email, $password, $role)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users(name, email, password, role)
                VALUES (?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param(
            "ssss",
            $name,
            $email,
            $hashedPassword,
            $role
        );

        return $stmt->execute();
    }

    // UPDATE USER
    public function update($id, $name, $email, $role)
    {
        $sql = "UPDATE users
                SET
                    name = ?,
                    email = ?,
                    role = ?
                WHERE id = ?";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param(
            "sssi",
            $name,
            $email,
            $role,
            $id
        );

        return $stmt->execute();
    }

    // DELETE USER
    public function delete($id)
    {
        $sql = "DELETE FROM users WHERE id = ?";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }
}
?>