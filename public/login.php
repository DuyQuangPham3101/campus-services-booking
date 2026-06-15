<?php

session_start();

require_once '../config/database.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users
            WHERE email = ?
            AND password = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("ss", $email, $password);

    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        $user = $result->fetch_assoc();

        $_SESSION['user'] = $user;

        header("Location: dashboard.php");

    } else {

        $message = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html>

<head>

    <title>Login</title>

    <link rel="stylesheet" href="../assets/style.css">

</head>

<body>

<div class="container">

    <h1>Login</h1>

    <?php if($message): ?>

        <div class="message error">
            <?= $message ?>
        </div>

    <?php endif; ?>

    <form method="POST">

        <label>Email</label>

        <input
            type="email"
            name="email"
            required
        >

        <label>Password</label>

        <input
            type="password"
            name="password"
            required
        >

        <button class="btn btn-submit" type="submit">
            Login
        </button>

    </form>

</div>

</body>
</html>