<?php
session_start();
require_once '../config/database.php';

$message = "";
$message_class = "error";

// Fetch departments for dropdown list
$dept_result = $conn->query("SELECT * FROM departments");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $student_code = trim($_POST['student_code']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $department_id = !empty($_POST['department_id']) ? (int)$_POST['department_id'] : null;

    // Check if email already exists
    $check_sql = "SELECT id FROM users WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    if ($check_stmt->get_result()->num_rows > 0) {
        $message = "Email is already registered!";
    } else {
        // Start transaction
        $conn->begin_transaction();
        try {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // 1. Insert into users table as student
            $user_sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'student')";
            $user_stmt = $conn->prepare($user_sql);
            $user_stmt->bind_param("sss", $name, $email, $hashed_password);
            $user_stmt->execute();
            $user_id = $conn->insert_id;

            // 2. Insert into user_profiles table
            $profile_sql = "INSERT INTO user_profiles (user_id, student_code, phone, address, department_id) VALUES (?, ?, ?, ?, ?)";
            $profile_stmt = $conn->prepare($profile_sql);
            $profile_stmt->bind_param("isssi", $user_id, $student_code, $phone, $address, $department_id);
            $profile_stmt->execute();

            $conn->commit();
            $message = "Account created successfully! You can now sign in.";
            $message_class = "success";
        } catch (Exception $e) {
            $conn->rollback();
            $message = "Registration failed: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register - CSB System</title>
    <link rel="stylesheet" href="../assets/style.css?v=1.1">
    <meta name="viewport" content="width=device-width, initial-scale=device-width">
    <style>
        .register-card {
            width: 950px;
        }
        .form-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px 18px;
        }
        .form-grid-2 .input-group {
            margin-bottom: 0;
        }
        .form-grid-2 label {
            margin-bottom: 4px;
            margin-top: 8px;
        }
        .form-grid-2 label:first-child {
            margin-top: 0;
        }
    </style>
</head>
<body class="login-body">

<div class="login-card register-card">
    <!-- LEFT PANEL: Illustration -->
    <div class="login-left">
        <!-- SVG Graphic -->
        <svg width="220" height="220" viewBox="0 0 240 240" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Background element -->
            <rect x="20" y="30" width="200" height="180" rx="20" fill="#f0fdf4"/>
            <!-- Mobile device body -->
            <rect x="70" y="50" width="100" height="140" rx="14" fill="#ffffff" stroke="#22c55e" stroke-width="6"/>
            <!-- Screen header area -->
            <path d="M70 75H170" stroke="#22c55e" stroke-width="2"/>
            <circle cx="120" cy="62" r="4" fill="#22c55e"/>
            <!-- Chat bubble green -->
            <path d="M125 100C125 91.7157 131.716 85 140 85H170C178.284 85 185 91.7157 185 100V110C185 118.284 178.284 125 170 125H145L132 135V125C128.022 125 125 121.716 125 117.778V100Z" fill="#86efac"/>
            <circle cx="145" cy="105" r="3" fill="#ffffff"/>
            <circle cx="155" cy="105" r="3" fill="#ffffff"/>
            <circle cx="165" cy="105" r="3" fill="#ffffff"/>
            <!-- Cute checkmark in grid -->
            <circle cx="120" cy="140" r="16" fill="#22c55e"/>
            <path d="M112 140L117 145L128 134" stroke="#ffffff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <div class="login-left-title" style="color: #22c55e;">Join Our Campus</div>
        <div class="login-left-desc">Tạo tài khoản sinh viên để đăng ký mượn phòng tự học, phòng máy tính và thiết bị nhanh chóng.</div>
    </div>

    <!-- RIGHT PANEL: Registration Form -->
    <div class="login-right" style="padding: 30px 45px;">
        <div class="login-right-title">Create Account</div>
        <div class="login-right-subtitle">Register as a new Student.</div>

        <?php if($message): ?>
            <div class="message <?= $message_class ?>"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-grid-2">
                <!-- Row 1 -->
                <div>
                    <label>Full Name</label>
                    <div class="input-group">
                        <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <input type="text" name="name" placeholder="Nguyen Van A" required>
                    </div>
                </div>
                <div>
                    <label>Phone Number</label>
                    <div class="input-group">
                        <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <input type="text" name="phone" placeholder="0912345678" required>
                    </div>
                </div>

                <!-- Row 2 -->
                <div>
                    <label>Student Code</label>
                    <div class="input-group">
                        <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                        </svg>
                        <input type="text" name="student_code" placeholder="SV123456" required>
                    </div>
                </div>
                <div>
                    <label>Department</label>
                    <select name="department_id" required style="margin-bottom: 0; padding: 10px 14px;">
                        <option value="">Select Faculty</option>
                        <?php while($dept = $dept_result->fetch_assoc()): ?>
                            <option value="<?= $dept['id'] ?>"><?= htmlspecialchars($dept['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Row 3 -->
                <div style="grid-column: span 2;">
                    <label>Address</label>
                    <div class="input-group">
                        <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <input type="text" name="address" placeholder="Hanoi, Vietnam" required>
                    </div>
                </div>

                <!-- Row 4 -->
                <div>
                    <label>Email (Username)</label>
                    <div class="input-group">
                        <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <input type="email" name="email" placeholder="student@student.edu.vn" required>
                    </div>
                </div>
                <div>
                    <label>Password</label>
                    <div class="input-group">
                        <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <input type="password" name="password" placeholder="••••••••" required>
                    </div>
                </div>
            </div>

            <!-- Submit Button (Green Theme) -->
            <button class="btn btn-submit" type="submit" style="margin-top: 24px; background-color: #22c55e; box-shadow: 0 4px 10px rgba(34, 197, 94, 0.2);">Sign Up</button>
        </form>

        <div class="login-bottom-link">
            Already have an account? <a href="login.php">Sign In</a>
        </div>
    </div>
</div>

</body>
</html>
