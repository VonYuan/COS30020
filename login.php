<?php
	$emailErr=$passwordErr="";
	$email=$password="";
	if(isset($_POST["login"])){		//if the user login
		if(isset($_POST["lemail"]) and !empty($_POST["lemail"])){		//if the login email is not empty
			$email=$_POST["lemail"];		//$email is assigned
		}
		
		if(isset($_POST["lpassword"]) and !empty($_POST["lpassword"])){		//if the login password is not empty
			$password=$_POST["lpassword"];		//$password is assigned
		}else{
			$passwordErr="Please enter your password";		//error message if password is not entered
		}
		$servername="localhost";		//server name
		$username="root";		//user name
		$pwd="";
		$conn=@mysqli_connect($servername,$username,$pwd)		//start connection
			or die("Failed to connect to server");		//connection failed and error message
		@mysqli_select_db($conn,"social_db")		//select the 101223947 database
			or die("Database not available");		//error message if database is not available
		
		$query="SELECT * FROM users WHERE user_email='$email'";
		$result=mysqli_query($conn,$query);		//query
		if(mysqli_num_rows($result)==1){		
			$row=mysqli_fetch_assoc($result);		//fetch a result row as an associative array
			if($row["password"]==$password){
				session_start();		//start the session
				$_SESSION["email"] = $email;
				$_SESSION['name'] = $row["profile_name"];
				$_SESSION['noOfFriends'] = $row["num_of_friends"];
				$_SESSION['login'] = "success";
				
				header("Location: friendlist.php");		//redirect to friendlist
			}else{
				$passwordErr="The password is incorrect";		//incorrect password
			}
		}else{
			$emailErr="The email does not exist";		//email does not exist
		}
		mysqli_close($conn);		//close connection
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<link rel="stylesheet" href="style.css">
</head>

<body>
<h1 class="title">Login</h1>


	<div class="signupFrm">
	<form class ="form" id ="loginform" name="loginform" method="POST" action="login.php">
	<div class="inputContainer">
		<label for="lemail" class="label">Email:</label>
		<input type="text"  class="input1" id="lemail" name="lemail" class="input" value="<?php echo $email ?>"/><br>

</div>
<div class="inputContainer">	
		<label for="lpassword" class="label">Password:</label>
		<input type="password"  class="input1" id="lpassword" name="lpassword" class="input"/><br>


</div>
	<div class="error"><?php echo $emailErr?></div>	
	<div class="error"><?php echo $passwordErr?></div>	
	<input class="submitBtn" type="submit" value="Log in" id="login" name="login"/>	
	<input class="submitBtn" type="reset" value="Clear" id="clear" name="clear"/>
	<input class="submitBtn" type="button" value="Home" class="homebutton" id="btnHome" onClick="document.location.href='index.php'" />
	
	</form>

	</div>
	




</body>
</html>