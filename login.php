<?php
session_start();
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = new mysqli('localhost', 'root', '', 'timetable_db');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $role = $_POST['role'];
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Select correct table
    if ($role === 'student') {
        $table = 'students';
    } elseif ($role === 'staff') {
        $table = 'staff';
    } elseif ($role === 'admin') {
        $table = 'admin';
    } else {
        $error = "Invalid role selected.";
    }

    if (!$error) {
        $stmt = $conn->prepare("SELECT id, password FROM $table WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $hashedPassword);
            $stmt->fetch();

            if (password_verify($password, $hashedPassword)) {
                $_SESSION['user_id'] = $id;
                $_SESSION['role'] = $role;
                header("Location: dashboard.php");
                exit;
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "Email not found.";
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>TIMETABLE MANAGER - LOGIN</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 500px;
        }

        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            animation: slideIn 0.8s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-header {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }

        .card-body {
            padding: 40px;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-label {
            display: block;
            margin-bottom: 10px;
            color: #444;
            font-weight: 500;
            font-size: 16px;
        }

        .form-input {
            width: 100%;
            padding: 18px 22px;
            border: 2px solid #e1e5ea;
            border-radius: 14px;
            font-size: 17px;
            transition: all 0.3s ease;
            background: #f8fafc;
        }

        .form-icon {
            position: absolute;
            right: 22px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            pointer-events: none;
        }

        .form-btn {
            width: 100%;
            padding: 18px;
            border: none;
            border-radius: 14px;
            font-size: 17px;
            font-weight: 600;
            cursor: pointer;
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            box-shadow: 0 5px 18px rgba(102, 126, 234, 0.4);
        }

        .form-links {
            text-align: center;
            margin-top: 20px;
        }

        .form-links a {
            color: #667eea;
            text-decoration: none;
        }

        .form-links a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .error-msg {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>Login</h2>
                <p>Welcome back! Please sign in to continue.</p>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div style="color: red; margin-bottom: 20px;"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="POST" action="login.php">
                    <div class="form-group">
                        <label class="form-label" for="role">Select Role</label>
                        <select name="role" id="role" class="form-input" required>
                            <option value="">--Select Role--</option>
                            <option value="student">Student</option>
                            <option value="staff">Staff</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="loginEmail">Email Address</label>
                        <div style="position: relative;">
                            <input type="email" name="email" id="loginEmail" class="form-input" placeholder="your@email.com" required/>
                            <i class="fas fa-envelope form-icon"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="loginPassword">Password</label>
                        <div style="position: relative;">
                            <input type="password" name="password" id="loginPassword" class="form-input" placeholder="••••••••" required/>
                            <i class="fas fa-lock form-icon"></i>
                        </div>
                    </div>

                    <button type="submit" class="form-btn">Sign In</button>

                    <div class="form-links">
                        <span>Don't have an account? </span>
                        <a href="register.php">Sign Up</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>