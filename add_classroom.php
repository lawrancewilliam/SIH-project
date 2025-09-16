<?php
include("db.php");
if(isset($_POST['submit'])){
    $name = $_POST['name'];
    $conn->query("INSERT INTO classrooms (name) VALUES ('$name')");
    header("Location: admin_dashboard.php");
    exit;
}
?>
<h2>Add Classroom</h2>
<form method="post">
    <label>Classroom Name:</label>
    <input type="text" name="name" required>
    <button type="submit" name="submit">Add Classroom</button>
</form>