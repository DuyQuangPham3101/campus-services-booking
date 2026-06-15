<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html>

<head>

    <title>Dashboard</title>

    <link rel="stylesheet" href="../assets/style.css">

    <style>

        body{
            margin:0;
            font-family:Arial, sans-serif;
            background:#f1f5f9;
        }

        .dashboard{
            display:flex;
            min-height:100vh;
        }

        /* SIDEBAR */

        .sidebar{
            width:260px;
            background:#0f172a;
            color:white;
            padding:25px 20px;
        }

        .sidebar h2{
            text-align:center;
            margin-bottom:35px;
            color:white;
            font-size:28px;
            font-weight:bold;
            letter-spacing:1px;
        }

        .menu-item{
            margin-bottom:15px;
        }

        .menu-btn{
            width:100%;
            background:#1e293b;
            color:white;
            border:none;
            padding:14px;
            text-align:left;
            border-radius:8px;
            cursor:pointer;
            font-size:16px;
            transition:0.3s;
        }

        .menu-btn:hover{
            background:#334155;
        }

        .submenu{
            margin-top:10px;
            margin-left:15px;
            display:block;
        }

        .submenu a{
            display:block;
            color:#cbd5e1;
            text-decoration:none;
            padding:8px 0;
            transition:0.3s;
        }

        .submenu a:hover{
            color:white;
            transform:translateX(5px);
        }

        /* MAIN */

        .main{
            flex:1;
            padding:35px;
        }

        .topbar{
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:35px;
        }

        .welcome{
            font-size:32px;
            font-weight:bold;
            color:#0f172a;
        }

        .logout-btn{
            background:#ef4444;
            color:white;
            padding:12px 18px;
            border-radius:8px;
            text-decoration:none;
            transition:0.3s;
        }

        .logout-btn:hover{
            background:#dc2626;
        }

        /* CARDS */

        .cards{
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(230px,1fr));
            gap:25px;
        }

        .card{
            background:white;
            padding:30px;
            border-radius:15px;
            box-shadow:0 4px 15px rgba(0,0,0,0.08);
            transition:0.3s;
        }

        .card:hover{
            transform:translateY(-5px);
        }

        .card h3{
            margin:0;
            color:#64748b;
            font-size:18px;
        }

        .card p{
            font-size:40px;
            font-weight:bold;
            margin-top:15px;
            color:#0f172a;
        }

    </style>

</head>

<body>

<div class="dashboard">

    <!-- SIDEBAR -->

    <div class="sidebar">

        <h2>CSB System</h2>

        <!-- BOOKINGS -->

        <div class="menu-item">

            <button class="menu-btn"
                    onclick="toggleMenu('bookingMenu')">

                Bookings

            </button>

            <div class="submenu" id="bookingMenu">

                <a href="../views/bookings/index.php">
                    Manage All Bookings
                </a>

                <a href="../views/bookings/create.php">
                    Create Booking
                </a>

            </div>

        </div>

        <!-- RESOURCES -->

        <div class="menu-item">

            <button class="menu-btn"
                    onclick="toggleMenu('resourceMenu')">

                Resources

            </button>

            <div class="submenu" id="resourceMenu">

                <a href="../views/resources/index.php">
                    Manage Resources
                </a>

                <a href="../views/resources/create.php">
                    Create Resource
                </a>

            </div>

        </div>

        <!-- TIME SLOTS -->

        <div class="menu-item">

            <button class="menu-btn"
                    onclick="toggleMenu('timeslotMenu')">

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

        <!-- USERS -->

        <div class="menu-item">

            <button class="menu-btn"
                    onclick="toggleMenu('userMenu')">

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

    </div>

    <!-- MAIN -->

    <div class="main">

        <div class="topbar">

            <div class="welcome">

                Welcome,
                <?= $user['name'] ?>

            </div>

            <a class="logout-btn" href="logout.php">
                Logout
            </a>

        </div>

        <!-- CARDS -->

        <div class="cards">

            <div class="card">

                <h3>Total Bookings</h3>

                <p>8</p>

            </div>

            <div class="card">

                <h3>Total Resources</h3>

                <p>5</p>

            </div>

            <div class="card">

                <h3>Total Time Slots</h3>

                <p>4</p>

            </div>

            <div class="card">

                <h3>Total Users</h3>

                <p>3</p>

            </div>

            <div class="card">

                <h3>Pending Bookings</h3>

                <p>2</p>

            </div>

            <div class="card">

                <h3>Approved Bookings</h3>

                <p>6</p>

            </div>

        </div>

    </div>

</div>

<script>

function toggleMenu(id){

    let menu = document.getElementById(id);

    if(menu.style.display === "none"){

        menu.style.display = "block";

    }else{

        menu.style.display = "none";

    }

}

</script>

</body>
</html>