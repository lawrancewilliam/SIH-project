<?php
session_start();
$error = "";

ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = new mysqli('localhost', 'root', '', 'timetable_db');
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

    $role = $_POST['role'] ?? '';
    $fullName = trim($_POST['fullName']);
    $email = trim($_POST['registerEmail']);
    $password = $_POST['registerPassword'];
    $confirmPass = $_POST['confirmPassword'];

    if ($password !== $confirmPass) {
        $error = "Passwords do not match!";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Check for existing email
        $table = ($role === 'student') ? 'students' : (($role === 'staff') ? 'staff' : 'admin');
        $emailCheckQuery = $conn->prepare("SELECT id FROM $table WHERE email = ?");
        $emailCheckQuery->bind_param("s", $email);
        $emailCheckQuery->execute();
        $emailCheckQuery->store_result();

        if ($emailCheckQuery->num_rows > 0) {
            $error = "Email already registered.";
        } else {
            if ($role === 'student') {
                $stmt = $conn->prepare("INSERT INTO students (fullName, email, password, rollNo, batch, department, mobile, dob) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssssss", $fullName, $email, $hashedPassword, $_POST['studentRollNo'], $_POST['batch'], $_POST['department'], $_POST['mobileNumber'], $_POST['dateOfBirth']);
            } elseif ($role === 'staff') {
                $stmt = $conn->prepare("INSERT INTO staff (fullName, email, password, staffCode, subjectHandling, numBatches, mobile) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssis", $fullName, $email, $hashedPassword, $_POST['staffCode'], $_POST['subjectHandling'], intval($_POST['numberOfBatches']), $_POST['mobileNumberStaff']);
            } elseif ($role === 'admin') {
                $stmt = $conn->prepare("INSERT INTO admin (fullName, email, password, adminCode, mobile) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $fullName, $email, $hashedPassword, $_POST['adminCode'], $_POST['mobileNumberAdmin']);
            }

            if ($stmt->execute()) {
                header("Location: login.php");
                exit;
            } else {
                $error = "Database error: " . $stmt->error;
            }

            $stmt->close();
        }

        $emailCheckQuery->close();
    }

    $conn->close();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TIMETABLE MANAGER - REGISTER</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <style>
    * {margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;}
    body {background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; justify-content: center; align-items: center; padding: 20px;}
    :root {--primary-color: #667eea; --secondary-color: #764ba2; --text-color: #444;}
    .container {width: 100%; max-width: 500px;}
    .card {background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); overflow: hidden; animation: slideIn 0.8s ease-out;}
    @keyframes slideIn {from {opacity:0; transform: translateY(-30px);} to {opacity:1; transform: translateY(0);}}
    .card-header {background: linear-gradient(45deg, #667eea, #764ba2); color: white; padding: 30px; text-align: center; position: relative;}
    .card-header::before {content: ''; position: absolute; top: 0; left: 0; right: 0; height: 5px; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);}
    .card-body {padding: 40px;}
    .form-group {margin-bottom: 30px; position: relative;}
    .form-label {display: block; margin-bottom: 10px; color: #444; font-weight: 500; font-size: 16px;}
    .form-input {width: 100%; padding: 18px 22px; border: 2px solid #e1e5ea; border-radius: 14px; font-size: 17px; transition: all 0.3s ease; background: #f8fafc;}
    .form-input:focus {outline: none; border-color: #667eea; box-shadow: 0 0 0 5px rgba(102,126,234,0.1);}
    .form-icon {position: absolute; right: 22px; top: 50%; transform: translateY(-50%); color: #a0aec0; pointer-events: none; transition: color 0.3s ease;}
    .form-input:focus ~ .form-icon {color: #667eea;}
    .form-btn {width: 100%; padding: 18px; border: none; border-radius: 14px; font-size: 17px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; background: linear-gradient(45deg, #667eea, #764ba2); color: white; box-shadow: 0 5px 18px rgba(102,126,234,0.4); display: none;}
    .form-btn:hover {transform: translateY(-3px); background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); box-shadow: 0 8px 25px rgba(102,126,234,0.5);}
    .form-links {text-align: center; margin-top: 30px;}
    .form-links a {color: #667eea; text-decoration: none;}
    .form-links a:hover {color: #764ba2; text-decoration: underline;}
    .form-radio-group {display: flex; gap: 20px; margin-top: 10px;}
    .form-radio-item {flex: 1; position: relative;}
    .form-radio {display: none;}
    .form-radio + label {display: block; padding: 14px 18px; border: 2px solid #e1e5ea; border-radius: 12px; font-size: 16px; cursor: pointer; transition: all 0.3s ease; text-align: center;}
    .form-radio:checked + label {border-color: #667eea; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-weight: 600; box-shadow: 0 4px 12px rgba(102,126,234,0.3);}
    .form-radio + label:hover {border-color: #667eea; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;}
    .hidden {display: none;}
  </style>
</head>
<body>
  <div class="container">
    <div class="card">
      <div class="card-header">
        <h2>Register</h2>
        <p>Join us today and streamline your scheduling</p>
      </div>
      <div class="card-body">
        <?php if($error) echo "<p style='color:red;text-align:center;margin-bottom:15px;'>$error</p>"; ?>
        <form id="registerForm" method="POST" action="register.php">
          <!-- Role selection -->
          <div class="form-group">
            <label class="form-label">Role</label>
            <div class="form-radio-group">
              <div class="form-radio-item">
                <input type="radio" id="admin" name="role" value="admin" class="form-radio">
                <label for="admin">Administrator</label>
              </div>
              <div class="form-radio-item">
                <input type="radio" id="staff" name="role" value="staff" class="form-radio">
                <label for="staff">Staff</label>
              </div>
              <div class="form-radio-item">
                <input type="radio" id="student" name="role" value="student" class="form-radio">
                <label for="student">Student</label>
              </div>
            </div>
          </div>

          <!-- Other fields -->
          <div id="otherFields" class="hidden">
            <!-- Common Fields -->
            <div class="form-group">
              <label class="form-label" for="fullName">Full Name</label>
              <div style="position: relative;">
                <input type="text" name="fullName" id="fullName" class="form-input" placeholder="John Doe" required>
                <i class="fas fa-user form-icon"></i>
              </div>
            </div>

            <div class="form-group">
              <label class="form-label" for="registerEmail">Email Address</label>
              <div style="position: relative;">
                <input type="email" name="registerEmail" id="registerEmail" class="form-input" placeholder="your@email.com" required>
                <i class="fas fa-envelope form-icon"></i>
              </div>
            </div>

            <!-- Student-Specific Fields -->
            <div id="studentFields" class="hidden">
              <div class="form-group">
                <label class="form-label" for="studentRollNo">Student Roll No</label>
                <input type="text" name="studentRollNo" id="studentRollNo" class="form-input" placeholder="e.g., 12345">
              </div>
              <div class="form-group">
                <label class="form-label" for="batch">Batch</label>
                <input type="text" name="batch" id="batch" class="form-input" placeholder="e.g., B.Tech 2025">
              </div>
              <div class="form-group">
                <label class="form-label" for="department">Department</label>
                <input type="text" name="department" id="department" class="form-input" placeholder="e.g., Computer Science">
              </div>
              <div class="form-group">
                <label class="form-label" for="mobileNumber">Mobile Number</label>
                <input type="text" name="mobileNumber" id="mobileNumber" class="form-input" placeholder="10-digit number">
              </div>
              <div class="form-group">
                <label class="form-label" for="dateOfBirth">Date of Birth</label>
                <input type="date" name="dateOfBirth" id="dateOfBirth" class="form-input">
              </div>
            </div>

            <!-- Staff-Specific Fields -->
            <div id="staffFields" class="hidden">
              <div class="form-group">
                <label class="form-label" for="staffCode">Staff Code</label>
                <input type="text" name="staffCode" id="staffCode" class="form-input" placeholder="e.g., SF123">
              </div>
              <div class="form-group">
                <label class="form-label" for="subjectHandling">Subject Handling</label>
                <input type="text" name="subjectHandling" id="subjectHandling" class="form-input" placeholder="e.g., Mathematics">
              </div>
              <div class="form-group">
                <label class="form-label" for="numberOfBatches">Number of Batches</label>
                <input type="number" name="numberOfBatches" id="numberOfBatches" class="form-input" placeholder="e.g., 3">
              </div>
              <div class="form-group">
                <label class="form-label" for="mobileNumberStaff">Mobile Number</label>
                <input type="text" name="mobileNumberStaff" id="mobileNumberStaff" class="form-input" placeholder="10-digit number">
              </div>
            </div>

            <!-- Admin-Specific Fields -->
            <div id="adminFields" class="hidden">
              <div class="form-group">
                <label class="form-label" for="adminCode">Admin Code</label>
                <input type="text" name="adminCode" id="adminCode" class="form-input" placeholder="Enter Admin Code">
              </div>
              <div class="form-group">
                <label class="form-label" for="mobileNumberAdmin">Mobile Number</label>
                <input type="text" name="mobileNumberAdmin" id="mobileNumberAdmin" class="form-input" placeholder="10-digit number">
              </div>
            </div>

            <!-- Password -->
            <div class="form-group">
              <label class="form-label" for="registerPassword">Password</label>
              <div style="position: relative;">
                <input type="password" name="registerPassword" id="registerPassword" class="form-input" placeholder="••••••••" autocomplete="off" required>
                <i class="fas fa-lock form-icon"></i>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label" for="confirmPassword">Confirm Password</label>
              <div style="position: relative;">
                <input type="password" name="confirmPassword" id="confirmPassword" class="form-input" placeholder="••••••••" autocomplete="off" required>
                <i class="fas fa-lock form-icon"></i>
              </div>
            </div>
          </div>

          <!-- Create Account Button -->
          <button type="submit" class="form-btn" id="createBtn">Create Account</button>

          <div class="form-links">
            <span>Already have an account? </span>
            <a href="login.php" class="login-link">Sign In</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const roleRadios = document.querySelectorAll('input[name="role"]');
      const otherFields = document.getElementById('otherFields');
      const createBtn = document.getElementById('createBtn');
      const studentFields = document.getElementById('studentFields');
      const staffFields = document.getElementById('staffFields');
      const adminFields = document.getElementById('adminFields');

      const handleRoleChange = () => {
        otherFields.classList.remove('hidden');
        createBtn.style.display = 'block';
        studentFields.classList.add('hidden');
        staffFields.classList.add('hidden');
        adminFields.classList.add('hidden');

        const selectedRole = document.querySelector('input[name="role"]:checked');
        if (selectedRole) {
          if (selectedRole.value === 'student') {
            studentFields.classList.remove('hidden');
          } else if (selectedRole.value === 'staff') {
            staffFields.classList.remove('hidden');
          } else if (selectedRole.value === 'admin') {
            adminFields.classList.remove('hidden');
          }
        }
      };

      roleRadios.forEach(radio => {
        radio.addEventListener('change', handleRoleChange);
      });
    });
  </script>
</body>
</html>
