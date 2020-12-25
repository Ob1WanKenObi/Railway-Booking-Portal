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
    <link rel="stylesheet" type='text/css' href="./style/AdminChoose.css">
</head>

<body>
<div class="User">
        <p>Logged in as : <?php echo $_SESSION["username"]; ?></p>
        <a href="logout.php">Logout</a>
    </div>
    <div class="box">
        <div class="container">
            <form class="form cf" onsubmit="redirect(event.preventDefault())">
                <section class="plan cf">
                    <h2>Select an option:</h2>
                    <input type="radio" name="radio1" id="free" value="createtrain" ><label class="free-label four col"
                        for="free">Create Train</label>
                    <input type="radio" name="radio1" id="basic" value="releasetrain" checked><label
                        class="basic-label four col" for="basic">Release Train</label>
                    <input type="radio" name="radio1" id="premium" value="canceltrain"><label class="premium-label four col"
                        for="premium">Cancel Train</label>
                </section>
                <input class="submit" type="submit" value="Submit">		
            </form>
        </div>
    </div>
    
    <script>
    function redirect(){
        let radios = document.getElementsByName("radio1");
        let selected;
        for(let i=0;i<radios.length;i++){
            if(radios[i].checked){
                selected=radios[i].value;
                break;
            }
        }
        
        let url = selected+".php";
        window.location.href = url;
    }
    </script>
</body>

</html>