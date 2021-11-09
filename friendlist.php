<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
    if(!isset($_SESSION['login'])){
        header("Location: login.php");
        exit();
    }

    echo "
    <p>Welcome, <strong>".$_SESSION['name']."</strong>!</p>
    <p>Here's your friends list. Currently you have ".$_SESSION['noOfFriends']." friends!</p>
    ";

    if($_SESSION['noOfFriends'] == 0){
        echo "<p>You Have No Friends</p>";
    }

    if(isset($_GET['pageNum'])){
        $pageNum = $_GET['pageNum'];
    }else{
        $pageNum = 1;
    }

    require_once("functions/connection.php");
    $numFriendsPerPage = 5;
    $offSet = ($pageNum-1) * $numFriendsPerPage;
    $totalFriends = $_SESSION['noOfFriends'];
    //round totalPage as a whole number
    $totalPage = ceil($totalFriends / $numFriendsPerPage);

    if($totalFriends >= 5){
        if($pageNum <= 2){
            echo "<a class='button' href='?pageNum=".($pageNum+1)."'> Next </a>";
        }elseif($pageNum > $totalPage-1){
            echo "<a class='button' href='?pageNum=".($pageNum-1)."'> Prev </a>";
        }else{
            echo "<a class='button' href='?pageNum=".($pageNum-1)."'> Prev </a>";
            echo "<a class='button' href='?pageNum=".($pageNum+1)."'> Next </a>";
        }
    }
    
?>
        <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] ?>">
            <table class="friendList">
            <?php
                require_once("functions/settings.php");
                $query = "SELECT * FROM users ORDER BY profile_name ";
                $result = mysqli_query($conn, $query);
                while ($row = mysqli_fetch_assoc($result)) {
                    if ($_SESSION['name'] == $row['profile_name']) {
                        $_SESSION['ID'] = $row['user_id'];
                    }
                }
                while ($row = mysqli_fetch_assoc($result)) {
                    $f_friendID = $row['friend_id'];
                    $f_name = $row['profile_name'];
                }
                $query = "SELECT * FROM myfriends WHERE friend_id1 = '".$_SESSION['ID']."' LIMIT $offset, $numOfPage";
                $result = mysqli_query($conn, $query);
                
                while ($row = mysqli_fetch_assoc($result)) {
                    $myf_friendID2 = $row['friend_id2'];
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
            ?>
            </table>
        </form>

</body>
</html>