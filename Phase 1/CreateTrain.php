<html>
<?php
// Initialize the session
session_start();
// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["username"] !="admin") {
    header("location: adminlogin.php");
    exit;
}
?>
<head>
    <link rel="stylesheet" href="style/CreateTrain.css">
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
<div class="User">
        <p>Logged in as : <?php echo $_SESSION["username"]; ?></p>
        <a href="logout.php">Logout</a>
    </div>
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
            <h3>Either select an existing station or create a new station.</h3>
            <input list="Stations" name="startPt" placeholder="Starting Station" required>
            <input list="Stations" name="endPt" placeholder="Destination Station" required>
            <input type="text" name="id" placeholder="Train ID" required>
            <input type="text" name="name" placeholder="Train Name" required>
            <h3>Select departure and arrival time:</h3>
            <input type="text" name="start_t" onfocus="(this.type='time')" onblur="(this.type='text')" placeholder="Start Time">
            <input type="text" name="end_t" onfocus="(this.type='time')" onblur="(this.type='text')" placeholder="End Time">
            <input type="submit" name="submitTrainQ" value="Create">
        </form>
        <a href="AdminChoose.php">Choose Another Option</a>


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
        
        $qurr = "INSERT INTO trains_created (ID, TNAME, START_PT, END_PT, START_TIME, END_TIME) VALUES($id, '$name', '$start', '$end', '$starttime', '$endtime')";
                
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