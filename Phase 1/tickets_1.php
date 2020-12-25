<html>

<head>
    <title>Ticket</title>
    <link rel="stylesheet" type='text/css' href="./style/Ticket.css">
</head>

<body>
    <?php
    // Initialize the session
    session_start();
    // Check if the user is logged in, if not then redirect him to login page
    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
        header("location: agentlogin.php");
        exit;
    }
    $server = "localhost";
    $username = "root";
    $password = "";
    $database = "test";
    $conn = mysqli_connect($server, $username, $password, $database);
    if (!$conn) {
        die("connection to server failed");
    }
    ?>

    <?php
    $today = str_replace("-", "", $_SESSION['jdate']);
    $tablename = "t" . $_SESSION['tbb'] . "_" . $today . "_" . $_SESSION['seattype'];
    $i = 0;
    //generate pnr as 1stseatbooked_tablename
    $sql = "SELECT COUNT(*) FROM $tablename";
    $result = $conn->query($sql);
    $seattbb = 0;
    if (!empty($result) && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $seattbb = $row["COUNT(*)"] + 1;
    }
    $pnr = "T" . $_SESSION['seattype'] . $_SESSION['tbb'] . "_" . $today . "_" . "$seattbb";
    $tid = $_SESSION['tbb'];
    while (isset($_POST['name'][$i])) {
        $tempname = $_POST['name'][$i];
        $tempage = $_POST['age'][$i];
        $tempgender = $_POST['gender'][$i];
        $agentid = $_SESSION['id'];
        $i++;
        $sql = "INSERT INTO $tablename (NAME,AGE,GENDER,PNR,AGENTID) VALUES('$tempname', '$tempage', '$tempgender', '$pnr', '$agentid')";
        $result = $conn->query($sql);
        if (!$result) {
            echo "Could not insert query to insert passenger details.";
        }
    }
    $sql = "SELECT start_pt,end_pt,start_time,end_time FROM `trains_created` WHERE id='$tid'";
    $result = $conn->query($sql);
    if (!empty($result) && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $start_pt = $row["start_pt"];
        $end_pt = $row["end_pt"];
        $start_time = $row["start_time"];
        $end_time = $row["end_time"];
    }

    ///all essential info variables:
    /*
    $tid=train id
    $_SESSION['tname']=train name;
    $_SESSION['jdate']=journey date in format yyyy-mm-dd;
    $_SESSION['id']=agent id;
    $pnr=PNR
    $start_pt;
    $end_pt;
    $start_time;
    $end_time;
    There is a while loop below. It basically loops through the list of people just inserted, and displays the details.
    Add html accordingly.
    */
    ?>
    <!-- insert the below php snip where where you want to start displaying the passenger details,,
     u can echo a <div> as a string outside the while loop if u wanna give it a class-->
    <div class="User">
        <p>Logged in as : <?php echo $_SESSION["username"]; ?></p>
        <a href="logout.php">Logout</a>
    </div>
    <div class="Release-train">

        <h1><?php echo $_SESSION['tname'] ?></h1>
        <p class="alignCenter">Train Number:<?php echo $tid ?></p>
        <p class="alignCenter">Journey Date:<?php echo $_SESSION['jdate'] ?></p>
        <div class="flexBox">
            <p>Boarding: <?php echo $start_pt ?></p>
            <p>Destination: <?php echo $end_pt ?></p>
        </div>
        <div class="flexBox">
            <p>Start Time: <?php echo $start_time ?></p>
            <p>End Time: <?php echo $end_time ?></p>
        </div>
        <h1 style="padding:10px;">PNR:<?php echo $pnr ?></h1>
        <?php
        $tempid = $_SESSION['id'];
        $sql = "select name from agents where id=$tempid";
        $result = $conn->query($sql);
        if (!empty($result) && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $agentname = $row["name"];
        }
        ?>
        <h1 style="padding: 7px;">Booked by: <?php echo $agentname ?></h1>
        <div class="flexBox">
            <table class="content-table">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Name</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Coach Number</th>
                        <th>Class</th>
                        <th>Seat Number</th>
                        <th>Seat Type</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    $sql = "SELECT seat,name,age,gender FROM $tablename WHERE PNR='$pnr'";
                    $result = $conn->query($sql);
                    if (!empty($result) && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $cNum;
                            $sNum;
                            $sType;
                            if ($_SESSION['seattype'] == 'ac') {
                                $cNum = ceil($row['seat'] / 18);
                                $sNum = $row['seat'] % 18;
                                if ($sNum % 6 == 1 || $sNum % 6 == 2) {
                                    $sType = "LB";
                                } else if ($sNum % 6 == 3 || $sNum % 6 == 4) {
                                    $sType = "UB";
                                } else if ($sNum % 6 == 5) {
                                    $sType = "SL";
                                } else if ($sNum % 6 == 0) {
                                    $sType = "SU";
                                }
                            } else {
                                $cNum = ceil($row['seat'] / 24);
                                $sNum = $row['seat'] % 24;
                                if ($sNum % 8 == 1 || $sNum % 8 == 4) {
                                    $sType = "LB";
                                } else if ($sNum % 8 == 2 || $sNum % 8 == 5) {
                                    $sType = "MB";
                                } else if ($sNum % 8 == 3 || $sNum % 8 == 6) {
                                    $sType = "UB";
                                } else if ($sNum % 8 == 7) {
                                    $sType = "SL";
                                } else {
                                    $sType = "SU";
                                }
                            }

                            echo "<tr class='active-row'><td>$i</td>";
                            echo "<td>" . $row['name'] . "</td>";
                            echo "<td>" . $row['age'] . "</td>";
                            echo "<td>" . $row['gender'] . "</td>";
                            echo "<td>" . $cNum . "</td>";
                            echo "<td>" . $_SESSION['seattype'] . "</td>";
                            echo "<td>" . $sNum . "</td>";
                            echo "<td>" . $sType . "</td></tr>";
                            $i++;
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>