<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
include("db.php");

// Function to fetch names from any table
function fetchNames($conn, $table) {
    $list = [];
    $result = $conn->query("SELECT name FROM $table ORDER BY name");
    if ($result) while ($row = $result->fetch_assoc()) $list[] = $row['name'];
    return $list;
}

// Fetch all lists
$departments = fetchNames($conn, 'departments');
$batches = fetchNames($conn, 'batches');
$courses = fetchNames($conn, 'courses');
$classrooms = fetchNames($conn, 'classrooms');
$faculty = fetchNames($conn, 'faculty');

// Fetch subjects
$subjects = [];
$subject_result = $conn->query("SELECT * FROM subjects ORDER BY name");
if ($subject_result) while ($row = $subject_result->fetch_assoc()) $subjects[] = $row;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<style>
:root {
    --primary-color: #667eea;
    --secondary-color: #764ba2;
    --text-color: #333;
    --bg-color: #f9f9f9;
    --card-bg: #fff;
    --sidebar-width: 220px;
}
* {margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI', sans-serif;}
body {background:var(--bg-color); color:var(--text-color);}
.dashboard-container {display:flex; min-height:100vh;}
.sidebar {
    width:var(--sidebar-width); background:var(--primary-color);
    color:white; padding:20px; display:flex; flex-direction:column;
}
.sidebar-header {text-align:center; margin-bottom:30px; font-size:1.5em; font-weight:bold;}
.sidebar-nav ul {list-style:none;}
.sidebar-nav li a {
    display:flex; align-items:center; color:white; text-decoration:none;
    padding:12px 15px; border-radius:8px; margin-bottom:8px; transition:0.2s;
}
.sidebar-nav li a i {margin-right:10px; width:20px; text-align:center;}
.sidebar-nav li a:hover, .sidebar-nav li a.active {background:rgba(255,255,255,0.2);}
.sidebar-footer {margin-top:auto;}
.main-content {flex:1; padding:30px;}
.header {margin-bottom:30px;}
.header h1 {font-size:28px; color:var(--primary-color); margin-bottom:5px;}
.header p {color:#666; font-size:14px;}
.card {
    background:var(--card-bg); border-radius:15px; padding:20px; margin-bottom:25px;
    box-shadow:0 4px 12px rgba(0,0,0,0.05);
}
.section-title {margin-bottom:15px; font-size:18px; color:var(--primary-color); border-left:4px solid var(--primary-color); padding-left:10px;}
ul {list-style:none; padding:0;}
li {background:#fdfdfd; padding:10px; border-radius:6px; margin-bottom:8px; border-left:2px solid var(--primary-color);}
li:hover {background:#f5f5f5;}
.btn {
    background-color:var(--primary-color); color:white; padding:8px 15px;
    border-radius:6px; text-decoration:none; font-weight:500; display:inline-block; margin-top:10px; transition:0.2s;
}
.btn:hover {background-color:var(--secondary-color);}
select, button {width:100%; padding:10px; border-radius:6px; border:1px solid #ccc; margin-bottom:15px;}
label {display:block; margin-bottom:5px; font-weight:500;}
form {margin-top:10px;}
.dashboard-container {
    display: flex;
    min-height: 100vh;
}

/* Sidebar stays fixed on the left */
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: var(--sidebar-width);
    height: 100%;
    background: var(--primary-color);
    color: white;
    padding: 20px;
    display: flex;
    flex-direction: column;
}

/* Main content fills the rest of the space */
.main-content {
    margin-left: var(--sidebar-width); /* start after sidebar */
    width: calc(100% - var(--sidebar-width));
    padding: 30px;
}

</style>
</head>
<body>
<div class="dashboard-container">

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">TIMETABLE</div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="#" class="active"><i class="fas fa-th-large"></i> Dashboard</a></li>
                <li><a href="#"><i class="fas fa-users-cog"></i> Manage Users</a></li>
                <li><a href="#"><i class="fas fa-book-open"></i> Manage Courses</a></li>
                <li><a href="#"><i class="fas fa-cogs"></i> System Settings</a></li>
                <li><a href="#"><i class="fas fa-user-shield"></i> My Profile</a></li>
            </ul>
        </nav>
        <div class="sidebar-footer">
            <nav class="sidebar-nav">
                <ul><li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li></ul>
            </nav>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <header class="header">
            <h1>Administrator Dashboard</h1>
            <p>System-wide overview and management tools.</p>
        </header>

        <!-- Phase 1 -->
        <div class="card">
            <h3 class="section-title">Phase 1: Initial Setup & Core Files</h3>
            <ul>
                <li>Database: <strong>timetable_db</strong></li>
                <li>Tables: admin, faculty, staff, students, departments, courses, classrooms, batches, timeslots</li>
                <li>Core Files: db.php, login.php, register.php, logout.php</li>
            </ul>
            <a href="add_department.php" class="btn">Add Departments</a>
        </div>

        <!-- Phase 2 -->
        <div class="card">
            <h3 class="section-title">Phase 2: Data Input & Population</h3>
            <a href="add_course.php" class="btn">Add Courses</a>
            <a href="add_classroom.php" class="btn">Add Classrooms</a>
            <a href="add_batch.php" class="btn">Add Batches</a>
            <a href="add_subject.php" class="btn">Add Subjects</a>
        </div>

        <!-- Phase 3 -->
        <div class="card">
            <h3 class="section-title">Phase 3: Timetable Generation</h3>
            <form method="post" action="generate_timetable.php">
                
                <label>Department</label>
                <select name="department" required>
                    <option value="">--Select Department--</option>
                    <?php foreach($departments as $d) echo "<option>$d</option>"; ?>
                </select>

                <label>Course</label>
                <select name="course" required>
                    <option value="">--Select Course--</option>
                    <?php foreach($courses as $c) echo "<option>$c</option>"; ?>
                </select>

                <label>Batch</label>
                <select name="batch" required>
                    <option value="">--Select Batch--</option>
                    <?php foreach($batches as $b) echo "<option>$b</option>"; ?>
                </select>

                <label>Subjects</label>
                <select name="subject[]" multiple required>
                    <?php 
                    foreach($subjects as $s){
                        echo "<option value='{$s['id']}'>{$s['name']} ({$s['course']}, {$s['batch']}) - {$s['faculty']}</option>";
                    }
                    ?>
                </select>

                <button type="submit" class="btn">Generate Timetable</button>
            </form>
        </div>

        <!-- Phase 4 -->
        <div class="card">
            <h3 class="section-title">Phase 4: Timetable Display</h3>
            <a href="view_timetable.php" class="btn">View Timetable</a>
        </div>

        <!-- Display Lists -->
        <?php
        $lists = [
            'Available Departments' => [$departments, 'add_department.php'],
            'Available Batches' => [$batches, 'add_batch.php'],
            'Available Courses' => [$courses, 'add_course.php'],
            'Available Classrooms' => [$classrooms, 'add_classroom.php'],
            'Faculty Members' => [$faculty, 'add_faculty.php'] // changed from register.php
        ];
        foreach($lists as $title => [$items, $link]) {
            echo '<div class="card"><h3 class="section-title">'.$title.'</h3>';
            if(empty($items)) echo "<p>No $title yet. <a href='$link'>Add/Register one now</a>.</p>";
            else echo '<ul><li>'.implode('</li><li>', $items).'</li></ul>';
            echo '</div>';
        }
        ?>

    </main>
</div>
</body>
</html>
