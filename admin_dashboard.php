<?php
session_start();

// Redirect to login.php if user is not logged in or not an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$role = $_SESSION['role'];

// --- Department with Sub-Departments (Accordion Style) ---
$department_list = [
    'Arts' => [
        'Fine Arts',
        'Performing Arts (Theater, Dance, Music)',
        'Visual Arts (Painting, Sculpture, Photography)',
        'Digital Arts',
        'Art History',
        'Design (Graphic, Fashion, Interior)',
        'Creative Writing',
        'Film Studies',
        'Architecture'
    ],
    'Engineering' => [
        'Civil Engineering',
        'Mechanical Engineering',
        'Electrical Engineering',
        'Chemical Engineering',
        'Computer Engineering',
        'Aerospace Engineering',
        'Biomedical Engineering',
        'Environmental Engineering',
        'Industrial Engineering',
        'Materials Engineering',
        'Petroleum Engineering',
        'Software Engineering'
    ],
    'Medical/Health Sciences' => [
        'Medicine',
        'Nursing',
        'Pharmacy',
        'Dentistry',
        'Physical Therapy',
        'Occupational Therapy',
        'Public Health',
        'Medical Laboratory Science',
        'Radiology',
        'Nutrition and Dietetics',
        'Veterinary Medicine',
        'Mental Health Counseling'
    ],
    'Sciences' => [
        'Biology',
        'Chemistry',
        'Physics',
        'Mathematics',
        'Computer Science',
        'Environmental Science',
        'Astronomy',
        'Geology',
        'Marine Science',
        'Biochemistry',
        'Statistics',
        'Data Science'
    ],
    'Social Sciences' => [
        'Psychology',
        'Sociology',
        'Anthropology',
        'Political Science',
        'Economics',
        'Geography',
        'International Relations',
        'Communication Studies',
        'Archaeology'
    ],
    'Business' => [
        'Business Administration',
        'Accounting',
        'Finance',
        'Marketing',
        'Management',
        'Entrepreneurship',
        'International Business',
        'Supply Chain Management',
        'Human Resources'
    ],
    'Humanities' => [
        'Philosophy',
        'History',
        'Literature',
        'Languages and Linguistics',
        'Religious Studies',
        'Cultural Studies',
        'Ethics',
        'Classics'
    ],
    'Law' => [
        'Constitutional Law',
        'Criminal Law',
        'International Law',
        'Corporate Law',
        'Environmental Law',
        'Human Rights Law',
        'Intellectual Property Law'
    ]
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TIMETABLE MANAGER - Admin Dashboard</title>
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

        /* Accordion Styles */
        .accordion-item {
            margin-bottom: 10px;
            border-radius: 10px;
            overflow: hidden;
        }
        .accordion-title {
            cursor: pointer;
            padding: 15px;
            font-weight: 600;
            color: var(--text-color);
            background: white;
            border: 1px solid #000;
            border-radius: 10px;
            transition: color 0.3s, border-color 0.3s, background-color 0.3s;
        }
        .accordion-title:hover {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        .accordion-item.active .accordion-title {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
            border-radius: 10px 10px 0 0;
        }
        .accordion-title i {
            float: right;
            transition: transform 0.3s;
        }
        .accordion-content {
            display: none;
            padding: 15px;
            background: white;
            border: 1px solid #000;
            border-top: none;
            border-radius: 0 0 10px 10px;
        }
        .accordion-content ul {
            list-style: none;
        }
        .accordion-content ul li {
            padding: 10px 15px;
            border-radius: 8px;
            transition: color 0.3s, background-color 0.3s;
        }
        .accordion-content ul li:hover {
            background-color: var(--primary-color);
            color: white;
            cursor: pointer;
        }
        .accordion-item.active .accordion-title i {
            transform: rotate(90deg);
        }
        .accordion-item.active .accordion-content {
            display: block;
        }
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
                    <li><a href="#"><i class="fas fa-users-cog"></i> Manage Users</a></li>
                    <li><a href="#"><i class="fas fa-book-open"></i> Manage Courses</a></li>
                    <li><a href="#"><i class="fas fa-cogs"></i> System Settings</a></li>
                    <li><a href="#"><i class="fas fa-user-shield"></i> My Profile</a></li>
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
                <h1>Administrator Dashboard</h1>
                <p>System-wide overview and management tools.</p>
            </header>

            <div class="card">
                <h3 class="section-title">Available Departments</h3><br>
                
                <?php foreach ($department_list as $mainDept => $subDepts): ?>
                    <div class="accordion-item">
                        <div class="accordion-title">
                            <?= htmlspecialchars($mainDept) ?>
                            <i class="fas fa-chevron-right"></i>
                        </div>
                        <div class="accordion-content">
                            <ul>
                                <?php foreach ($subDepts as $sub): ?>
                                    <li><?= htmlspecialchars($sub) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>
    </div>

    <script>
        // Accordion toggle logic
        const accordionItems = document.querySelectorAll(".accordion-item");
        accordionItems.forEach(item => {
            const title = item.querySelector(".accordion-title");
            title.addEventListener("click", () => {
                item.classList.toggle("active");
            });
        });
    </script>
</body>
</html>
