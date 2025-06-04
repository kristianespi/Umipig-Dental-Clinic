<?php
require 'db_connection.php';

if (!isset($_GET['date'], $_GET['dentist_id'])) {
    echo json_encode([]);
    exit;
}

$date = $_GET['date'];
$dentist_id = intval($_GET['dentist_id']);

$sql = "SELECT available_time FROM dentistavailability WHERE Dentist_ID = ? AND available_date = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $dentist_id, $date);
$stmt->execute();
$result = $stmt->get_result();

$times = [];
while ($row = $result->fetch_assoc()) {
    $times[] = substr($row['available_time'], 0, 5); // e.g., "09:00"
}

echo json_encode($times);

$stmt->close();
$conn->close();
