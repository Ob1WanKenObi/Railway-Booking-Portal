<html>

<head>
    <link rel="stylesheet" href="search_p2.css">
</head>
<?php
$server = "localhost";
$username = "root";
$password = "";
$database = "phase2";
$conn = mysqli_connect($server, $username, $password, $database);
if (!$conn) {
    die("connection to server failed");
}
?>

<body>
    <datalist id="Stations">
            <?php
            $result = $conn->query("(SELECT DISTINCT START_PT FROM TRAINS) UNION (SELECT DISTINCT END_PT FROM TRAINS) ");
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<option value=" . $row["START_PT"] . ">";
                }
            } else {
                echo "No stations exist.";
            }
            ?>
    </datalist>
    
    <div class="Release-train" id="checkTrain" style="height: 300px; width: 800px;">
        <form name="booking_f" method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
            <input list="Stations" name="startPt" placeholder="Starting Station" required>
            <input list="Stations" name="endPt" placeholder="Destination Station" required>
            <input type="text" name="mydate" min="2020-11-28" onfocus="(this.type='date')" onblur="(this.type='text')" placeholder="Journey date" required>
            <input type="submit" name="submitTrainQ" value="Check availability">
        </form>
        <a href = "createtrain.php"> Create a train </a>
    </div>

    <?php
    function list_of_trains($conn)
    {
        $start = $_POST['startPt'];
        $end = $_POST['endPt'];
        $day = $_POST['mydate'];
        $i = 0;
        $b1=0;
    
        //check for direct trains
        $resultd = $conn->query("SELECT a.id as id1, a.tname as t1, a.start_pt as src, a.start_time as st1, a.end_time as et1, a.end_pt as dest 
        FROM trains as a WHERE a.start_pt='$start' and a.end_pt='$end' and a.jdate= '$day' ");
        echo "<div class='Release-train' id='selectTrain' style='display: none;'>
                <h1>Available Trains on ". $day."</h1>";
        if (!empty($resultd) && $resultd->num_rows > 0) {
            $b1=1;
            while ($row = $resultd->fetch_assoc()) {
                $today = str_replace("-","",$day);
                echo "<label class='trainInfo'>";
                echo "<p style='font-weight: 700; margin: 0 auto; display: block;'>Direct Train</p>";
                echo "  <div class='flexBox'>
                            <p>Train :".$row["id1"]."<br>" . $row["t1"]." </p>
                            <p>".$row["src"]." to ".$row["dest"]."</p>
                            <p>Start time: ".$row["st1"]." <br>EndTime :".$row["et1"]."</p>
                        </div>
                        </label>";
            }
        } 

        //check for indirect trains
        $result = $conn->query("SELECT a.id as id1, a.tname as t1, a.start_pt as src, a.start_time as st1, a.end_time as et1, a.end_pt as break_stn, b.id as id2, b.tname as t2, b.start_time as st2, b.end_time as et2, b.end_pt as dest 
        FROM trains as a, trains as b WHERE a.start_pt='$start' and b.end_pt='$end' and a.end_pt=b.start_pt and a.jdate=b.jdate and a.end_time < b.start_time and a.jdate='$day' ");
        if (!empty($result) && $result->num_rows > 0) {
            $b1=1;
            while ($row = $result->fetch_assoc()) {
                $today = str_replace("-","",$day);
                echo 
                "<label class='trainInfo'>
                    <p style='font-weight: 700; margin: 0 auto; display: block;'>Indirect Train</p>
                    <div class='flexBox'>
                        <p>Train 1 : ".$row["id1"]."<br>".$row["t1"]."</p>
                        <p>".$row["src"]." to ".$row["break_stn"]."</p>
                        <p>Start time: ".$row["st1"]." <br>EndTime :".$row["et1"]."</p>
                    </div>
                    <div class='flexBox'>
                        <p>Train 2: ".$row["id2"]."<br>".$row["t2"]."</p>
                        <p>".$row["break_stn"]." to ".$row["dest"]."</p>
                        <p>Start time: ".$row["st2"]." <br>EndTime :".$row["et2"]."</p>
                    </div>
                </label>";
            }
                
        }

        if($b1==0){
            echo "<h1>No Train Found</h1>"; 
        }
        echo"<a href='search.php'>Choose another date</a></div>";
        
    }
    if (isset($_POST['submitTrainQ'])) {
        list_of_trains($conn);
        echo "<script> document.getElementById('checkTrain').style.display='none';
        document.getElementById('selectTrain').style.display=''; </script>";
    }
    ?>
</body>

</html>