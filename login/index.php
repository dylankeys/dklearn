<?php
	session_start();
    include("../config.php");
	include("../lib.php");

    unset($_SESSION["currentUser"]);
    unset($_SESSION["currentUserID"]);

    if (isset($_POST["action"]) && $_POST["action"]=="login")
    {
        $formUser=$_POST["username"];
        $plainPass=$_POST["password"];
		
		$formPass=md5($plainPass);

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

                if (isset($_POST["courseid"]))
                {
					redirect("../course/view.php?id=". $_POST["courseid"]);
                }
                else
                {
                    redirect("../profile/");
                }

            }
            else
            {
                redirect("../login/index.php?failCode=2");
            }
        }
        else
        {
			redirect("../login/index.php?failCode=1");
        }

    }
    else
    {
?>
<!DOCTYPE html>
<?php
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
	<script defer src="https://use.fontawesome.com/releases/v5.0.8/js/all.js" integrity="sha384-SlE991lGASHoBfWbelyBPLsUlwY1GwNDJo3jSJO04KZ33K2bwfV9YBauFfnzvynJ" crossorigin="anonymous"></script>

	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../images/favicon.ico">
	
    <title><?php echo $sitename;?> | Login</title>
	
	<!--DK CSS-->
	<link href="../styles.css" rel="stylesheet">
	
	</head>

	<body style="background-image: url('../images/loginBG.jpg')">

		<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: <?php echo $theme;?>;">
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
	<div class="login">

    <?php
	
    if (isset($_GET["failCode"])) {
        if ($_GET["failCode"]==1)
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
				<strong>Error!</strong> The username you entered does not match one in our records.
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>';
        if ($_GET["failCode"]==2)
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
				<strong>Error!</strong> The password you entered does not match one in our records.
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>';
        if ($_GET["failCode"]==3)
        {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
				<strong>Error!</strong> You must be logged in to enrol on this course, please login or register.
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>';

            if (isset($_GET["courseid"]))
            {
                $courseid = $_GET["courseid"];
            }
        }

    }
	else if(isset($_GET["registered"]))
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
			<div class="card">
				<div class="card-body">
					<h5 class="card-title">Please login or register</h5>
					
					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text" id="user-addon"><i class="fas fa-user-circle"></i></span>
						</div>
						<input type="text" class="form-control" placeholder="Username" name="username" aria-describedby="user-addon">
					</div>
					
					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<span class="input-group-text" id="pw-addon"><i class="fas fa-key"></i></span>
						</div>
						<input type="password" class="form-control" placeholder="Password" name="password" aria-describedby="pw-addon">
					</div>
					
					<?php
						if (isset($courseid))
						{
							echo "<input type='hidden' name='courseid' value='". $courseid ."'>";
						}
					?>
					
					<input type="hidden" name="action" value="login">
					<input style="float:right" type="submit" value="Login" class="btn btn-primary" role="button">
					
					<?php
						if ($userReg == "1")
						{
							echo '<p class="card-text"><a class="a" href="../register/">Don\'t have an account? Register here</a></p>';
						}
					?>
				</div>
			</div>
		</form>
	</div>

   </div>
   </div>
   
   <footer>
		<p class="copyright"><?php echo $sitename ." | &copy ". date("Y"); ?></p>
		<ul class="v-links">
			<li><a href="../">Home</a></li>
			<li><a href="../course">Courses</a></li>
			<li><a href="../dashboard">Dashboard</a></li>
			<li><a href="../contact">Contact</a></li>
			<li><a href="../profile">Profile</a></li>
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