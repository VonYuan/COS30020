<?php 
$host = "localhost";
$user = "root";
$pass = "";
$db = "social_db";

$conn = @mysqli_connect($host, $user, $pass)or die("Failed to connect to server");
mysqli_options($conn, MYSQLI_OPT_LOCAL_INFILE, true);
?>