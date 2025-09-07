<?php
session_start();

// Redirect to login.php if user is not logged in or not a staff member
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit;
}

$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TIMETABLE MANAGER - Staff Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --text-color: #444;
            --bg-color: #f4f7f6;
            --sidebar-width: 260px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: var(--bg-color);
            min-height: 100vh;
            color: var(--text-color);
        }

        .dashboard-container {
            display: flex;
        }

        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: linear-gradient(180deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 20px;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            text-align: center;
            margin-bottom: 40px;
            padding: 20px 0;
        }

        .sidebar-nav ul {
            list-style: none;
        }

        .sidebar-nav li a {
            display: flex;
            align-items: center;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }

        .sidebar-nav li a:hover,
        .sidebar-nav li a.active {
            background: rgba(255, 255, 255, 0.15);
            color: white;
        }

        .sidebar-nav li a i {
            margin-right: 15px;
            width: 20px;
            text-align: center;
        }

        .sidebar-footer {
            margin-top: auto;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            padding: 40px;
        }

        .header {
            margin-bottom: 40px;
        }

        .header h1 {
            font-size: 32px;
            font-weight: 600;
        }

        .header p {
            color: #777;
            font-size: 16px;
        }

        .card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            padding: 30px;
            margin-bottom: 30px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
        }

        .stat-card {
            display: flex;
            align-items: center;
            padding: 25px;
        }

        .stat-card .icon {
            font-size: 28px;
            padding: 20px;
            border-radius: 50%;
            margin-right: 20px;
        }

        .stat-card .icon.classes { background-color: #e9f2ff; color: #667eea; }
        .stat-card .icon.batches { background-color: #e6fffa; color: #38b2ac; }
        .stat-card .icon.students { background-color: #fff0f0; color: #f56565; }

        .stat-card .info h3 {
            font-size: 28px;
            font-weight: 600;
        }

        .stat-card .info p {
            color: #777;
        }

        .section-title {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .schedule-list .schedule-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .time {
            color: var(--primary-color);
            font-weight: 600;
            margin-right: 20px;
            width: 100px;
        }

        .details h4 { font-weight: 600; }
        .details p { color: #777; font-size: 14px; }
    </style>
</head>

<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>TIMETABLE</h2>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="#" class="active"><i class="fas fa-th-large"></i> Dashboard</a></li>
                    <li><a href="#"><i class="fas fa-calendar-alt"></i> My Schedule</a></li>
                    <li><a href="#"><i class="fas fa-users"></i> My Batches</a></li>
                    <li><a href="#"><i class="fas fa-user"></i> Profile</a></li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <nav class="sidebar-nav">
                    <ul>
                        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </nav>
            </div>
        </aside>

        <main class="main-content">
            <header class="header">
                <h1>Welcome Back, Staff!</h1>
                <p>Here's your overview for today.</p>
            </header>

            <section class="stats-grid">
                <div class="card stat-card">
                    <div class="icon classes"><i class="fas fa-chalkboard-teacher"></i></div>
                    <div class="info">
                        <h3>4</h3>
                        <p>Classes Today</p>
                    </div>
                </div>
                <div class="card stat-card">
                    <div class="icon batches"><i class="fas fa-users"></i></div>
                    <div class="info">
                        <h3>2</h3>
                        <p>Batches Handled</p>
                    </div>
                </div>
                <div class="card stat-card">
                    <div class="icon students"><i class="fas fa-user-graduate"></i></div>
                    <div class="info">
                        <h3>120</h3>
                        <p>Total Students</p>
                    </div>
                </div>
            </section>

            <div class="card">
                <h3 class="section-title">Today's Classes</h3>
                <div class="schedule-list">
                    <div class="schedule-item">
                        <div class="time">10:00 AM</div>
                        <div class="details">
                            <h4>Advanced Algorithms</h4>
                            <p>Batch: B.Tech 2024, Room 301</p>
                        </div>
                    </div>
                    <div class="schedule-item">
                        <div class="time">12:00 PM</div>
                        <div class="details">
                            <h4>Database Systems Lab</h4>
                            <p>Batch: B.Tech 2025, Lab 2</p>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>
</body>

</html>