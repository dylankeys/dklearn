<!DOCTYPE html>
<?php
    include("dbConnect.php");
    session_start();
   
    unset($_SESSION["currentUser"]);
    unset($_SESSION["currentUserID"]);

    if (isset($_POST["action"]) && $_POST["action"]=="login")
    {
        $formUser=$_POST["username"];
        $formPass=$_POST["password"];

        $dbQuery=$db->prepare("select * from users where username=:formUser");
        $dbParams = array('formUser'=>$formUser);
        $dbQuery->execute($dbParams);

        $dbRow=$dbQuery->fetch(PDO::FETCH_ASSOC);

        if ($dbRow["username"]==$formUser)
        {
            if ($dbRow["password"]==$formPass)
            {
                $_SESSION["currentUser"]=$formUser;
                $_SESSION["currentUserID"]=$dbRow["id"];
                //header("Location: /profile.php");
                if (isset($_POST["courseid"]))
                {
                    echo "<script>window.location.href = 'course.php?id=". $_POST["courseid"] ."'</script>";
                }
                else
                    {
                    echo "<script>window.location.href = 'profile.php'</script>";
                }

            }
            else
            {
                //header("Location: /login.php?failCode=2");
                echo "<script>window.location.href = 'login.php?failCode=2'</script>";
            }
        }
        else
        {
            //header("Location: /login.php?failCode=1");
            echo "<script>window.location.href = 'login.php?failCode=1'</script>";
        }

    }
    else
    {

?>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    
  <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="images/favicon.ico">

    <title>dklearn</title>
    <!-- Bootstrap core CSS -->
    <link href="dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="assets/js/ie-emulation-modes-warning.js"></script>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>
<body>


<!-- The justified navigation menu is meant for single line per list item.
           Multiple lines will require custom code not provided by Bootstrap. -->
      <div class="masthead" style="background-image: url('images/bg-color.png');background-repeat: repeat-yx; width: 100%;">
    <!--
        <h3 class="text-muted">Project name</h3> -->
    
    <p style="text-align:center; font-size:2.5em"><a class="logo" href="index.php"><b>dk</b>learn</a></p>


    
        <nav>
          <ul class="nav nav-justified">
            <li class="active"><a href="index.php">Home</a></li>
            <li><a href="courses.php">Courses</a></li>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="contact.php">Contact</a></li>
            <li><a href="profile.php">Profile</a></li>
            
      
          </ul>
        </nav>
      </div>

<div class="container">

    <?php
    if (isset($_GET["failCode"])) {
        if ($_GET["failCode"]==1)
            echo "<h3 style='color:red; text-align: center;'>Bad username entered</h3>";
        if ($_GET["failCode"]==2)
            echo "<h3 style='color:red; text-align: center;'>Bad password entered</h3>";
        if ($_GET["failCode"]==3)
        {
            echo "<h3 style='color:red; text-align: center;'>Login or register to enrol on this course</h3>";

            if (isset($_GET["courseid"]))
            {
                $courseid = $_GET["courseid"];
            }
        }

    }
    ?>
    <div style="width:30%; margin-left:auto; margin-right:auto;">
<br>
<?php
/*
	if (!isset($_SESSION["message"]))
	{*/
		echo "<h4>Please log in or register</h4>";
	/*
	}
	
	if(isset($_SESSION["message"]))
	{
		echo "<h4 style='color:green'>You have been sucessfully registered, log in to begin learning</h4>";
	}
*/
?>
   <form name="login" method="post" action="login.php">

	<div id="loginInfo"class="form-group">
    <input type="text" class="form-control" placeholder="username" name="username">
  </div>

  <div id="loginInfo"class="form-group">
    <input type="password" class="form-control" placeholder="password" name="password">
  </div>

       <?php
       if (isset($courseid))
       {
           echo "<input type='hidden' name='courseid' value='". $courseid ."'>";
       }
       ?>

	 <input type="hidden" name="action" value="login">
     <input style="float:right" type="submit" value="Login" class="btn btn-default" role="button">
	 </form>
  <br><br>
   <a class="a" href="register.php"><h4>Don't have an account? Register here</h4></a>

   </div>
   </div>
</body>

</html>

<?php
}
?>
