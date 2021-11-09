<?php
	session_start();	//start the session
	if (isset($_SESSION['email'])) { 
	 session_unset();		//free all session variables
	 session_destroy();		//close the session
	}
	header('Location: index.php');		//redirect to home page
?>	