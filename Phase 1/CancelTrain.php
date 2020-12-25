<html>
<?php
// Initialize the session
session_start();
// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["username"] != "admin") {
    header("location: adminlogin.php");
    exit;
}
?>
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

<head>
    <link rel="stylesheet" type='text/css' href="./style/ReleaseTrain.css">
</head>

<body>
    <datalist id="releasedTrain">
        <?php
        $result = $conn->query("SELECT DISTINCT TRAINS_CREATED.TNAME FROM TRAINS_RELEASED,TRAINS_CREATED WHERE TRAINS_RELEASED.ID=TRAINS_CREATED.ID");
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<option value=" . $row["TNAME"] . ">";
            }
        } else {
            echo "No released trains exist.";
        }
        ?>
    </datalist>
    <div class="Release-train" style="height: 300px; width: 800px;" id="cancel">
        <h1>
            Cancel Train
        </h1>
        <?php
        function delTrain($conn)
        {
            $tname = $_POST['cancel'];
            $day = $_POST['myDate'];
            $today = str_replace("-", "", $day);
            $sql = "SELECT id FROM TRAINS_CREATED WHERE TNAME='$tname'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $tid = $row["id"];
            }
            $qurr = "DELETE FROM trains_released WHERE JDATE='$day' AND ID=$tid";
            $result = $conn->query($qurr);
            $tablename_ac="t".$tid."_".$today."_ac";
            $tablename_sl="t".$tid."_".$today."_sl";
            $qurr = "DROP TABLE $tablename_ac";
            $result = $conn->query($qurr);
            $qurr = "DROP TABLE $tablename_sl";
            $result = $conn->query($qurr);
            if(!$result){
                echo "Could not process query to delete.";
            }
            else{
                echo "<script>alert('Train $tname removed from booking system succesfully');</script>";
            }
        }
        if (isset($_POST['cancelTrainQ'])) {
            delTrain($conn);
        }
        ?>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
            <input list="releasedTrain" name="cancel" placeholder="Choose Train" required>
            <input type="text" name="myDate" min="2020-11-28" placeholder="Choose Date" required onfocus="(this.type='date')" onblur="(this.type='text')">
            <input type="submit" name="cancelTrainQ" value="Cancel Train">
        </form>
        <a href="AdminChoose.php">Choose Another Option</a>

    </div>
    <div class="Release-train" id="notFound" style="display: none; height: 200px; width: 500px;">
        <h1>This Train is not Released, please select a released train</h1>
        <a href="CancelTrain.php">Go Back</a>
    </div>

    <div class="Release-train" id="successful" style="display: none;height: 200px;width: 500px;">
        <h1>Cancellation Successful</h1>
        <a href="AdminChoose.php">Choose Another Option</a>
    </div>

    <script>
        function failed() {
            document.getElementById("cancel").style.display = "none";
            document.getElementById("notFound").style.display = "";
            document.getElementById("successful").style.display = "none";
        }

        function success() {
            document.getElementById("cancel").style.display = "none";
            document.getElementById("notFound").style.display = "none";
            document.getElementById("successful").style.display = "";
        }
    </script>
</body>

</html>