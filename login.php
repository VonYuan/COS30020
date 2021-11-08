<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<form method="POST" action="login.php">
        <p><input type="email" name="email" value="<?php echo isset($_POST["userEmail"]) ? $_POST["userEmail"] : ''; ?>"></p>
        <p><input type="password" name="password"></p>
        <input type="submit" name="postForm" value="Login">
        <input type="reset" name="resetForm" value="Clear">
    </form>

    <?php
    $errMsg = "";
    $state = "error";
	$email=$password="";
	include_once("functions/function.php");
    if(isset($_POST['userEmail'])){
		$email = sanitizeInput($conn,$_POST['userEmail']);
	}	 
    if(isset($_POST['password'])){
		$password = sanitizeInput($conn,$_POST['password']);
	}
    if(isset($_POST['postForm'])){
		$query="SELECT * FROM users WHERE user_email='$email'";
		$result=mysqli_query($conn,$query);		//query
		if(mysqli_num_rows($result)==1){		
			$row=mysqli_fetch_assoc($result);		//fetch a result row as an associative array
			if($row["password"]==$password){
				session_start();		//start the session
				$_SESSION["email"] = $email;
				$_SESSION['name'] = $row['profile_name'];
                $_SESSION['noOfFriends'] = $row['num_of_friends'];
				$state = "success";
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
</body>
</html>