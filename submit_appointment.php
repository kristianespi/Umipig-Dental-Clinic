<?php
session_start();
require 'db_connection.php';

// Load PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Check if user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in."]);
    exit;
}

// Get user input and sanitize
$name = trim($_POST['name'] ?? '');
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$phone = trim($_POST['phone'] ?? '');
$service = trim($_POST['service'] ?? '');
$dentist_id = filter_var($_POST['doctor'] ?? 0, FILTER_VALIDATE_INT);
$preferred_date = trim($_POST['preferred_date'] ?? '');
$preferred_time = trim($_POST['preferred_time'] ?? '');
$message = trim($_POST['message'] ?? '');

// Check for required fields
if (empty($name) || empty($email) || empty($phone) || empty($preferred_date) || empty($preferred_time) || empty($service) || empty($dentist_id)) {
    echo json_encode(["status" => "error", "message" => "Please fill in all required fields."]);
    exit;
}

// Use user_id from session as patient_id
$patient_id = $_SESSION['user_id'];
$admin_id = 1; 

// Format the date and time
$appointment_date = date('Y-m-d', strtotime($preferred_date));  
$appointment_time = date('H:i:s', strtotime($preferred_time));
$appointment_status = 'Pending';

// Check if the dentist ID exists in the database
$check_sql = "SELECT Dentist_ID, name, specialization FROM dentists WHERE Dentist_ID = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $dentist_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Selected dentist not found."]);
    exit;
}
$dentist_data = $check_result->fetch_assoc();
$check_stmt->close();

// Insert into the Appointment table
$sql = "INSERT INTO Appointment (Patient_ID, Dentist_ID, Admin_ID, Appointment_Date, Appointment_Time, Appointment_Status, Service_Type)
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiissss", $patient_id, $dentist_id, $admin_id, $appointment_date, $appointment_time, $appointment_status, $service);

if ($stmt->execute()) {
    // Appointment saved successfully - send confirmation email

    $mail = new PHPMailer(true);
    try {
        // SMTP server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'kristianespinase01@gmail.com';   // Your Gmail address here
        $mail->Password = 'upin izwz iker gbou';         // Your Gmail app password here
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Sender and recipient
        $mail->setFrom('UmipigDentalClinic@gmail.com', 'Umipig Dental Clinic');
        $mail->addAddress($email, $name);  // Send to patient's email

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Appointment Confirmation - Umipig Dental Clinic';
        $mail->Body = "
            <h2>Appointment Confirmation</h2>
            <p>Dear {$name},</p>
            <p>Thank you for scheduling your appointment with Umipig Dental Clinic.</p>
            <p><strong>Service:</strong> {$service}<br>
            <strong>Date:</strong> {$appointment_date}<br>
            <strong>Time:</strong> {$appointment_time}<br>
            <strong>Dentist:</strong> {$dentist_data['name']} ({$dentist_data['specialization']})</p>
            <p>We look forward to seeing you!</p>
            <p>Best regards,<br>Umipig Dental Clinic Team</p>
        ";

        $mail->send();
        echo json_encode(["status" => "success", "message" => "Appointment booked successfully. Confirmation email sent."]);
    } catch (Exception $e) {
        echo json_encode(["status" => "success", "message" => "Appointment booked, but email could not be sent. Mailer Error: {$mail->ErrorInfo}"]);
    }

} else {
    echo json_encode(["status" => "error", "message" => "Failed to book appointment: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
