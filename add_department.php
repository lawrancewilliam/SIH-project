<?php
session_start();

// Redirect if not logged in or not admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include("db.php");

// Handle form submission
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dept_name = trim($_POST['department_name']);

    if (!empty($dept_name)) {
        $stmt = $conn->prepare("INSERT INTO departments (name) VALUES (?)");
        $stmt->bind_param("s", $dept_name);

        if ($stmt->execute()) {
            $message = "<p style='color: green;'>✅ Department added successfully!</p>";
        } else {
            $message = "<p style='color: red;'>❌ Error: " . $conn->error . "</p>";
        }
        $stmt->close();
    } else {
        $message = "<p style='color: red;'>⚠️ Department name cannot be empty.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Department</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7f6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .form-container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            width: 400px;
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }
        button {
            background: #667eea;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: 0.3s;
        }
        button:hover {
            background: #764ba2;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            text-decoration: none;
            color: #667eea;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Add Department</h2>
        <?php if (!empty($message)) echo $message; ?>
        <form method="POST">
            <label for="department_name">Department Name:</label>
            <input type="text" id="department_name" name="department_name" placeholder="Enter department name" required>
            
            <button type="submit"><i class="fas fa-plus"></i> Add Department</button>
        </form>
        <a href="admin_dashboard.php" class="back-link">⬅ Back to Dashboard</a>
    </div>
</body>
</html>
