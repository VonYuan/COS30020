	<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Document</title>
		<link rel="stylesheet" href="style.css">
	</head>
	<body>
	<h1 class="title">Sign up</h1>
<div class ="signupFrm">

	<form class ="form" id ="signupform" name="signupform" method="POST" action="<?php echo $_SERVER['PHP_SELF'] ?>">
	<div class="inputContainer">
			<label for="email" class="label">Email:</label>
			<input type="text" class="input1" id="email" name="email" placeholder='e.g."abcd@gmail.com"'/>
</div>
<div class="inputContainer">
			<label for="pname" class="label">Profile Name:</label>
			<input type="text" class="input1"id="pname" name="pname" placeholder='e.g."George Russell"'/>
</div>
<div class="inputContainer">
			<label for="password" class="label">Password:</label>
			<input type="password" class="input1"id="password" name="password" placeholder='e.g."abcd1234"'/>
</div>
<div class="inputContainer">
			<label for="cpassword" class="label">Confirm Password:</label>
			<input type="password" class="input1"id="cpassword" name="cpassword"/>
			</div>
		<input class="submitBtn" type="submit" value="Register" id="register" name="register"/>	
		<input class="submitBtn" type="reset" value="Clear" id="clear" name="clear"/>
		<input class="submitBtn" type="button" value="Home" class="homebutton" id="btnHome" onClick="document.location.href='index.php'" />
		</form>
</div>
<?php
		
	$emailErr=$pnameErr=$passwordErr=$cpasswordErr="";
	$email=$pname=$password=$cpassword="";
	$results = true;
	include_once("functions/connection.php");
	if(isset($_POST["register"])){
			//if the register button is clicked
		if(isset($_POST["email"]) and !empty($_POST["email"])){		//if email is not empty
			$email = mysqli_escape_string($conn,$_POST['email']);
			if(!filter_var($email, FILTER_VALIDATE_EMAIL)){		//validate email format
				$emailErr="Please enter a valid email";		//error message
				$results=false;
			}

		}else{
			$emailErr="Please enter your email";		//error message
			$results=false;
		}
		
		if(isset($_POST["pname"]) and !empty($_POST["pname"])){		//if the profile name is not empty
			$pname=$_POST["pname"];
			if(!preg_match('/^[a-zA-Z ]+$/',$pname)){		//compare the profile name to see if it consists of only letters or space
				$pnameErr="Please enter only letters for your profile name";		//error message
				$results=false;
			}elseif(strlen($pname)>25){		//compare the length of the profile name to see if it exceeds 30
				$pnameErr="The profile name can only contain a maximum of 25 characters";		//error message
				$results=false;
			}
		}else{
			$pnameErr="Please enter your profile name";		//error message
			$results=false;
		}
		
		
		if(isset($_POST["password"]) and !empty($_POST["password"])){		//if the password is not empty
			$password=$_POST["password"];
			
				if( strlen($password) < 8 ) {
				$passwordErr="Password Must At Least 8 Character";
				}
				
				if(!preg_match("#[0-9]+#", $password) ) {
				$passwordErr = "Password must include at least one number!";
				}
				
				if(!preg_match("#[a-z]+#", $password) ) {
				$passwordErr= "Password must include at least one Lowercase Letter!";
				}
				
				if(!preg_match("#[A-Z]+#", $password) ) {
				$passwordErr= "Password must include at least one Capital Letter!";
				}
				
				if(!preg_match("#\W+#", $password) ) {
				$passwordErr= "Password must include at least one symbol!";
				}
		}else{
			$passwordErr="Please enter your password";		//error message
			$results=false;
		}
		
		if(isset($_POST["cpassword"]) and !empty($_POST["cpassword"])){		//if the confirm password is not empty
			$cpassword=$_POST["cpassword"];
			if($cpassword!=$password){		//if the confirm password does not match the password
				$results=false;
				$cpasswordErr="Both passwords do not match.";		//error message
			}
		}else{
			$results=false;
			$cpasswordErr="Please confirm your password";		//error message
		}
		
		if($results){
			require_once("functions/connection.php");
			mysqli_select_db($conn,$db);
			$spname = mysqli_escape_string($conn,$_POST['pname']);
    		$semail = mysqli_escape_string($conn,$_POST['email']);
    		$spassword = mysqli_escape_string($conn,$_POST['password']);
    		$scpassword = mysqli_escape_string($conn,$_POST['cpassword']);
			$query = "SELECT*FROM users WHERE user_email='$semail'";
			$result=mysqli_query($conn,$query);	
			
			$r_cout=mysqli_num_rows($result);
	
			if($r_cout < 1){
				$date=date('Y-m-d');
				$query="INSERT INTO users (user_email, password, profile_name, date_started, num_of_friends)
				VALUES('$semail','$spassword','$spname','$date',0)";
				mysqli_query($conn,$query);

				
			}else{
				$emailErr="An account with the same email already exists.";		//error message
				$results=false;
			}
			mysqli_free_result($result);		//fetch rows from a result-set, then free the memory associated with the result
			mysqli_close($conn);		//close connection
		}
		
		if($results){
			session_start();
			$state = "success";		//start session
			$_SESSION["email"] = $semail;
			$_SESSION['login'] = "success";
        	$_SESSION['name'] = $spname;
            $_SESSION['noOfFriends'] = 0;
			
			header("Location: friendadd.php");		//redirect to friendadd.php
		}else{
		echo"<div class='f1f'>";
		echo"<fieldset class='f1'>";
		echo"<legend>Error Message</legend>";
		echo $emailErr."<br>";
		echo $pnameErr."<br>";
		echo $passwordErr."<br>";
		echo $cpasswordErr;
		echo"</fieldset>";
		echo"</div>";
		}
	}
	?>
	</body>
	</html>