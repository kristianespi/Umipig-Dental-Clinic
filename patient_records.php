<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "clinic_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Records</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
       * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

body {
    color: #333;
    overflow-x: hidden;
}


/* Header */
header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 20px;
    background-color: #ecf5ff;
    width: 100%;
    box-sizing: border-box;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 56px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    z-index: 1100;
}


.logo-container {
    display: flex;
    align-items: center;
    gap: 10px;
}

.logo-circle {
    width: 40px;
    height: 40px;
    background-color: rgba(255, 255, 255, 0.9);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

    .logo-circle img {
        width: 45px;
        height: 45px;
        object-fit: contain;
    }

.clinic-info h1 {
    font-size: 15px;
    text-align: left;
    color: #333;
    margin: 0 0 5px 0;
}

.clinic-info p {
    font-size: 10px;
    color: #555;
    margin: 0;
}

.main-nav {
    flex-grow: 1;
    display: flex;
    justify-content: center;
    gap: 50px;
}

    .main-nav a {
        text-decoration: none;
        color: #333;
        font-weight: 700;
        font-size: 12px;
        transition: color 0.3s;
    }

        .main-nav a:hover {
            color: #0066cc;
        }

.main-nav a.active {
    color: #0056b3;
    font-weight: bold;
}

.header-right {
    display: flex;
    gap: 20px;
    margin-right: 10px;
    align-items: center;
}

.auth-link {
    text-decoration: none;
    color: #0066cc;
    font-weight: 600;
    font-size: 12px;
    transition: color 0.3s;
}

    .auth-link:hover {
        color: #003d80;
    }

.header-right span {
    color: black;
    font-size: 10px;
}


.welcome-text {
    font-weight: 700;
    font-size: 12px;
    color: #003366;
}

.welcome-text .auth-link {
    font-weight: 600;
    color: #0066cc;
    text-decoration: none;
}

.welcome-text .auth-link:hover {
    color: #003d80;
}

/* Layout */
.dashboard {
    display: flex;
    min-height: 100vh;
}

/* Sidebar */
.sidebar {
    position: fixed;
    top: 50px; /* same as header height */
    left: -260px;
    width: 30vw;
    max-width: 350px;
    min-width: 150px;
    height: calc(100vh - 20px); /* full height minus header */
    background-color: #6b839e;
    color: white;
    padding: 20px 0;
    transition: left 0.8s ease;
    z-index: 1000;
    overflow-x: hidden;
   
}


/* Expand sidebar on hover */
.sidebar:hover {
    left: 0;

}

/* ☰ Menu icon - visible only when sidebar is collapsed */
.menu-icon {
    position: fixed;
    left: 20px;
    top: 90px;
    transform: translateY(-50%);
    font-size: 24px;
    background-color: #6b839e;
    color: white;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: opacity 0.3s;
}

/* Hide menu icon when sidebar is expanded */
.sidebar:hover .menu-icon {
    display: none;
}

/* Nav Menu */
.nav-menu {
    list-style: none;
    margin-top: 40px;
}

.nav-item {
    padding: 12px 20px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.nav-item:hover {
    background-color: rgba(255, 255, 255, 0.1);
}

/* Hide text by default */
.nav-item span {
    display: none;
    white-space: nowrap;
}

/* Show text when sidebar is hovered */
.sidebar:hover .nav-item span {
    display: inline;
}

.nav-link {
    text-decoration: none;
    color: white;
    cursor: pointer;
    transition: background-color 0.3s;
}

        .content-area {
            flex: 1;
            padding: 30px;
        }
        .content-area h2 {
    /* Your styles here */
    color: royalblue;
    font-size: 28px;
    font-weight: bold;
    text-align: left;
    margin-top: 70px;
    margin-left: 100px;
}

        .records-table {
            width: 88%;
            border-collapse: collapse;
            margin-top: 150px;
            margin-left: 150px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .records-table th, .records-table td {
            border: 1px solid #ddd;
            padding: 10px 15px;
            text-align: left;
        }
        .records-table th {
            background-color: #f1f1f1;
            color: #333;
        }
        .btn-view {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 5px 8px;
            cursor: pointer;
            border-radius: 4px;
            font-size: 12px;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 10;
            left: -0px; top: 0;
            width: 100%; height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: white;
            margin: 3% auto;
            padding: 20px;
            width: 32%;
            max-width: 800px;
            max-height: 85vh;
            margin-top: 70px;
            margin-bottom: 80px;
            overflow-y: auto;
            border-radius: 10px;
        }
        .close {
            float: right;
            font-size: 24px;
            cursor: pointer;
        }
        form label {
            font-weight: 500;
            display: block;
            margin-top: 15px;
            color: #2c3e50;
        }
        form input, form select, form textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        form textarea {
            resize: vertical;
        }

        @media (min-width: 768px) {
    .header-content {
        flex-direction: row;
    }

    .header-logo {
        margin-bottom: 0;
    }

    .header-nav {
        flex-direction: row;
    }

        .sidebar {
        width: 100%;
        padding: 10px;
    }

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

<div class="content-area">
    <h2>Patient Records</h2>
    <table class="records-table">
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Age</th>
    <th>Birthdate</th>
    <th>Sex</th>
    <th>Address</th> <!-- New Address header -->
    <th>Action</th>
</tr>

<?php
$sql = "SELECT id, name, age, birthdate, sex, address FROM patient_records";
$result = $conn->query($sql);
if ($result->num_rows > 0):
    while ($row = $result->fetch_assoc()):
?>
<tr>
    <td><?= htmlspecialchars($row['id']) ?></td>
    <td><?= htmlspecialchars($row['name']) ?></td>
    <td><?= htmlspecialchars($row['age']) ?></td>
    <td><?= htmlspecialchars($row['birthdate']) ?></td>
    <td><?= htmlspecialchars($row['sex']) ?></td>
    <td><?= htmlspecialchars($row['address']) ?></td> <!-- New Address cell -->
    <td>
        <a class="btn-view" href="javascript:void(0)" onclick="openModal(<?= $row['id'] ?>)">View</a>
        <a href="dental_records.php?id=<?= $row['id'] ?>" class="btn-view" style="background-color: #28a745; margin-left: 5px;">View Chart</a>
    </td>
</tr>

        <?php endwhile; else: ?>
        <tr><td colspan="9" style="text-align: center;">No records found.</td></tr>

        <?php endif; $conn->close(); ?>
    </table>
</div>

<div id="viewModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3>Patient Details</h3>
        <form id="patientForm">
            <div id="formFields"></div>
        </form>
    </div>
</div>

<script>
function openModal(id) {
    fetch('fetch_patient.php?id=' + id)
        .then(res => res.json())
        .then(data => {
            const formFields = document.getElementById('formFields');
            formFields.innerHTML = '';
            for (let key in data) {
                if (key === 'id' || key === 'created_at') continue;
                const label = key.replace(/([A-Z])/g, ' $1').replace(/^./, str => str.toUpperCase());
                let input;
                if (key.endsWith('Details') || key === 'reason') {
                    input = `<textarea readonly>${data[key] || ''}</textarea>`;
                } else if (["sex", "goodHealth", "underTreatment", "medication", "tobacco", "allergy", "oralIssues", "dentalComplications", "gumBleeding", "toothSensitivity", "grindingClenching", "dentalSurgery", "jawProblems", "pregnant", "birthControl"].includes(key)) {
                    input = `<select disabled><option>${data[key]}</option></select>`;
                } else {
                    input = `<input type="text" value="${data[key] || ''}" readonly>`;
                }
                formFields.innerHTML += `<label>${label}</label>${input}`;
            }
            document.getElementById('viewModal').style.display = 'block';
        });
}
function closeModal() {
    document.getElementById('viewModal').style.display = 'none';
}
</script>

</body>
</html>
