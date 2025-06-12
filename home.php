    <?php

require_once 'db_connection.php';


    session_start();
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        header("Location: admin_dashboard.php");
        exit();
    }
    // Initialize user data
    $userData = [
        'fullname' => '',
        'email' => '',
        'phone' => ''
    ];

    $showBanner = false;  // Default: don't show banner

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];

        // Connect to database
        $conn = new mysqli('localhost', 'root', '', 'clinic_db');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Fetch user info (optional, if you need to pre-fill form)
        $sql = "SELECT fullname, email, phone FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $userData = $result->fetch_assoc();
        }
        $stmt->close();

        // Check if user has filled patient info form
    $showBanner = false; // Always define this variable

    $sql2 = "SELECT COUNT(*) FROM patient_records WHERE user_id = ?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("i", $user_id);
    $stmt2->execute();
    $stmt2->bind_result($count);
    $stmt2->fetch();
    $stmt2->close();

    if ($count == 0) {
        $showBanner = true;
    }

    }
    ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Umipig Dental Clinic</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="home.css">
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
        <a href="home.php" class="active">Home</a>
        <a href="aboutUs.php">About Us</a>
        <a href="contactUs.php">Contact</a>
        <a href="services.php">Services</a>
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

<?php if (isset($_SESSION['user_id']) && $showBanner): ?>
<div id="infoBanner" class="info-banner">
    <p><strong>Reminder:</strong> Please fill up the patient information form before scheduling an appointment.</p>
    <button class="banner-button" onclick="openPatientInfoModal()">Complete Info</button>
    <span class="close-banner" onclick="document.getElementById('infoBanner').style.display='none'">&times;</span>

</div>
<?php endif; ?>




<div id="patientInfoModal" class="modal">
  <div class="modal-content">
    <span class="close-modal" onclick="closePatientInfoModal()">&times;</span>
    <?php include 'patient_information_form.php'; ?>
  </div>
</div>





    <!-- About Us Section -->
    <section id="about" class="about-section fade-section" data-direction="left">
       
        <div class="section-container">
            <h2>About Our Clinic</h2>
            <div class="about-group">
                <div class="about-content">
                    <h3>Our Mission</h3>
                    <p>
                        At Umipig Dental Clinic, our mission is to deliver compassionate, high-quality dental care that enhances the health and confidence of every patient we serve.
                        We are dedicated to creating a welcoming and comfortable environment where patients of all ages feel safe and cared for. Our team of skilled professionals is committed to staying updated
                        with the latest advancements in dental technology and treatment methods to ensure optimal care. We believe in educating our patients, empowering them to make informed decisions about their
                        oral health. Integrity, professionalism, and genuine concern for our patients are at the heart of everything we do. We strive to build long-lasting relationships based on trust, respect,
                        and outstanding results. Through our commitment to excellence, we aim to transform smiles and improve lives—one patient at a time.
                    </p>
                    <h4>Our Values</h4>
                    <ul>
                        <li>Patient-Centered Care</li>
                        <li>Professional Excellence</li>
                        <li>Continuous Education</li>
                        <li>Gentle Approach</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>


    <!-- Features Section -->
    <section id="features" class="features-section fade-section" data-direction="right">
        <h2>Why Choose Us?</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">🦷</div>
                <h3>Modern Equipment</h3>
                <p>State-of-the-art dental technology for precise treatment</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">👨‍⚕️</div>
                <h3>Expert Team</h3>
                <p>Experienced dentists and friendly staff</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🏥</div>
                <h3>Clean Environment</h3>
                <p>Sterile and comfortable clinic setting</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">💰</div>
                <h3>Affordable Care</h3>
                <p>Competitive pricing and flexible payment options</p>
            </div>
        </div>
    </section>


    <!-- Services Carousel -->
    <section class="carousel-section fade-section" data-direction="left">
        <div class="container">
            <div class="carousel-header">
                <h1>Services</h1>
                <p><a href="services.php">Click here to see all services</a></p>
            </div>


            <div class="carousel-container">
                <div class="carousel-viewport">
                    <div class="carousel-track">


                        <div class="carousel-slide">
                            <img src="images/cosmeticDentistry_Service.jpg" alt="Cosmetic Dentistry">
                        </div>
                        <div class="carousel-slide">
                            <img src="images/pediatricDentistry_Service.jpg" alt="Pediatric Dentistry">
                        </div>
                        <div class="carousel-slide">
                            <img src="images/tmjTreatment_Service.jpg" alt="TMJ Treatment">
                        </div>
                        <div class="carousel-slide">
                            <img src="images/gumDiseaseTreatment_Service.jpg" alt="Gum Disease Treatment">
                        </div>
                        <div class="carousel-slide">
                            <img src="images/dentalCrowns_Service.jpg" alt="Dental Crowns & Bridges">
                        </div>
                        <div class="carousel-slide">
                            <img src="images/professionalTeethWhitening_Service.jpg" alt="Teeth Whitening">
                        </div>
                        <div class="carousel-slide">
                            <img src="images/wisdomToothExtraction_Service.jpg" alt="Wisdom Tooth Extraction">
                        </div>
                    </div>
                </div>


                <button class="carousel-button-left">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>


                <button class="carousel-button-right">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>
        </div>
    </section>








    <!-- Before & After Section -->
    <section id="results" class="results-section fade-section" data-direction="right">
        <div class="section-container">
            <h2>Treatment Results</h2>
            <div class="results-grid">
                <div class="result-case">
                    <div class="before-after">
                        <img src="images/beforeTreatment.png" alt="Before Treatment">
                        <img src="images/afterTreatment.png" alt="After Treatment">
                    </div>
                    <p>Orthodontic Treatment</p>
                </div>
                <div class="result-case">
                    <div class="before-after">
                        <img src="images/beforeTreatment2.jpg" alt="Before Treatment">
                        <img src="images/afterTreatment2.png" alt="After Treatment">
                    </div>
                    <p>Veneers</p>
                </div>
            </div>
        </div>
    </section>



