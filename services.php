<?php
session_start();
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: admin_dashboard.php");
    exit();
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Umipig Dental Clinic - Services</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="services.css">
</head>
<body>

    <header>

        <div class="logo-container">
            <div class="logo-circle">
                <img src="images/UmipigDentalClinic_Logo.jpg" alt="Umipig Dental Clinic">
            </div>

            <div class="clinic-info">
                <h1>Umipig Dental Clinic</h1>
                <p>General Dentist, Orthodontist, Oral Surgeon & Cosmetic Dentist</p>
            </div>

        </div>


        <nav class="main-nav">
            <a href="home.php">Home</a>
            <a href="aboutUs.php">About Us</a>
            <a href="contactUs.php">Contact</a>
            <a href="services.php" class="active">Services</a>
        </nav>

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


    <h1>- Our Services -</h1>
    <div class="services-container">
    </div>

    <script src="services.js"></script>
</body>
</html>
