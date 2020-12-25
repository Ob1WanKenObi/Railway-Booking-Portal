<html>
<head>
    <link rel="stylesheet" href="CreateTrain_p2.css">
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

    <div class="Release-train">

        <datalist id="Stations">
            <?php
            $result = $conn->query("SELECT * FROM STATIONS");
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<option value=" . $row["station"] . ">";
                }
            } else {
                echo "No existing stations.";
            }
            ?>
        </datalist>

        <form name="create_train_form" method="post" action='<?php echo $_SERVER["PHP_SELF"]; ?>'>
            <h1 style = "text-align: center">    Release a new train.</h1>
            <input list="Stations" name="startPt" placeholder="Starting Station" required>
            <input list="Stations" name="endPt" placeholder="Destination Station" required>
            <input type="text" name="id" placeholder="Train ID" required>
            <input type="text" name="name" placeholder="Train Name" required>
            <input type="number" name="ac" placeholder="No. of AC Coaches" required min=0>
            <input type="number" name="sl" placeholder="No. of SL Coaches" required min=0>
            <input type="text" name="date" onfocus="(this.type='date')" onblur="(this.type='text')" placeholder="Journey date" required>
            <h3>Select departure and arrival time:</h3>
            <input type="text" name="start_t" onfocus="(this.type='time')" onblur="(this.type='text')" placeholder="Start Time">
            <input type="text" name="end_t" onfocus="(this.type='time')" onblur="(this.type='text')" placeholder="End Time">
            <input type="submit" name="submitTrainQ" value="Create">
        </form>
        <a href = "search.php"> Search for a train </a>
    </div>    
    
    
    <?php
    function add_train($conn)
    {
        
        $name=$_POST["name"];
        $id=$_POST["id"];
        $start = $_POST['startPt'];
        $end = $_POST['endPt'];
        $starttime=$_POST['start_t'];
        $endtime=$_POST['end_t'];
        $day=$_POST["date"];
        $sl=$_POST["sl"];
        $ac=$_POST["ac"];
        
        $qurr = "INSERT INTO trains (ID, TNAME, JDATE, START_PT, END_PT, AC_NUM, SL_NUM, START_TIME, END_TIME) VALUES($id, '$name', '$day', '$start', '$end', '$ac', '$sl', '$starttime', '$endtime')";
                
        $result = $conn->query($qurr);
        if(!$result)
        {
            echo "Could not insert query to release train. (1.0)";
        }
        echo "<script>alert('Train $name added to system');</script>";
    }
    if (isset($_POST['submitTrainQ'])) {
        add_train($conn);
    }
    ?>

</body>

</html>    