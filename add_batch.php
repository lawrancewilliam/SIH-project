<?php
include("db.php");
if(isset($_POST['submit'])){
    $name = $_POST['name'];
    $conn->query("INSERT INTO batches (name) VALUES ('$name')");
    header("Location: admin_dashboard.php");
    exit;
}
?>
<h2>Add Batch</h2>
<form method="post">
    <label>Batch Name:</label>
    <input type="text" name="name" required>
    <button type="submit" name="submit">Add Batch</button>
</form>