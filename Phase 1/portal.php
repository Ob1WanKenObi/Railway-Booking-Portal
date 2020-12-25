<html>
<?php
// Initialize the session
session_start();
// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: agentlogin.php");
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
    <title>Booking Portal</title>
    <link rel="stylesheet" type='text/css' href="./style/Portal.css">
</head>

<body>
    <div class="User">
        <p>Logged in as : <?php echo $_SESSION["username"]; ?></p>
        <a href="logout.php">Logout</a>
    </div>
    <?php
    $tid = strtok($_SESSION['mytbb'], "_");
    $jdate_tbf = strtok("_");
    //echo $tid."<br>".$jdate_tbf;
    $a = substr($jdate_tbf, 0, 4);
    $d = substr($jdate_tbf, 4, 2);
    $c = substr($jdate_tbf, 6, 2);
    $jdate = $a . "-" . $d . "-" . $c;
    $sql = "SELECT trains_created.TNAME as tname,trains_released.AC_NUM as ac_num,trains_released.SL_NUM as sl_num 
            FROM trains_released,trains_created 
            WHERE TRAINS_RELEASED.ID=TRAINS_CREATED.ID and trains_released.ID='$tid' AND trains_released.JDATE='$jdate'";
    $result = $conn->query($sql);
    if (!empty($result) && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $tname = $row["tname"];
        $ac_num = $row["ac_num"];
        $sl_num = $row["sl_num"];
    }
    $ac_seats_c = $ac_num * 18;
    $sl_seats_c = $sl_num * 24;

    $sql_ac = "SELECT COUNT(*) FROM t" . $_SESSION['mytbb'] . "_ac";
    $sql_sl = "SELECT COUNT(*) FROM t" . $_SESSION['mytbb'] . "_sl";
    $ac_seats_b = 0;
    $sl_seats_b = 0;
    $result = $conn->query($sql_ac);
    if (!empty($result) && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $ac_seats_b = $row["COUNT(*)"];
    }
    $result = $conn->query($sql_sl);
    if (!empty($result) && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $sl_seats_b = $row["COUNT(*)"];
    }

    $ac_vac = $ac_seats_c - $ac_seats_b;
    $sl_vac = $sl_seats_c - $sl_seats_b;
    ?>
    <datalist id="gender">
        <option value="Male">
        <option value="Female">
    </datalist>
    <div class="initialForm" id="initialForm">
        <p><?php echo $tid . " " . $tname; ?></p>
        <form name="myform" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <label>
                <input type="radio" value="AC" name="seat_type" checked>
                AC
            </label>
            <label>
                <input type="radio" value="SL" name="seat_type">
                Non-AC
            </label>
            <input type="number" id="numOfSeats" name="numOfSeats" min="1" placeholder="Number of Seats" required>
            <input type="submit" value="Check Availability" name="checkSeat" onclick="submitForm()">
        </form>
    </div>
    <div class="nameForm" id="nameForm" style="display:none;">

    </div>
    <script>
        <?php
        function submitForm($ac_vac, $sl_vac)
        {
            $reqSeats = $_REQUEST["numOfSeats"];
            $seatType = $_REQUEST["seat_type"];

            if (!$reqSeats) {
                return;
            }

            if ($seatType == "AC") {
                $numOfSeats = $ac_vac; //ac seats from php
            } else {
                $numOfSeats = $sl_vac; //sleeper seats from php
            }
            if ($numOfSeats < $reqSeats) {
                echo "alert('Only ' + $numOfSeats + ' are Available');";
            } else {
                //echo "alert('helo');";
                echo "document.getElementById('initialForm').style.display = 'none';  document.getElementById('nameForm').style.display = '';";
                $formString = '<h1>Enter information</h1> <form method="post" action="tickets_1.php">';
                $numForms = '';
                for ($i = 0; $i < $reqSeats; $i++) {
                    $j = $i + 1;
                    $numForms = $numForms . 'Passenger ' . $j . "<br><br>" . '<input type="text" placeholder="Name" name="name[]" required> <input type="number" placeholder="Age" name="age[]" required min="0"> <input list="gender" placeholder="Gender" name="gender[]"required> <br/> <br/>';
                }
                $formString = $formString . $numForms . '<input type="submit" value="Book Ticket"></form> <a onclick="window.location.reload()" style="padding-bottom:20px;"><strong>Change Seat Type</a>';
                echo "document.getElementById('nameForm').innerHTML = '$formString';";
            }
        }
        if (isset($_REQUEST['checkSeat'])) {
            $_SESSION['seattype'] = $_REQUEST['seat_type'];
            $_SESSION['tbb'] = $tid;
            $_SESSION['tname'] = $tname;
            $_SESSION['jdate'] = $jdate;
            submitForm($ac_vac, $sl_vac);
        }
        ?>
    </script>
</body>

</html>