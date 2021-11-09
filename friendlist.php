
   
<?php
    include("functions/function.php");

    if (session_status() == PHP_SESSION_NONE)session_start();

    if(!isset($_SESSION['login'])){
        header("Location: index.php");
        exit();
    }

    echo "
    <p>Welcome, <strong>".$_SESSION['name']."</strong>!</p>
    <p>Here's your friends list. Currently you have ".$_SESSION['noOfFriends']." friends!</p>
    ";

    if($_SESSION['noOfFriends'] == 0){
        echo "<p>Sorry about that</p>";
    }

    //if pageNum doesn't exist, set var pageNum as a GET method
    //else set it as 1
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

    if($totalFriends > 5){
        if($pageNum < 2){
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
                require_once("functions/connection.php");
                showFriendsList($conn, $offSet, $numFriendsPerPage);
            ?>
            </table>
        </form>

