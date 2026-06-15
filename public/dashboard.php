<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];

require_once '../config/database.php';

// Fetch dynamic database counts for dashboard cards
$total_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings")->fetch_assoc()['count'];
$total_resources = $conn->query("SELECT COUNT(*) as count FROM resources")->fetch_assoc()['count'];
$total_time_slots = $conn->query("SELECT COUNT(*) as count FROM time_slots")->fetch_assoc()['count'];
$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$pending_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'pending'")->fetch_assoc()['count'];
$approved_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'approved'")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html>

<head>
    <title>Dashboard - CSB System</title>
    <link rel="stylesheet" href="../assets/style.css">

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
        }

        .dashboard {
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR STYLE */
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
            color: #f8fafc;
            padding: 30px 20px;
            border-right: 1px solid #334155;
            display: flex;
            flex-direction: column;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 40px;
            color: #ffffff;
            font-size: 26px;
            font-weight: 700;
            font-family: 'Outfit', sans-serif;
            letter-spacing: -0.5px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 20px;
        }

        .menu-item {
            margin-bottom: 18px;
        }

        .menu-btn {
            width: 100%;
            background: rgba(255, 255, 255, 0.03);
            color: #cbd5e1;
            border: 1px solid rgba(255, 255, 255, 0.05);
            padding: 14px 18px;
            text-align: left;
            border-radius: 12px;
            cursor: pointer;
            font-size: 15px;
            font-family: 'Outfit', sans-serif;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
            outline: none;
        }

        .menu-btn:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.1);
        }

        .submenu {
            margin-top: 8px;
            margin-left: 10px;
            display: block;
            border-left: 2px solid rgba(255, 255, 255, 0.05);
            padding-left: 12px;
        }

        .submenu a {
            display: block;
            color: #94a3b8;
            text-decoration: none;
            padding: 8px 0;
            font-size: 14px;
            transition: all 0.2s;
        }

        .submenu a:hover {
            color: #ffffff;
            transform: translateX(4px);
        }

        /* MAIN SECTION STYLE */
        .main {
            flex: 1;
            padding: 40px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e2e8f0;
        }

        .welcome {
            font-size: 30px;
            font-weight: 700;
            color: #0f172a;
            font-family: 'Outfit', sans-serif;
            letter-spacing: -0.5px;
        }

        .logout-btn {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            padding: 10px 20px;
            border-radius: 10px;
            font-family: 'Outfit', sans-serif;
            font-weight: 500;
            font-size: 14px;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
            transition: all 0.2s;
        }

        .logout-btn:hover {
            box-shadow: 0 6px 16px rgba(239, 68, 68, 0.3);
            transform: translateY(-1px);
        }

        /* CARDS GRID */
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 25px;
        }

        .card {
            background: white;
            padding: 28px;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.01), 0 2px 4px -2px rgba(0, 0, 0, 0.01);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #6366f1 0%, #4f46e5 100%);
        }

        /* Distinct color tops for status summaries */
        .card.pending-card::before {
            background: linear-gradient(90deg, #f59e0b 0%, #d97706 100%);
        }

        .card.approved-card::before {
            background: linear-gradient(90deg, #10b981 0%, #059669 100%);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(15, 23, 42, 0.08);
        }

        .card h3 {
            margin: 0;
            color: #64748b;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .card p {
            font-size: 38px;
            font-weight: 700;
            margin-top: 12px;
            color: #0f172a;
        }
    </style>
</head>

<body>

<div class="dashboard">

    <!-- SIDEBAR -->
    <div class="sidebar">
        <h2>CSB System</h2>

        <!-- BOOKINGS (Everyone can see) -->
        <div class="menu-item">
            <button class="menu-btn" onclick="toggleMenu('bookingMenu')">
                Bookings
            </button>
            <div class="submenu" id="bookingMenu">
                <a href="../views/bookings/index.php">
                    <?php if ($user['role'] === 'student'): ?>
                        Manage My Bookings
                    <?php else: ?>
                        Manage All Bookings
                    <?php endif; ?>
                </a>
                <a href="../views/bookings/create.php">
                    Create Booking
                </a>
            </div>
        </div>

        <!-- RESOURCES (Lecturer & Admin only) -->
        <?php if ($user['role'] === 'admin' || $user['role'] === 'lecturer'): ?>
        <div class="menu-item">
            <button class="menu-btn" onclick="toggleMenu('resourceMenu')">
                Resources
            </button>
            <div class="submenu" id="resourceMenu">
                <a href="../views/resources/index.php">
                    Manage Resources
                </a>
                <?php if ($user['role'] === 'admin'): ?>
                <a href="../views/resources/create.php">
                    Create Resource
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- TIME SLOTS (Admin only) -->
        <?php if ($user['role'] === 'admin'): ?>
        <div class="menu-item">
            <button class="menu-btn" onclick="toggleMenu('timeslotMenu')">
                Time Slots
            </button>
            <div class="submenu" id="timeslotMenu">
                <a href="../views/time_slots/index.php">
                    Manage Time Slots
                </a>
                <a href="../views/time_slots/create.php">
                    Create Time Slot
                </a>
            </div>
        </div>
        <?php endif; ?>

        <!-- USERS (Admin only) -->
        <?php if ($user['role'] === 'admin'): ?>
        <div class="menu-item">
            <button class="menu-btn" onclick="toggleMenu('userMenu')">
                Users
            </button>
            <div class="submenu" id="userMenu">
                <a href="../views/users/index.php">
                    Manage Users
                </a>
                <a href="../views/users/create.php">
                    Create User
                </a>
            </div>
        </div>
        <?php endif; ?>

        <!-- APPROVALS QUEUE (Lecturer & Admin only) -->
        <?php if ($user['role'] === 'admin' || $user['role'] === 'lecturer'): ?>
        <div class="menu-item">
            <a href="../views/approvals/index.php" style="text-decoration: none;">
                <button class="menu-btn">Approvals Queue</button>
            </a>
        </div>
        <?php endif; ?>

        <!-- USAGE REPORTS (Lecturer & Admin only) -->
        <?php if ($user['role'] === 'admin' || $user['role'] === 'lecturer'): ?>
        <div class="menu-item">
            <a href="../views/reports/index.php" style="text-decoration: none;">
                <button class="menu-btn">Usage Reports</button>
            </a>
        </div>
        <?php endif; ?>
    </div>

    <!-- MAIN -->
    <div class="main">
        <div class="topbar">
            <div class="welcome">
                Welcome, <?= htmlspecialchars($user['name']) ?>
            </div>
            <a class="logout-btn" href="logout.php">
                Logout
            </a>
        </div>

        <!-- CARDS -->
        <div class="cards">
            <div class="card">
                <h3>Total Bookings</h3>
                <p><?= $total_bookings ?></p>
            </div>

            <!-- Hide specific admin metrics from standard student view -->
            <?php if ($user['role'] === 'admin' || $user['role'] === 'lecturer'): ?>
            <div class="card">
                <h3>Total Resources</h3>
                <p><?= $total_resources ?></p>
            </div>

            <div class="card">
                <h3>Total Time Slots</h3>
                <p><?= $total_time_slots ?></p>
            </div>

            <div class="card">
                <h3>Total Users</h3>
                <p><?= $total_users ?></p>
            </div>
            <?php endif; ?>

            <div class="card pending-card">
                <h3>Pending Bookings</h3>
                <p><?= $pending_bookings ?></p>
            </div>

            <div class="card approved-card">
                <h3>Approved Bookings</h3>
                <p><?= $approved_bookings ?></p>
            </div>
        </div>
    </div>

</div>

<script>
function toggleMenu(id) {
    let menu = document.getElementById(id);
    if (menu.style.display === "none") {
        menu.style.display = "block";
    } else {
        menu.style.display = "none";
    }
}
</script>

</body>
</html>