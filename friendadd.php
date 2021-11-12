<?php
    if (session_status() == PHP_SESSION_NONE)session_start();

    if(!isset($_SESSION['login'])){
        header("Location: index.php");
        exit();
    }
    
    echo "
    <p>Welcome, <strong>".$_SESSION['name']."</strong>!</p>
    <p>You can add these people as a friend! Ain't that sweet :D</p>
    ";
    //if pageNum doesn't exist, set var pageNum as a GET method
    //else set it as 1
    if(isset($_GET['pageNum'])){
        $pageNum = $_GET['pageNum'];
    }else{
        $pageNum = 1;
    }

    require_once("functions/connection.php");
    $numFriendsPerPage = 5;
    $lines = ($pageNum-1) * $numFriendsPerPage;
    mysqli_select_db($conn,"social_db");
    $query = "SELECT * FROM users";
    	
    $result = mysqli_query($conn, $query);
    $r_c = mysqli_num_rows($result);
    //round totalPage as a whole number
    $totalPage = $r_c/5;

        if ($pageNum < 2) {
            echo "<a class='button' href='?pageNum=".($pageNum+1)."'> Next </a>";
        } elseif ($pageNum > $totalPage-1) {
            echo "<a class='button' href='?pageNum=".($pageNum-1)."'> Prev </a>";
        } else {
            echo "<a class='button' href='?pageNum=".($pageNum-1)."'> Prev </a>";
            echo "<a class='button' href='?pageNum=".($pageNum+1)."'> Next </a>";
        }
    
?>

        <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] ?>">
            
               <?php
                require_once("functions/connection.php");
                require_once("functions/function.php");
                showRegisteredUsers($conn, $lines, $numFriendsPerPage);
                
                ?>

        </form>
