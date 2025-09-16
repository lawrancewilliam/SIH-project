<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
include("db.php");

// Fetch Courses
$course_list = [];
$course_result = $conn->query("SELECT * FROM courses ORDER BY name");
if ($course_result) while ($row = $course_result->fetch_assoc()) $course_list[] = $row['name'];

// Fetch Batches
$batch_list = [];
$batch_result = $conn->query("SELECT * FROM batches ORDER BY name");
if ($batch_result) while ($row = $batch_result->fetch_assoc()) $batch_list[] = $row['name'];

// Fetch Faculty
$faculty_list = [];
$faculty_result = $conn->query("SELECT * FROM faculty ORDER BY name");
if ($faculty_result) while ($row = $faculty_result->fetch_assoc()) $faculty_list[] = $row['name'];

// Handle Form Submission
if (isset($_POST['submit'])) {
    $subject_name = $conn->real_escape_string($_POST['subject_name']);
    $course = $conn->real_escape_string($_POST['course']);
    $batch = $conn->real_escape_string($_POST['batch']);
    $faculty = $conn->real_escape_string($_POST['faculty']);

    $sql = "INSERT INTO subjects (name, course, batch, faculty) VALUES ('$subject_name', '$course', '$batch', '$faculty')";
    if ($conn->query($sql)) {
        $success = "Subject added successfully!";
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Subject</title>
<style>
body {font-family:'Segoe UI', sans-serif; background:#f9f9f9; color:#333; padding:20px;}
.card {background:#fff; padding:20px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.05); max-width:500px; margin:auto;}
h2 {color:#667eea; margin-bottom:20px;}
label {display:block; margin-top:15px; margin-bottom:5px; font-weight:500;}
input, select, button {width:100%; padding:10px; border-radius:6px; border:1px solid #ccc; margin-bottom:10px;}
button {background:#667eea; color:white; border:none; cursor:pointer; transition:0.3s;}
button:hover {background:#764ba2;}
.success {color:green; margin-bottom:10px;}
.error {color:red; margin-bottom:10px;}
</style>
</head>
<body>

<div class="card">
    <h2>Add Subject</h2>

    <?php if(isset($success)) echo "<p class='success'>$success</p>"; ?>
    <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>

    <form method="post">
        <label>Subject Name</label>
        <input type="text" name="subject_name" required>

        <label>Course</label>
        <select name="course" required>
            <option value="">--Select Course--</option>
            <?php foreach($course_list as $c) echo "<option>$c</option>"; ?>
        </select>

        <label>Batch</label>
        <select name="batch" required>
            <option value="">--Select Batch--</option>
            <?php foreach($batch_list as $b) echo "<option>$b</option>"; ?>
        </select>

        <label>Faculty</label>
        <select name="faculty" required>
            <option value="">--Select Faculty--</option>
            <?php foreach($faculty_list as $f) echo "<option>$f</option>"; ?>
        </select>

        <button type="submit" name="submit">Add Subject</button>
    </form>
</div>

</body>
</html>
