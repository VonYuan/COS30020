<?php
if (session_status() == PHP_SESSION_NONE) session_start();

echo "Logged out successfully";

session_unset();
session_destroy();
header("location: index.php");
exit();
?>