<!-- Book Appointment Section -->
<section id="book-appointment" class="appointment-section fade-section" data-direction="right">
    <div class="section-container">
        <h2>Schedule Your Visit</h2>
        <div class="appointment-grid">
            <div class="appointment-form">
                <form id="appointmentForm" method="POST" action="submit_appointment.php">
<input type="text" id="name" name="name" placeholder="Full Name" required
       value="<?php echo htmlspecialchars($userData['fullname'] ?? ''); ?>" readonly>


<input type="email" id="email" name="email" placeholder="Email Address" required
       value="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>" readonly>

<input type="tel" id="phone" name="phone" placeholder="Phone Number (+63)" required
       value="<?php echo htmlspecialchars($userData['phone'] ?? ''); ?>" readonly>


                    <!-- 🔁 Added id="service" and onchange handler -->
                    <select id="service" name="service" required onchange="suggestDentist()">
                        <option value="">Select Service</option>
                        <option value="Dental Cleaning">Dental Cleaning</option>
                        <option value="Regular Checkup">Regular Checkup</option>
                        <option value="Orthodontics">Orthodontics</option>
                        <option value="Emergency Care">Emergency Care</option>
                        <option value="Root Canal">Root Canal</option>
                        <option value="Cosmetic Dentistry">Cosmetic Dentistry</option>
                        <option value="Dental Implants">Dental Implants</option>
                        <option value="Professional Teeth Whitening">Professional Teeth Whitening</option>
                        <option value="Pediatric Dentistry">Pediatric Dentistry</option>
                        <option value="Dentures">Dentures</option>
                        <option value="Gum Disease Treatment">Gum Disease Treatment</option>
                        <option value="Wisdom Teeth Extraction">Wisdom Teeth Extraction</option>
                        <option value="Dental Crowns & Bridges">Dental Crowns & Bridges</option>
                        <option value="TMJ Treatment">TMJ Treatment</option>
                        <option value="Dental Sealants">Dental Sealants</option>
                        <option value="Fluoride Treatment">Fluoride Treatment</option>
                    </select>

                    <select id="doctor" name="doctor" required>
                        <option value="">Select Available Doctors</option>
                        <?php   
                        require 'db_connection.php';
                        $sql = "SELECT Dentist_ID, name, specialization FROM dentists";
                        $result = $conn->query($sql);

                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $id = htmlspecialchars($row['Dentist_ID']);
                                $name = htmlspecialchars($row['name']);
                                $specialization = htmlspecialchars($row['specialization']);
                                echo "<option value=\"$id\">$name - $specialization</option>";
                            }
                        } else {
                            echo "<option disabled>No doctors available</option>";
                        }

                        $conn->close();
                        ?>
                    </select>

                    <input type="date" id="preferred_date" name="preferred_date" required>
                    <input type="time" id="preferred_time" name="preferred_time" required>

                    <textarea id="message" name="message" placeholder="Additional Notes"></textarea>

                   <button type="submit" id="submitAppointmentBtn" class="cta-button">Book Appointment</button>

                </form>
            </div>
            
            <div class="appointment-info">
                <h3>Clinic Hours</h3>
                <ul>
                    <li>Monday - Friday: <strong>9:00 AM - 6:00 PM</strong></li>
                    <li>Saturday: <strong>9:00 AM - 3:00 PM</strong></li>
                    <li>Sunday: <strong>Closed</strong></li>
                </ul>
                <p>For emergencies, don't hesitate to text / call <strong>0999-2221111</strong></p>
            </div>
        </div>
    </div>
</section>



    <!-- Footer -->
    <footer class="footer">
        <div class="footer-wrapper">
            <div class="footer-left">
                <div class="clinic-branding">
                    <img src="images/UmipigDentalClinic_NoBGLogo.jpg" alt="Clinic Logo" class="footer-logo" />
                    <div>
                        <h2>Umipig Dental Clinic</h2>
                        <p>General Dentist, Orthodontist, Oral Surgeon & Cosmetic Dentist</p>
                    </div>
                </div>


                <div class="footer-row">
                    <img src="images/gps.png" alt="Location Icon" class="icon" />
                    <div>
                        <p class="contact-label">Address</p>
                        <p>2nd Floor, Village Eats Food Park, Bldg., #9<br>Village East Executive Homes 1900 Cainta, Philippines</p>
                    </div>
                </div>
            </div>


            <div class="footer-right">
                <div class="footer-row">
                    <img src="images/gmail.png" alt="Email Icon" class="icon" />
                    <div>
                        <p class="contact-label">Email</p>
                        <p>Umipigdentalclinic@gmail.com</p>
                    </div>
                </div>


                <div class="footer-row">
                    <img src="images/phone-call.png" alt="Phone Icon" class="icon" />
                    <div>
                        <p class="contact-label">Hotline</p>
                        <p>0999-1112222</p>
                    </div>
                </div>


                <div class="footer-row">
                    <img src="images/facebook.png" alt="Facebook Icon" class="icon" />
                    <div>
                        <p class="contact-label">Facebook</p>
                        <a href="https://www.facebook.com/umipigdentalcliniccainta" target="_blank">
                            https://www.facebook.com/umipigdentalcliniccainta
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>


    <script src="home.js"></script>
</body>
</html>
