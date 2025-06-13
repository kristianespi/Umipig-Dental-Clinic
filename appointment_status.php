<?php
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_id = $_POST['appointment_id'] ?? '';
    $new_status = $_POST['new_status'] ?? '';

    if ($appointment_id && $new_status) {
        $stmt = $conn->prepare("UPDATE Appointment SET Appointment_Status = ? WHERE Appointment_ID = ?");
        $stmt->bind_param("si", $new_status, $appointment_id);

        if ($stmt->execute()) {
            echo "Status updated successfully.";
        } else {
            echo "Failed to update status: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Missing data.";
    }

    $conn->close();
}
?>
// test comment (github)

<form action="appointment_status.php" method="POST">
  <input type="hidden" name="appointment_id" value="123">
  <select name="new_status">
    <option value="Pending">Pending</option>
    <option value="Approved">Approved</option>
    <option value="Declined">Declined</option>
    <option value="Completed">Completed</option>
  </select>
  <button type="submit">Update</button>
</form>
