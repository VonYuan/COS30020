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

            $query = "CREATE TABLE IF NOT EXISTS myFriends (
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
function getNow($conn){

        $query = "SELECT * FROM users ORDER BY profile_name ASC";
        $result = mysqli_query($conn, $query);
        
        if($result){
            while ($row = mysqli_fetch_assoc($result)) {
                if ($_SESSION['name'] == $row['profile_name']) {
                    $_SESSION['ID'] = $row['user_ID'];
                }
            }
        }   
}
function showFriends($conn, $lines){
    if($conn){
        mysqli_select_db($conn,"social_db");
        $query = "SELECT * FROM users ORDER BY profile_name ASC";
        $result = mysqli_query($conn, $query);

        if($result){
            getNow($conn); 
            while ($row = mysqli_fetch_assoc($result)) {
                $f_friendID = $row['user_ID'];
                $f_name = $row['profile_name'];

                $searchQuary = "SELECT * FROM myFriends WHERE user_ID = '".$_SESSION['ID']."' LIMIT $lines, 5";
                $searchResult = mysqli_query($conn, $searchQuary);

                while ($row = mysqli_fetch_assoc($searchResult)) {
                    $userID= $row['friend_id'];
                    if ($userID == $f_friendID) {
                        echo "
                        <tr>
                            <td>
                                <p> $f_name </p>
                            </td>
                            <td>
                                <input type='submit' name='FRND_".$f_friendID."' value='unfriend'>
                            </td>
                        </tr>
                        ";
                    }
                }
            }
            mysqli_free_result($searchResult);
            deleteFriends($conn);
        }
    }
}

function deleteFriends($conn){
    if($conn){
        mysqli_select_db($conn,"social_db");
        $query = "SELECT * FROM myFriends WHERE user_ID = '".$_SESSION['ID']."'";
        $result = mysqli_query($conn, $query);

        if($result){
            while ($row = mysqli_fetch_assoc($result)) {
                $friend_id = $row['friend_id'];
                if(isset($_POST["FRND_$friend_id"])){
                    mysqli_select_db($conn,"social_db");
                    $query = "DELETE FROM myFriends WHERE user_ID = ".$_SESSION['ID']." AND friend_id = $friend_id";
                    $result = mysqli_query($conn, $query);

                    if($result){
                        $_SESSION['noOfFriends']--;
                        $query = "UPDATE users SET num_of_friends = '".$_SESSION['noOfFriends']."' WHERE user_ID  = '".$_SESSION['ID']."'";
                        $result = mysqli_query($conn, $query);
            
                        $query = "SELECT profile_name FROM users WHERE user_ID  = '$friend_id'";
                        $result = mysqli_query($conn, $query);
            
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "Friend Removed", $row['profile_name']." is no longer your friend. <br> <em>Please refresh your page to see changes.</em>";
                            
                        }
                    }

                }
                
            }

            mysqli_free_result($result);
            mysqli_close($conn);
        }
    }
}


function showRegisteredUsers($conn, $lines){
    $state = "error";
    if($conn){
        getNow($conn);
        $query = "SELECT user_ID, profile_name FROM users 
        WHERE user_ID NOT IN (SELECT friend_id FROM myFriends where user_ID=".$_SESSION['ID'].")  AND user_ID != ".$_SESSION['ID']."
        GROUP BY profile_name LIMIT $lines, 5"; 
        $result = mysqli_query($conn, $query);
        if($result){
            while ($row = mysqli_fetch_assoc($result)) {
                $profile_name = $row['profile_name'];
                $userID = $row['user_ID'];
                echo "
            
                    <tr>
                        <td>
                            $profile_name
                        </td>
                        <td>
                            <input type='submit' name='Friends".$userID."' value='Add Friend'>
                        </td>
                    </tr>

                    ";
                    
        }
                    mysqli_free_result($result);
                    addFriendLogic($conn);
    }
        else{
            echo"Cannot Execute The Query";
        }
    }
    else{
        echo"Cannot Connect To The Database";
    }
    
}

function addFriendLogic($conn){
    if($conn){
        mysqli_select_db($conn,"social_db");
        $query = "SELECT * FROM users WHERE user_ID != '".$_SESSION['ID']."'";
        $result = mysqli_query($conn, $query);
    }if ($result){
            while ($row = mysqli_fetch_assoc($result)) {
                $userID = $row['user_ID']; 
                if(isset($_POST["Friends$userID"])){
                    $query = "SELECT * FROM users ORDER BY profile_name ASC";
                    $result = mysqli_query($conn, $query);
        
                    if($result){
                    while ($row = mysqli_fetch_assoc($result)) {
                        if ($_SESSION['name'] == $row['profile_name']) {
                            $_SESSION['ID'] = $row['user_ID'];
                        }
                    }
                    $query = "INSERT INTO myFriends VALUES(".$_SESSION['ID'].", $userID)";
                    $result = mysqli_query($conn, $query);
                    if($result){
                        $_SESSION['noOfFriends']++;
                        $query = "UPDATE users SET num_of_friends = '".$_SESSION['noOfFriends']."' WHERE user_id = '".$_SESSION['ID']."'";
                        $result = mysqli_query($conn, $query);

                        $query = "SELECT profile_name FROM users WHERE user_ID  = '$userID'";
                        $result = mysqli_query($conn, $query);

                        while ($row = mysqli_fetch_assoc($result)) {
                        echo "<div class='t1'>";
                        echo"Friend Added", $row['profile_name']." is now your new friend!<br>Please refresh your page to see changes";
                        echo "</div>";
                        }
                }
            }   
        }
            }
        }else{
            echo"Cannot connect to the database";
        }
        mysqli_free_result($result);
        mysqli_close($conn);
    }
?>