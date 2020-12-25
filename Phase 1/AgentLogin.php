<html>

<head>
    <link rel="stylesheet" type='text/css' href="style/LoginStyle.css">
</head>
<?php
    // Initialize the session
    session_start();

    // Check if the user is already logged in, if yes then redirect him to welcome page
    if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
        header("location: bookinglayout.php");
        exit;
    }

    // Include config file
    $server = "localhost";
    $username = "root";
    $password = "";
    $database = "test";
    $conn = mysqli_connect($server, $username, $password, $database);
    if (!$conn) {
        die("connection to server failed");
    }

    // Define variables and initialize with empty values
    $username = $password = "";
    $username_err = $password_err = "";

    // Processing form data when form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Check if username is empty
        if (empty(trim($_POST["username"]))) {
            $username_err = "Please enter username.";
        } else {
            $username = trim($_POST["username"]);
        }

        if($username=="admin"){
            echo "<script> alert(Please use Admin login portal to log in);</script>";
        }

        // Check if password is empty
        if (empty(trim($_POST["password"]))) {
            $password_err = "Please enter your password.";
        } else {
            $password = trim($_POST["password"]);
        }

        // Validate credentials
        if (empty($username_err) && empty($password_err)) {
            // Prepare a select statement
            $sql = "SELECT id, username, password FROM agents WHERE username = ?";

            if ($stmt = mysqli_prepare($conn, $sql)) {
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "s", $param_username);

                // Set parameters
                $param_username = $username;

                // Attempt to execute the prepared statement
                if (mysqli_stmt_execute($stmt)) {
                    // Store result
                    mysqli_stmt_store_result($stmt);

                    // Check if username exists, if yes then verify password
                    if (mysqli_stmt_num_rows($stmt) == 1) {
                        // Bind result variables
                        mysqli_stmt_bind_result($stmt, $id, $username, $db_pass);
                        if (mysqli_stmt_fetch($stmt)) {
                            if ($password == $db_pass) {
                                // Password is correct, so start a new session
                                session_start();
                                
                                // Store data in session variables
                                $_SESSION["loggedin"] = true;
                                $_SESSION["id"] = $id;
                                $_SESSION["username"] = $username;

                                // Redirect user to welcome page
                                header("location: bookinglayout.php");
                            } else {
                                // Display an error message if password is not valid
                                $password_err = "The password you entered was not valid.";
                                echo $password_err;
                            }
                        }
                    } else {
                        // Display an error message if username doesn't exist
                        $username_err = "No account found with that username.";
                        echo $username_err;
                    }
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }

                // Close statement
                mysqli_stmt_close($stmt);
            }
        }

        // Close connection
        mysqli_close($conn);
    }
    ?>
<body>
    <title>Login Portal</title>
    <script>  
            function validation()  
            {  
                var id=document.loginform.username.value;  
                var ps=document.loginform.password.value;  
                if(id.length=="" && ps.length=="") {  
                    alert("User Name and Password fields are empty");  
                    return false;  
                }  
                else  
                {  
                    if(id.length=="") {  
                        alert("User Name is empty");  
                        return false;  
                    }   
                    if (ps.length=="") {  
                    alert("Password field is empty");  
                    return false;  
                    }  
                }                             
            }  
        </script>
    <div class="loginBox">
        <img src="images/avatar.png" class="avatar" alt="Avatar">
        <h1>Login Here</h1>
        <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>" name="loginform">
            <p>Username</p>
            <input type="text" name="username" placeholder="Enter username">
            <p>Password</p>
            <input type="password" name="password" placeholder="Enter Password">
            <input type="submit" name="loginQ" value="Login" onclick="validation()">
            <a href="signup.php"><strong> Sign Up as Booking Agent </strong> </a><br>
            <a href="adminlogin.php"><strong>Sign In as Admin</strong></a>
            
        </form>

    </div>
    
</body>

</html>