<?php
session_start();
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect POST data safely
    $time = trim($_POST['appt_time'] ?? '');
    $date = trim($_POST['appt_date'] ?? '');
    $patient_name = trim($_POST['patient_name'] ?? '');  // ✅ corrected variable name
    $dentist_id = trim($_POST['dentist_name'] ?? '');
    $procedure = trim($_POST['procedure'] ?? '');
    $status = trim($_POST['status'] ?? '');

    // ✅ Check for missing fields
    if (!$time || !$date || !$patient_name || !$dentist_id || !$procedure || !$status) {
        echo json_encode(['success' => false, 'error' => 'Missing required fields']);
        exit;
    }

    // ✅ Get dentist name from dentists table
    $dentist_stmt = $conn->prepare("SELECT name FROM dentists WHERE Dentist_ID = ?");
    if (!$dentist_stmt) {
        echo json_encode(['success' => false, 'error' => 'Failed to prepare dentist query: ' . $conn->error]);
        exit;
    }
    $dentist_stmt->bind_param("i", $dentist_id);
    $dentist_stmt->execute();
    $dentist_stmt->bind_result($dentist_name);

    if (!$dentist_stmt->fetch()) {
        $dentist_stmt->close();
        echo json_encode(['success' => false, 'error' => 'Dentist not found']);
        exit;
    }
    $dentist_stmt->close();

    // ✅ Insert appointment
    $stmt = $conn->prepare("INSERT INTO appointment 
        (Appointment_Time, Appointment_Date, Patient_Name_Custom, Dentist_Name_Custom, Service_Type, Appointment_Status) 
        VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => 'Failed to prepare insert query: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param("ssssss", $time, $date, $patient_name, $dentist_name, $procedure, $status);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Insert failed: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>
