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
    

<?php
    if (session_status() == PHP_SESSION_NONE)session_start();

    if(!isset($_SESSION['login'])){
        header("Location: index.php");
        exit();
    }
    
    echo "
    <p>Welcome, <strong>".$_SESSION['name']."</strong>!</p>
    <p>Here's your friends list. Currently you have ".$_SESSION['noOfFriends']." friends!</p>
    ";
    //if pageNum doesn't exist, set var pageNum as a GET method
    //else set it as 1
    if(isset($_GET['pageNum'])){
        $pageNum = $_GET['pageNum'];
    }else{
        $pageNum = 1;
    }

    require_once("functions/connection.php");
    $lines = ($pageNum-1) * 5;
    mysqli_select_db($conn,"social_db");
    $query = "SELECT * FROM users";
    	
    $result = mysqli_query($conn, $query);
    $r_c = mysqli_num_rows($result);
    //round totalPage as a whole number
    $totalPage = $r_c/5;
    
    require_once("functions/function.php");
    echo"<form method='POST' action='friendadd.php'>";

     require_once("functions/connection.php");
     require_once("functions/function.php");
     echo "<div class= 'signupFrm'>";
     echo "<table class='styled-table'>";
     echo"<thead>
         <tr>
             <th>Name</th>
             <th>Click To Add</th>
         </tr>
     </thead>";
     showRegisteredUsers($conn, $lines);
     echo"</table>";
     echo"</div>";


     echo '</form>';
        if ($pageNum < 2) {
            echo"<div class = 'page'>";
            echo "<li class= 't'><a class='button' href='?pageNum=".($pageNum+1)."'> Next </a></li>";
        } elseif ($pageNum > $totalPage-1) {
            echo"<div class = 'page'>";
            echo "<li class= 't'><a class='button' href='?pageNum=".($pageNum-1)."'> Prev </a></li>";
        } else {
            echo"<div class = 'page'>";
            echo "<li class= 't'><a class='button' href='?pageNum=".($pageNum-1)."'> Prev </a></li>";
            echo "<li class= 't'><a class='button' href='?pageNum=".($pageNum+1)."'> Next </a></li>";
            echo"</div>";
        }
    
?>
        </body>
</html>