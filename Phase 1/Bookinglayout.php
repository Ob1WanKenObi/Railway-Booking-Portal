<html>
<?php
// Initialize the session
session_start();
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: agentlogin.php");
    exit;
}
?>
<head>
    <link rel="stylesheet" href="style/BookingLayout.css">
</head>
<?php
$server = "localhost";
$username = "root";
$password = "";
$database = "test";
$conn = mysqli_connect($server, $username, $password, $database);
if (!$conn) {
    die("connection to server failed");
}
?>

<body>
    <datalist id="Stations">
            <?php
            $result = $conn->query("(SELECT DISTINCT START_PT FROM TRAINS_CREATED) UNION (SELECT DISTINCT END_PT FROM TRAINS_CREATED) ");
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<option value=" . $row["START_PT"] . ">";
                }
            } else {
                echo "No stations exist.";
            }
            ?>
    </datalist>

    <div class="User">
        <p>Logged in as : <?php echo $_SESSION["username"]; ?></p>
        <a href="logout.php">Logout</a>
    </div>
    
    <div class="Release-train" id="checkTrain" style="height: 300px; width: 800px;">
        <form name="booking_f" method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
            <input list="Stations" name="startPt" placeholder="Starting Station" required>
            <input list="Stations" name="endPt" placeholder="Destination Station" required>
            <input type="text" name="mydate" min="2020-11-28" onfocus="(this.type='date')" onblur="(this.type='text')" placeholder="Journey date" required>
            <input type="submit" name="submitTrainQ" value="Check availability">
        </form>
    </div>

    <?php
    function list_of_trains($conn)
    {
        $start = $_POST['startPt'];
        $end = $_POST['endPt'];
        $day = $_POST['mydate'];
        $i = 0;
        $result = $conn->query("SELECT TRAINS_RELEASED.ID as id, TRAINS_CREATED.start_time AS start_time, TRAINS_CREATED.end_time AS end_time,TRAINS_RELEASED.ac_num as ac_num, TRAINS_RELEASED.sl_num as sl_num, TRAINS_CREATED.tname as tname
        FROM TRAINS_RELEASED, TRAINS_CREATED 
        WHERE TRAINS_RELEASED.jdate='$day' AND TRAINS_CREATED.start_pt='$start' and TRAINS_CREATED.end_pt='$end' AND TRAINS_RELEASED.ID=TRAINS_CREATED.ID ");
        if (!empty($result) && $result->num_rows > 0) {
            $URL=$_SERVER["PHP_SELF"];
            echo "<div class='Release-train' id='selectTrain' style='display: none;' >
                        <h1>Available Trains</h1>
                        <form method='post' name='train_selection' action='$URL'>";
            while ($row = $result->fetch_assoc()) {
                $today = str_replace("-","",$day);
                echo "<label>
                        <input type='radio' name='tbb' value='" . $row["id"] . "_" . $today . "' ";
                if ($i == 0) echo " checked";
                else $i = 1;
                echo ">";
                echo "<p>Train " .$row["id"]. "</p><br/>";
                echo "<p>" .$day."</p><br/>";
                echo "<p>".$start . "-->" . $end."</p><br/>";
                echo "<p> Start Time: ".$row["start_time"] ."<br>End Time: " .$row["end_time"]."</p><br></label>";
            }
            echo "<input type='submit' value='Book this train' name='trainSelectQ'></form> 
                    <a href='bookinglayout.php'>Choose another date</a> 
                </div>";
        } 
        else{
            echo "<div class='Release-train' id='selectTrain' style='display: none;' >
            <h1>No Train Found</h1>
            <a href='bookinglayout.php'>Choose another date</a>  
            </div>"; // Check the href part pls.
        }
        
    }
    if (isset($_POST['submitTrainQ'])) {
        list_of_trains($conn);
        echo "<script> document.getElementById('checkTrain').style.display='none';
        document.getElementById('selectTrain').style.display=''; </script>";
    }
    if(isset($_POST['trainSelectQ'])){
        $_SESSION['mytbb']=$_POST['tbb'];
        header("location: portal.php");
    }
    ?>
</body>

</html>