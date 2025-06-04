<?php
session_start(); // Start the session to access session variables

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Not an admin, redirect to home or login page
    header("Location: login.php");
    exit;
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Umpig Dental Clinic - Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="admin_dashboard.css">
</head>

<body>

    <header>
        <div class="logo-container">
            <div class="logo-circle">
                <img src="images/UmipigDentalClinic_Logo.jpg" alt="Umipig Dental Clinic" />
            </div>
            <div class="clinic-info">
                <h1>Umipig Dental Clinic</h1>
                <p>General Dentist, Orthodontist, Oral Surgeon & Cosmetic Dentist</p>
            </div>
        </div>



        <div class="header-right">
            <?php if (isset($_SESSION['username'])): ?>
                <span class="welcome-text">
                    Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>! &nbsp; | &nbsp;
                    <a href="logout.php" class="auth-link">Logout</a>
                </span>
            <?php else: ?>
                <a href="register.php" class="auth-link">Register</a>
                <span>|</span>
                <a href="login.php" class="auth-link">Login</a>
            <?php endif; ?>
        </div>
    </header>

    <div class="dashboard">
        <!-- Sidebar -->
        <div class="sidebar">
            <!-- ☰ menu icon shown only when sidebar is collapsed -->
            <div class="menu-icon">☰</div>
            <ul class="nav-menu">
               <li class="nav-item">
                     <a href="admin_dashboard.php" class="nav-link">Dashboard</a>
               </li>
                <li class="nav-item">
                     <a href="appointment_module.php" class="nav-link">Appointment Management</a>
               </li>
               <li class="nav-item">
                     <a href="patient_records.php" class="nav-link">Patient Records</a>
               </li>
                <li class="nav-item"><span>Reports</span></li>
                <li class="nav-item"><span>Documents / Files</span></li>
                <li class="nav-item"><span>Billing Records</span></li>
                <li class="nav-item"><span>Calendar</span></li>
                <li class="nav-item"><span>Tasks & Reminders</span></li>
                <li class="nav-item"><span>System Settings</span></li>
            </ul>
        </div>

        <!-- Main Content Area -->
        <div id="main-content" class="content-area">
            <p>Please select a module from the sidebar.</p>
        </div>
    </div>

    <script src="admin_dashboard.js"></script>
</body>
</html>
