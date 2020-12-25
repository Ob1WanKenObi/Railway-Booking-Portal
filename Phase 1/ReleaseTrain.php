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
<div class="User">
        <p>Logged in as : <?php echo $_SESSION["username"]; ?></p>
        <a href="logout.php">Logout</a>
    </div>
    <datalist id="createdTrain">
        <?php
        $result = $conn->query("SELECT DISTINCT TRAINS_CREATED.TNAME FROM TRAINS_CREATED");
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<option value=" . $row["TNAME"] . ">";
            }
        } else {
            echo "No created trains exist.";
        }
        ?>
    </datalist>
    <div class="Release-train" style="height: 300px; width: 800px;">
        <h1>
            Release Train
        </h1>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
            <input list="createdTrain" name="release" placeholder="Choose Train" required>
            <input type="text" min="2020-11-28" name="myDate" placeholder="Choose Date" required onfocus="(this.type='date')" onblur="(this.type='text')">
            <input type="number" name="acnum" placeholder="AC Coaches" required>
            <input type="number" name="slnum" placeholder="SL Coaches" required>
            <input type="submit" name="releaseTrainQ" value="Release">
        </form>
        <?php
        function releaseTrain($conn)
        {
            $tname = $_POST['release'];
            $day = $_POST['myDate'];
            $ac_num = $_POST['acnum'];
            $sl_num = $_POST['slnum'];
            $sql = "SELECT id FROM TRAINS_CREATED WHERE TNAME='$tname'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $id = $row["id"];
            }
            $qurr = "INSERT INTO trains_released (ID, JDATE,AC_NUM,SL_NUM) VALUES($id, '$day', '$ac_num', '$sl_num')";
            $result = $conn->query($qurr);
            if (!$result) {
                echo "Could not insert query to release train. (1.0)";
            }
            $today = str_replace("-", "", $day);
            $q = "CREATE TABLE T" . "$id" . "_" . "$today" . "_ac (seat int AUTO_INCREMENT PRIMARY KEY, name varchar(100), age int, gender varchar(7), pnr varchar(100), agentid int)";
            echo "<br>";
            //echo $q;
            $result = $conn->query($q);
            if (!$result) {
                echo "Could not insert query to release train. (2.0)";
            }
            $q = "CREATE TABLE T" . "$id" . "_" . "$today" . "_sl (seat int AUTO_INCREMENT PRIMARY KEY, name varchar(100), age int, gender varchar(7), pnr varchar(100), agentid int)";
            $result = $conn->query($q);
            if (!$result) {
                echo "Could not insert query to release train. (3.0)";
            }
            else{
                echo "<script>alert('Train $tname added to booking system succesfully');</script>";
            }
        }
        if (isset($_POST['releaseTrainQ'])) {
            releaseTrain($conn);
        }
        ?>
        <a href="adminChoose.php">Choose Another Option</a>
    </div>
    <div class="Release-train" id="notFound" style="display: none; height: 200px; width: 500px;">
        <h1>This Train is not Created, please select a created train</h1>
        <a href="ReleaseTrain.php">Go Back</a>
    </div>

    <div class="Release-train" id="successful" style="display: none;height: 200px;width: 500px;">
        <h1>Released Successfully</h1>
        <a href="AdminChoose.php">Choose Another Option</a>
    </div>

    <script>
        function failed() {
            document.getElementById("release").style.display = "none";
            document.getElementById("notFound").style.display = "";
            document.getElementById("successful").style.display = "none";
        }

        function success() {
            document.getElementById("release").style.display = "none";
            document.getElementById("notFound").style.display = "none";
            document.getElementById("successful").style.display = "";
        }
    </script>


</body>

</html>