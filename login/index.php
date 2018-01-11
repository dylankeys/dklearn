<?php
	session_start();
    include("../config.php");
	include("../lib.php");

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
				
				$lastlogin=time();
				$dbQuery=$db->prepare("update users set lastlogin=:lastlogin where id=:userid");
				$dbParams=array('lastlogin'=>$lastlogin, 'userid'=>$_SESSION["currentUserID"]);
				$dbQuery->execute($dbParams);

                //header("Location: /profile.php");
                if (isset($_POST["courseid"]))
                {
                    echo "<script>window.location.href = '../course/view.php?id=". $_POST["courseid"] ."'</script>";
                }
                else
                {
                    echo "<script>window.location.href = '../profile'</script>";
                }

            }
            else
            {
                //header("Location: /login.php?failCode=2");
                echo "<script>window.location.href = '../login/index.php?failCode=2'</script>";
            }
        }
        else
        {
            //header("Location: /login.php?failCode=1");
            echo "<script>window.location.href = '../login/index.php?failCode=1'</script>";
        }

    }
    else
    {
		
?>
<!DOCTYPE html>
<?php
		session_start();
        $userID=$_SESSION["currentUserID"];
		
		$dbQuery=$db->prepare("select * from users where id=:id");
        $dbParams = array('id'=>$userID);
        $dbQuery->execute($dbParams);
        //$dbRow=$dbQuery->fetch(PDO::FETCH_ASSOC);

        while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC))
        {
           $username=$dbRow["username"];
		   $fullname=$dbRow["fullname"];
		   $profileimage=$dbRow["profileimage"];
        }
?>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/css/bootstrap.min.css" integrity="sha384-Zug+QiDoJOrZ5t4lssLdxGhVrurbmBWopoEl+M6BdEfwnCJZtKxi1KgxUyJq13dy" crossorigin="anonymous">

	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../images/favicon.ico">
	
    <title><?php echo $sitename;?> | Login</title>
	
	<!--DK CSS-->
	<link href="../styles.css" rel="stylesheet">
	
	</head>

	<body>

		<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #1E88FF;">
		<!--<nav class="navbar navbar-expand-lg navbar-light bg-light">-->
		  <a class="navbar-brand" href="../index.php"><?php echo $sitename;?></a>
		  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		  </button>
		  <div class="collapse navbar-collapse" id="navbarText">
			<ul class="navbar-nav mr-auto">
			  <li class="nav-item">
				<a class="nav-link" href="../">Home</a>
			  </li>
			  <li class="nav-item">
				<a class="nav-link" href="../course/">Courses</a>
			  </li>
			  <li class="nav-item">
				<a class="nav-link" href="../dashboard/">Dashboard</a>
			  </li>
			  <li class="nav-item">
				<a class="nav-link" href="../contact/">Contact</a>
			  </li>
			  <li class="nav-item active">
				<a class="nav-link" href="../profile/">Profile</a>
			  </li>
			  <li class="nav-item">
				<?php if (has_capability("site:config",$userID)) { echo '<a class="nav-link" href="../settings/">Administration</a>'; } ?>
			  </li>
			</ul>
			<span class="navbar-text">
			
			  <?php
				if (isset($username)) {
					echo "<img src='".$profileimage."' width='28px' alt='Profile Image' class='rounded-circle'>&nbsp;<a href='../profile/'>".$fullname." (<a href='../profile/killSession.php'>Log out</a>)</a>";
				}
				else {
					echo "<a href='../login/'>Log in or sign up</a>";
				}
			  ?>
			</span>
		  </div>
		</nav>

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
    <div style="width:50%; margin-left:auto; margin-right:auto;">
<br>
<?php

	if (!isset($_GET["registered"]))
	{
		echo "<h4>Please log in or register</h4>";
	}

	if(isset($_GET["registered"]))
	{
		echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
				<strong>Successfully registered!</strong> Log in below to access your account and begin learning.
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>';
	}

?>
   <form name="login" method="post" action="index.php">

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
   <a class="a" href="../register/"><h4>Don't have an account? Register here</h4></a>
   <!--<a class="a" href="../register/organisation/"><h4>or set up an organisation here</h4></a>-->

   </div>
   </div>
   
   <footer>
		<p class="copyright"><?php echo $sitename ." | &copy ". date("Y"); ?></p>
		<ul class="v-links">
			<li>Home</li>
			<li>Courses</li>
			<li>Dashboard</li>
			<li>Contact</li>
			<li>Profile</li>
		</ul>
	  </footer>
	  
	    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/js/bootstrap.min.js" integrity="sha384-a5N7Y/aK3qNeh15eJKGWxsqtnX/wWdSZSKp+81YjTmS15nvnvxKHuzaWwXHDli+4" crossorigin="anonymous"></script>
</body>

</html>
<?php
	}
?>