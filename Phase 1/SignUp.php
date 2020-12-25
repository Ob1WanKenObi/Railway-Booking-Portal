<html>

<head>
    <title>SignUp Portal</title>
    <link rel="stylesheet" type='text/css' href="./style/SignUpStyle.css">

<body>
    <?php
    $server = "localhost";
    $username = "root";
    $password = "";
    $database = "test";
    $conn = mysqli_connect($server, $username, $password, $database);
    if (!$conn) {
        die("conn to server failed");
    }
    // Define variables and initialize with empty values
    $username = $password = $confirm_password = $name = $cc = $add = "";
    $username_err = $password_err = $confirm_password_err = $name_err = $cc_err = $add_err = "";

    // Processing form data when form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Validate username
        if (empty(trim($_POST["username"]))) {
            $username_err = "Please enter a username.";
        } else {
            // Prepare a select statement
            $sql = "SELECT id FROM agents WHERE username = ?";

            if ($stmt = mysqli_prepare($conn, $sql)) {
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "s", $param_username);

                // Set parameters
                $param_username = trim($_POST["username"]);

                // Attempt to execute the prepared statement
                if (mysqli_stmt_execute($stmt)) {
                    /* store result */
                    mysqli_stmt_store_result($stmt);

                    if (mysqli_stmt_num_rows($stmt) == 1) {
                        $username_err = "This username is already taken.";
                        echo "<script>alert('$username_err');</script>";
                    } else {
                        $username = trim($_POST["username"]);
                    }
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }

                // Close statement
                mysqli_stmt_close($stmt);
            }
        }

        // Validate password
        if (empty(trim($_POST["password"]))) {
            $password_err = "Please enter a password.";
        } elseif (strlen(trim($_POST["password"])) < 6) {
            $password_err = "Password must have atleast 6 characters.";
            echo "<script>alert('$password_err');</script>";
        } else {
            $password = trim($_POST["password"]);
        }

        // Validate confirm password
        if (empty(trim($_POST["confirm_password"]))) {
            $confirm_password_err = "Please confirm password.";
        } else {
            $confirm_password = trim($_POST["confirm_password"]);
            if (empty($password_err) && ($password != $confirm_password)) {
                $confirm_password_err = "Password did not match.";
                echo "<script>alert('$confirm_password_err');</script>";
            }
        }

        // Validate name
        if (empty(trim($_POST["name"]))) {
            $name_err = "Please enter a name.";
        } else {
            $name = trim($_POST["name"]);
        }

        // Validate cc
        if (empty(trim($_POST["cc"]))) {
            $cc_err = "Please enter a credit card number.";
        } elseif (strlen(trim($_POST["cc"])) < 25) {
            $cc_err = "Credit card number must have atleast 25 characters.";
            echo "<script>alert('$cc_err');</script>";
        } else {
            $cc = trim($_POST["cc"]);
        }

        // Validate address
        if (empty(trim($_POST["add"]))) {
            $add_err = "Please enter an address.";
        } else {
            $add = trim($_POST["add"]);
        }

        // Check input errors before inserting in database
        if (empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($name_err) && empty($cc_err) && empty($add_err)) {

            // Prepare an insert statement
            $sql = "INSERT INTO agents (username, name, cc_num, password, address) VALUES (?, ?, ?, ?, ?)";

            if ($stmt = mysqli_prepare($conn, $sql)) {
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "ssiss", $username,$name, $cc, $password, $add);

                // Attempt to execute the prepared statement
                if (mysqli_stmt_execute($stmt)) {
                    // Redirect to login page
                    header("location: agentlogin.php");
                } else {
                    echo "Something went wrong. Please try again later.";
                }

                // Close statement
                mysqli_stmt_close($stmt);
            }
        }

        // Close connection
        mysqli_close($conn);
    }
    ?>
    <div class="loginBox">
        <img src="./images/avatar.png" class="avatar" alt="Avatar">
        <h1>Sign Up</h1>
        <form id="signUp" method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
            <p>Username</p>
            <input type="text" name="username" placeholder="Enter Username" required>
            <p>Password</p>
            <input type="password" name="password" placeholder="Enter Password" required>
            <p>Confirm Password</p>
            <input type="password" name="confirm_password" placeholder="Enter Password Again" required>
            <p>Name</p>
            <input type="text" name="name" placeholder="Enter Your Name" required>
            <p>Credit Card Number</p>
            <input type="number" name="cc" placeholder="Enter Card Number" required>
            <textarea form="signUp" name="add" placeholder="Enter Address" required></textarea>
            <input type="submit" name="" value="Sign Up">
        </form>
    </div>
    <div class="linklol">
        <a href="agentlogin.php"><strong> Sign In as Booking Agent </strong> </a><br>
        <a href="adminlogin.php"><strong>Sign In as Admin</strong></a>
    </div>


</body>
</head>

</html>