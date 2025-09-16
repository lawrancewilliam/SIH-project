<?php
include("db.php");
if(isset($_POST['submit'])){
    $name = $_POST['name'];
    $conn->query("INSERT INTO courses (name) VALUES ('$name')");
    header("Location: admin_dashboard.php");
    exit;
}
?>
<h2>Add Course</h2>
<form method="post">
    <label>Course Name:</label>
    <input type="text" name="name" required>
    <button type="submit" name="submit">Add Course</button>
</form>
