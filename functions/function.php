<?php
    function createTables($conn){

        if(!$conn){
           echo "Cannot connect to the database";
        } 
        else {
            $query = "CREATE TABLE IF NOT EXISTS users (
            user_ID int(4) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            user_email varchar(50) NOT NULL,
            password varchar(20) NOT NULL,
            profile_name varchar(30) NOT NULL,
            date_started date NOT NULL,
            num_of_friends int UNSIGNED NOT NULL
            );";
            mysqli_query($conn, $query);

            $query = "CREATE TABLE IF NOT EXISTS myfriends (
            user_id INT NOT NULL,
			friend_id INT NOT NULL
            );";
            mysqli_query($conn, $query);
        }
    }

function insertdataintousers($conn){
    if($conn){
            $query = "SELECT * FROM users where 1";
            $result = mysqli_query($conn, $query);

            if(mysqli_num_rows($result)==0){
            $query="LOAD DATA LOCAL INFILE 'user.txt' 
                    INTO TABLE users FIELDS TERMINATED BY ',' 
                    LINES TERMINATED BY '\n'
                    IGNORE 1 ROWS
                    (@user_email,password,profile_name,num_of_friends)
                    SET user_email=@user_email,date_started=CURDATE();";
                    mysqli_query($conn, $query);
            }
         else{
             echo "Cannot connect to the database";
         }
        
    }
}

function insertdataintomyfriend($conn){
    if($conn){
            $query = "SELECT * FROM myFriends where 1";
            $result = mysqli_query($conn, $query);

            if(mysqli_num_rows($result)==0){
                $query="LOAD DATA LOCAL INFILE 'myFriends.txt' 
                INTO TABLE myfriends FIELDS TERMINATED BY ',' 
                LINES TERMINATED BY '\n'
                IGNORE 1 ROWS
                (user_id,friend_id);";
                mysqli_query($conn, $query);
            }
         else{
             echo "Cannot connect to the database";
         }
        
    }
}
function sanitizeInput($conn,$input){
   $input = mysqli_real_escape_string($conn,$input);
   return $input;
}


function getNow($conn){
    $state = "error";
        $errMsg = array();
        $query = "SELECT * FROM users ORDER BY profile_name ASC";
        $result = mysqli_query($conn, $query);
        
        if($result){
            while ($row = mysqli_fetch_assoc($result)) {
                if ($_SESSION['name'] == $row['profile_name']) {
                    $_SESSION['ID'] = $row['user_id'];
                }
        }
}


?>