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
                    $_SESSION['ID'] = $row['user_ID'];
                }
        }
}

}




function showFriendsList($conn, $offset, $numOfPage){
    $state = "error";

    if(!$conn){
        echo "Cannot connect to the database";
        
    } else {
        mysqli_select_db($conn,"social_db");
        $query = "SELECT * FROM users ORDER BY profile_name ASC";
        $result = mysqli_query($conn, $query);

        if(!$result){
            echo "Fetch Error";

        }else{
            getNow($conn); 
            while ($row = mysqli_fetch_assoc($result)) {
                $f_friendID = $row['user_ID'];
                $f_name = $row['profile_name'];

                $searchQuary = "SELECT * FROM myFriends WHERE user_ID = '".$_SESSION['ID']."' LIMIT $offset, $numOfPage";
                $searchResult = mysqli_query($conn, $searchQuary);

                while ($row = mysqli_fetch_assoc($searchResult)) {
                    $myf_friendID2= $row['friend_id'];
                    if ($myf_friendID2 == $f_friendID) {
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
            mysqli_free_result($result);
            removeFriendLogic($conn);
        }
    }
}

function removeFriendLogic($conn){
    $state = "error";


    if(!$conn){
        echo "Cannot connect to the database";
    } else {
        mysqli_select_db($conn,"social_db");
        $query = "SELECT * FROM myFriends WHERE user_ID = '".$_SESSION['ID']."'";
        $result = mysqli_query($conn, $query);

        if (!$result) {
          echo"Cannot fetch requested query";

        } else {
            while ($row = mysqli_fetch_assoc($result)) {
                $myf_friendID2 = $row['friend_id'];
                /*set the buttons to FRND_(their id) and called removeFriend to get functions*/
                echo((isset($_POST["FRND_$myf_friendID2"]))? removeFriend($conn, $myf_friendID2): "");
            }

            mysqli_free_result($result);
            mysqli_close($conn);
        }
    }
}


function removeFriend($conn, $userID){
    $state = "error";
    $errMsg="";

    if (!$conn) {
        echo "Cannot connect to the database";

    } else {
        mysqli_select_db($conn,"social_db");
        $query = "DELETE FROM myFriends WHERE user_ID = ".$_SESSION['ID']." AND friend_id = $userID";
        $result = mysqli_query($conn, $query);

        if (!$result) {
            echo"Cannot fetch requested query";

        } else {
            $state = "success";
            $_SESSION['noOfFriends']--;
            $query = "UPDATE users SET num_of_friends = '".$_SESSION['noOfFriends']."' WHERE user_ID  = '".$_SESSION['ID']."'";
            $result = mysqli_query($conn, $query);

            $query = "SELECT profile_name FROM users WHERE user_ID  = '$userID'";
            $result = mysqli_query($conn, $query);

            while ($row = mysqli_fetch_assoc($result)) {
                echo "Friend Removed", $row['profile_name']." is no longer your friend. <br> <em>Please refresh your page to see changes.</em>";
                
            }
        }
    }
}


function showRegisteredUsers($conn, $lines, $numOfPage){
    $state = "error";
    if($conn){
        getNow($conn);
        $query = "SELECT user_ID, profile_name FROM users 
        WHERE user_ID NOT IN (SELECT friend_id FROM myFriends where user_ID=".$_SESSION['ID'].")  AND user_ID != ".$_SESSION['ID']."
        GROUP BY profile_name LIMIT $lines, $numOfPage"; 
        $result = mysqli_query($conn, $query);
        if($result){
            while ($row = mysqli_fetch_assoc($result)) {
                $profile_name = $row['profile_name'];
                $userID = $row['user_ID'];
                echo "
                    <tr>
                        <td>
                            <p>$profile_name</p>
                        </td>
                        <td>
                            <input type='submit' name='Friends".$userID."' value='Add Friend'>
                        </td>
                    </tr>
                    ";
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
    $state = "error";
    if(!$conn){
        array_push($errMsg, "Mercury Server", "Cannot connect to the database");
        return displayMessage($errMsg, $state);
    } else {
        mysqli_select_db($conn,"social_db");
        $query = "SELECT * FROM users WHERE user_ID != '".$_SESSION['ID']."'";
        $result = mysqli_query($conn, $query);

        if (!$result) {
            array_push($errMsg, "Query", "Cannot fetch requested query");
            return displayMessage($errMsg, $state);
        } else {
            while ($row = mysqli_fetch_assoc($result)) {
                $f_userID = $row['user_ID']; 
                echo((isset($_POST["FRND_$f_userID"]))? addFriend($conn, $f_userID): "");
            }
            mysqli_free_result($result);
            mysqli_close($conn);
        }
    }
}

function addFriend($conn, $userID){
    $state = "error";
    $errMsg = array();

    if (!$conn) {
        array_push($errMsg, "Mercury Server", "Cannot connect to the database");
        return displayMessage($errMsg, $state);
    } else {
        getNow($conn);
        $query = "INSERT INTO myFriends VALUES(".$_SESSION['ID'].", $userID)";
        $result = mysqli_query($conn, $query);

        if (!$result) {
            array_push($errMsg, "Query", "Cannot fetch requested query");
            return displayMessage($errMsg, $state);
        } else {
            $state = "success";
            $_SESSION['noOfFriends']++;
            $query = "UPDATE users SET num_of_friends = '".$_SESSION['noOfFriends']."' WHERE friend_id = '".$_SESSION['ID']."'";
            $result = mysqli_query($conn, $query);

            $query = "SELECT profile_name FROM users WHERE user_ID  = '$userID'";
            $result = mysqli_query($conn, $query);

            while ($row = mysqli_fetch_assoc($result)) {
                array_push($errMsg, "Friend Added", $row['profile_name']." is now your new friend!<br> <em>Please refresh your page to see changes.</em>");
                return displayMessage($errMsg, $state);
            }
        }
    }
}


?>