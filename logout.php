<?php
session_start();
session_unset();  // remove all session variables
session_destroy(); // destroy the session
header("Location: home.php"); // redirect to login page
exit();
?>