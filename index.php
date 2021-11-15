<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Assignment 2 Friends</title>
</head>
<body>
    <h1>My Social Circle </h1>
    <img id ="img" src="images/pic.png" alt="VoonYuan">
    <p>Name: Voon Boon Yuan</p>
    <p>Student ID: Voon Boon Yuan</p>
    <p>Email: Voon Boon Yuan</p>

    <fieldset>
        <legend>Declaration</legend>
            <p>
                I declare that this assignment is my individual work. I have not work collaboratively nor have I copied from any other student's work or from any other source.
                I have not engaged another party to complete this assignment. I am aware of the University’s policy with regards to plagiarism. 
                I have not allowed, and will not allow, anyone to copy my work with the intention of passing it off as his or her own work.”
            </p>
    </fieldset>
    <fieldset>
        <legend>Message</legend>
        <div class ="info">
           <?php
           $success = true;
           require_once("functions/connection.php");
           if(!mysqli_select_db($conn,"social_db")){
            $sql = "CREATE DATABASE social_db";
            mysqli_query($conn,$sql);
            mysqli_select_db($conn,$db);
            require_once("functions/function.php");
            createTables($conn);
            insertdataintousers($conn);
            insertdataintomyfriend($conn);
           
           }
           else{
           }
           if($success==true){
               echo"
               <p style ='color:green'>DataBase Social_db Created Success</p>
               <p style ='color:green'>Tables User And myFriends Created Success</p>
               <p style ='color:green'>Tables Populated Success</p>
               ";
               

           }
           
           ?>
           </div>
    </fieldset>

    <nav>
    
	<ul class = "navbar">
        <div id ="navb">
		<li><a class ="nav" href="index.php">Home</a></li>
		<li><a class ="nav" href="signup.php">Sign-Up</a></li>
		<li><a class ="nav" href="login.php">Log-In</a></li>
		<li><a class ="nav" href="about.php">About</a></li>
        </div>
	</ul>
	</nav>
    
</body>
</html>