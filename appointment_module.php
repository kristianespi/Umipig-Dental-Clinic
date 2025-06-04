<?php
session_start();
require 'db_connection.php';

// Load PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Not an admin, redirect to home or login page
    header("Location: home.php");
    exit;
}


// Fetch dentists for dropdown
$dentists_result = $conn->query("SELECT Dentist_ID, name FROM dentists ORDER BY name ASC");

// Handle form submissions (add or update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['form_type'])) {
        if ($_POST['form_type'] === 'update') {
            // Update appointment
            $id = $_POST['appointment_id'];
            $time = $_POST['appointment_time'];
            $date = $_POST['appointment_date'];
            $patient_name_custom = $_POST['patient_name_custom'];
            $dentist_id = $_POST['dentist_id'];
            $procedure = $_POST['procedure'];
            $status = $_POST['status'];

            $stmt = $conn->prepare("UPDATE appointment 
                                    SET Appointment_Time = ?, Appointment_Date = ?, Patient_Name_Custom = ?, Dentist_ID = ?, Service_Type = ?, Appointment_Status = ? 
                                    WHERE Appointment_ID = ?");
            $stmt->bind_param("ssssssi", $time, $date, $patient_name_custom, $dentist_id, $procedure, $status, $id);

            if ($stmt->execute()) {
                $_SESSION['message'] = "Appointment updated successfully!";
            } else {
                $_SESSION['message'] = "Error updating appointment: " . $stmt->error;
            }
            $stmt->close();
            header("Location: appointment_module.php");
            exit;

        } elseif ($_POST['form_type'] === 'add') {
            // Add new appointment
            $time = $_POST['appt_time'];
            $date = $_POST['appt_date'];
            $patient_name_custom = $_POST['patient_name_custom'];
            $dentist_id = $_POST['dentist_name'];
            $procedure = $_POST['procedure'];
            $status = $_POST['status'];

            $stmt = $conn->prepare("INSERT INTO appointment (Appointment_Time, Appointment_Date, Patient_Name_Custom, Dentist_ID, Service_Type, Appointment_Status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $time, $date, $patient_name_custom, $dentist_id, $procedure, $status);

            if ($stmt->execute()) {
                $_SESSION['message'] = "Appointment added successfully!";
            } else {
                $_SESSION['message'] = "Error adding appointment: " . $stmt->error;
            }
            $stmt->close();
            header("Location: appointment_module.php");
            exit;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['form_type'] === 'reschedule') {
    $id = $_POST['appointment_id'];
    $newDate = $_POST['new_date'];
    $newTime = $_POST['new_time'];

    // Step 1: Update the appointment date and time
    $stmt = $conn->prepare("UPDATE appointment SET Appointment_Date = ?, Appointment_Time = ? WHERE Appointment_ID = ?");
    $stmt->bind_param("ssi", $newDate, $newTime, $id);

    if ($stmt->execute()) {
        $stmt->close();

        // Step 2: Now retrieve full updated appointment details
        $stmt = $conn->prepare("
            SELECT 
                a.Service_Type, 
                u.email, 
                u.fullname, 
                d.name AS dentist_name, 
                d.specialization
            FROM appointment a
            LEFT JOIN users u ON a.Patient_ID = u.id
            LEFT JOIN dentists d ON a.Dentist_ID = d.Dentist_ID
            WHERE a.Appointment_ID = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $appointmentData = $result->fetch_assoc();
        $stmt->close();

        // Step 3: Send the email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'kristianespinase01@gmail.com';
            $mail->Password = 'upin izwz iker gbou';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('UmipigDentalClinic@gmail.com', 'Umipig Dental Clinic');
            $mail->addAddress($appointmentData['email'], $appointmentData['fullname']);

            $mail->isHTML(true);
            $mail->Subject = 'Appointment Rescheduled - Umipig Dental Clinic';
            $mail->Body = "
                <h2>Appointment Rescheduled</h2>
                <p>Dear {$appointmentData['fullname']},</p>
                <p>Your appointment has been rescheduled. Here are your updated details:</p>
                <p><strong>Service:</strong> {$appointmentData['Service_Type']}<br>
                <strong>Date:</strong> {$newDate}<br>
                <strong>Time:</strong> {$newTime}<br>
                <strong>Dentist:</strong> {$appointmentData['dentist_name']} ({$appointmentData['specialization']})</p>
                <p>Please contact us if you have any questions.</p>
                <p>Best regards,<br>Umipig Dental Clinic Team</p>
            ";

            $mail->send();
            echo json_encode(['status' => 'success', 'message' => 'Appointment rescheduled. Email sent.']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'success', 'message' => 'Appointment rescheduled, but email could not be sent. Mailer Error: ' . $mail->ErrorInfo]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error updating appointment: ' . $stmt->error]);
        $stmt->close();
    }

    exit;
}





// Fetch appointments to display
$sql = "SELECT 
            a.Appointment_ID,
            a.Appointment_Time,
            a.Appointment_Date,
            a.Service_Type,
            a.Appointment_Status,
            a.Patient_Name_Custom,
            a.Patient_ID,
            a.Dentist_ID,
            u.fullname AS user_fullname,
            (SELECT name FROM dentists WHERE Dentist_ID = a.Dentist_ID) AS dentist_name
        FROM appointment a
        LEFT JOIN users u ON a.Patient_ID = u.id
        ORDER BY a.Appointment_Date DESC, a.Appointment_Time DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dental Clinic Appointment System</title>
      <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="appointment_module.css" />
  <style>
    table select, table input[type="time"], table input[type="date"], table input[type="text"] {
      width: 90%;
      box-sizing: border-box;
      border: 1px solid #ccc;
      padding: 4px;
      font-size: 12px;
      font-weight: 600;
    }
    .update-btn {
      cursor: pointer;
      padding: 6px 10px;
      background-color: green;
      color: white;
      border: none;
      border-radius: 4px;
    }
    .update-btn:disabled {
      background-color: #ccc;
      cursor: not-allowed;
    }
    /* Modal styles */
    .modal {
      display: none;
      position: fixed;
      z-index: 100;
      left: 0; top: 40px;
      margin-bottom: 200px;
      width: 100%; height: 100%;
      overflow: auto;
      background-color: rgba(0,0,0,0.5);
      justify-content: center;
      align-items: center;
    }
    .modal-content {
      background: white;
      padding: 20px;
      border-radius: 6px;
      width: 300px;
      margin-bottom: 60px;
      margin-top: 40px;

    }
    .modal-content label {
      display: block;
      margin-top: 10px;
    }
    .modal-content input, .modal-content select {
      width: 100%;
      padding: 6px;
      margin-top: 4px;
    }
    .modal-content button {
      margin-top: 15px;
      padding: 8px 12px;
      background-color: #28a745;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
    .modal-content .close-btn {
      background-color: #dc3545;
      margin-left: 10px;
    }
  </style>
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


<div class="container">
<div class="header">
  <h1>Appointments</h1>
  <button id="rescheduleAppointmentBtn">Reschedule Appointment</button>
  <button id="newAppointmentBtn">Add Appointment</button>
</div>


<?php if (isset($_SESSION['message'])): ?>
  <p style="color:green; font-weight:bold;"><?= htmlspecialchars($_SESSION['message']); ?></p>
  <?php unset($_SESSION['message']); ?>
<?php endif; ?>

<div class="appointment-table-container">
  <table id="appointmentTable">
    <thead>
      <tr>
        <th>TIME</th>
        <th>DATE</th>
        <th>PATIENT</th>
        <th>DENTIST</th>
        <th>PROCEDURE</th>
        <th>STATUS</th>
        <th>ACTIONS</th>
      </tr>
    </thead>
    <tbody id="appointmentList">
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <form method="POST" action="appointment_module.php">
              <input type="hidden" name="form_type" value="update">
              <input type="hidden" name="appointment_id" value="<?= $row['Appointment_ID'] ?>">


<td>
  <?php
    $time = $row['Appointment_Time'];
    $dateTime = DateTime::createFromFormat('H:i:s', $time);
    echo $dateTime ? $dateTime->format('g:i A') : htmlspecialchars($time);
  ?>
  <input type="hidden" name="appointment_time" value="<?= htmlspecialchars($row['Appointment_Time']) ?>">
</td>


<td>
  <?= htmlspecialchars($row['Appointment_Date']) ?>
  <input type="hidden" name="appointment_date" value="<?= htmlspecialchars($row['Appointment_Date']) ?>">
</td>


<td>
  <?= !empty($row['user_fullname']) ? htmlspecialchars($row['user_fullname']) : htmlspecialchars($row['Patient_Name_Custom']) ?>
  <input type="hidden" name="patient_name_custom" value="<?= !empty($row['user_fullname']) ? htmlspecialchars($row['user_fullname']) : htmlspecialchars($row['Patient_Name_Custom']) ?>">
</td>

<td>
  <?= htmlspecialchars($row['dentist_name']) ?>
  <input type="hidden" name="dentist_id" value="<?= htmlspecialchars($row['Dentist_ID']) ?>">
</td>

<td>
  <?= htmlspecialchars($row['Service_Type']) ?>
  <input type="hidden" name="procedure" value="<?= htmlspecialchars($row['Service_Type']) ?>">
</td>

<td>
  <select name="status" required>
    <?php 
    $statuses = ['Pending', 'Confirmed', 'Completed', 'Cancelled'];
    foreach ($statuses as $status): 
      $selected = ($status === $row['Appointment_Status']) ? "selected" : "";
    ?>
      <option value="<?= $status ?>" <?= $selected ?>><?= $status ?></option>
    <?php endforeach; ?>
  </select>
</td>

<td>
  <button type="submit" class="update-btn">Update</button>
</td>
        </form>
          </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="7">No appointments found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Add Appointment Modal -->
<div id="appointmentModal" class="modal">
  <div class="modal-content">
    <h3>Add New Appointment</h3>
    <form method="POST" action="appointment_module.php" id="appointmentForm">
      <input type="hidden" name="form_type" value="add">
      
      <label for="appt_time">Time:</label>
      <input type="time" id="appt_time" name="appt_time" required>

      <label for="appt_date">Date:</label>
      <input type="date" id="appt_date" name="appt_date" required>

      <label for="patient_name">Patient Name:</label>
      <input type="text" id="patient_name" name="patient_name_custom" required placeholder="Enter patient name" />

      <label for="dentist_name">Dentist Name:</label>
      <select id="dentist_name" name="dentist_name" required>
        <option value="">Select Dentist</option>
        <?php
        // Reset pointer to loop dentists for modal dropdown
        $dentists_result->data_seek(0);
        while ($dentist = $dentists_result->fetch_assoc()):
        ?>
          <option value="<?= $dentist['Dentist_ID'] ?>"><?= htmlspecialchars($dentist['name']) ?></option>
        <?php endwhile; ?>
      </select>

<label for="procedure">Procedure:</label>
<select id="procedure" name="procedure" required>
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



      <label for="status">Status:</label>
      <select id="status" name="status" required>
        <option value="Pending">Pending</option>
        <option value="Confirmed">Confirmed</option>
        <option value="Completed">Completed</option>
        <option value="Cancelled">Cancelled</option>
      </select>

      <button type="submit">Add Appointment</button>
      <button type="button" id="closeModal" class="close-btn">Cancel</button>
    </form>
  </div>
</div>



<!-- Reschedule Modal -->
<div id="rescheduleModal" class="modal" style="display:none;">
  <div class="modal-content" style="padding:20px; background:#fff; border-radius:5px; width:400px; margin:auto; margin-top:100px; position:relative;">
    <h2>Reschedule Appointment</h2>
    
    <label for="selectAppointment">Select Appointment:</label>
    <select id="selectAppointment">
      <!-- options will be dynamically added here -->
    </select>
    
    <br><br>
    <label for="newDate">New Date:</label>
    <input type="date" id="newDate" required>
    
    <br><br>
    <label for="newTime">New Time:</label>
    <input type="time" id="newTime" required>
    
    <br><br>
    <button id="saveRescheduleBtn">Save</button>
    <button id="cancelRescheduleBtn">Cancel</button>
  </div>
</div>




<script>
  const modal = document.getElementById('appointmentModal');
  const openBtn = document.getElementById('newAppointmentBtn');
  const closeBtn = document.getElementById('closeModal');

  openBtn.onclick = () => {
    modal.style.display = 'flex';
  };

  closeBtn.onclick = () => {
    modal.style.display = 'none';
  };

  window.onclick = (e) => {
    if (e.target === modal) {
      modal.style.display = 'none';
    }
  };

 document.getElementById('rescheduleAppointmentBtn').addEventListener('click', () => {
  document.getElementById('rescheduleModal').style.display = 'block';

  const select = document.getElementById('selectAppointment');
  select.innerHTML = ''; // Clear previous options

  // You can generate these options from PHP as JSON data or embed them inline
  const appointments = [
    <?php
      $result->data_seek(0); // rewind appointments result pointer
      while ($row = $result->fetch_assoc()) {
        $id = $row['Appointment_ID'];
        $patient = !empty($row['user_fullname']) ? $row['user_fullname'] : $row['Patient_Name_Custom'];
        $date = $row['Appointment_Date'];
        $time = DateTime::createFromFormat('H:i:s', $row['Appointment_Time']);
        $timeFormatted = $time ? $time->format('g:i A') : $row['Appointment_Time'];
        echo "{id: $id, label: '$patient on $date at $timeFormatted'},";
      }
    ?>
  ];

  appointments.forEach(app => {
    const option = document.createElement('option');
    option.value = app.id;
    option.textContent = app.label;
    select.appendChild(option);
  });
});

document.getElementById('saveRescheduleBtn').addEventListener('click', () => {
  const appointment_id = document.getElementById('selectAppointment').value;
  const new_date = document.getElementById('newDate').value;
  const new_time = document.getElementById('newTime').value;

  if (!appointment_id || !new_date || !new_time) {
    alert('Please fill all fields.');
    return;
  }

  fetch('appointment_module.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: new URLSearchParams({
      form_type: 'reschedule',
      appointment_id,
      new_date,
      new_time
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.status === 'success') {
      alert(data.message);
      location.reload(); // Reload to show updated data
    } else {
      alert('Error: ' + data.message);
    }
  })
  .catch(err => alert('Request failed: ' + err));
});

document.getElementById('cancelRescheduleBtn').addEventListener('click', () => {
  document.getElementById('rescheduleModal').style.display = 'none';
});




</script>

</body>
</html>
