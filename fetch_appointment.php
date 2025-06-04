<?php
// fetch_appointments.php
header('Content-Type: application/json');

// Replace these with your actual DB credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "clinic_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT Appointment_ID, Appointment_Date, Appointment_Time, Patient_Name_Custom FROM appointments";
$result = $conn->query($sql);

$appointments = [];
while ($row = $result->fetch_assoc()) {
    $appointments[] = [
        'appointment_id' => $row['Appointment_ID'],
        'appointment_date' => $row['Appointment_Date'],
        'appointment_time' => $row['Appointment_Time'],
        'patient_name' => $row['Patient_Name_Custom']
    ];
}

echo json_encode($appointments);

$conn->close();
?>
