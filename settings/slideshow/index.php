<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/css/bootstrap.min.css" integrity="sha384-Zug+QiDoJOrZ5t4lssLdxGhVrurbmBWopoEl+M6BdEfwnCJZtKxi1KgxUyJq13dy" crossorigin="anonymous">

	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../images/favicon.ico">

	<?php
		include("../../config.php");
		include("../../lib.php");
		session_start();
        $userID=$_SESSION["currentUserID"];
		
		if(!has_capability("site:config",$userID))
		{
			echo "<script>window.location.href = '../../index.php?permission=0'</script>";
		}
		
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
	
    <title><?php echo $sitename;?> | Slideshow Settings</title>
	
	<!--DK CSS-->
	<link href="../../styles.css" rel="stylesheet">
	
	</head>

	<body>

		<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #1E88FF;">
		<!--<nav class="navbar navbar-expand-lg navbar-light bg-light">-->
		  <a class="navbar-brand" href="../../"><?php echo $sitename;?></a>
		  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		  </button>
		  <div class="collapse navbar-collapse" id="navbarText">
			<ul class="navbar-nav mr-auto">
			  <li class="nav-item">
				<a class="nav-link" href="../../">Home</a>
			  </li>
			  <li class="nav-item">
				<a class="nav-link" href="../../course/">Courses</a>
			  </li>
			  <li class="nav-item">
				<a class="nav-link" href="../../dashboard/">Dashboard</a>
			  </li>
			  <li class="nav-item">
				<a class="nav-link" href="../../contact/">Contact</a>
			  </li>
			  <li class="nav-item">
				<a class="nav-link" href="../../profile/">Profile</a>
			  </li>
			  <li class="nav-item active">
				<?php if (has_capability("site:config",$userID)) { echo '<a class="nav-link" href="../../settings/">Administration</a>'; } ?>
			  </li>
			</ul>
			<span class="navbar-text">
			
			  <?php
				if (isset($username)) {
					echo "<img src='".$profileimage."' width='28px' alt='Profile Image' class='rounded-circle'>&nbsp;<a href='../profile/'>".$fullname." (<a href='../profile/killSession.php'>Log out</a>)</a>";
				}
				else {
					echo "<a href='../../login/'>Log in or sign up</a>";
				}
			  ?>
			</span>
		  </div>
		</nav>

      <div class="container">

         <h1>Slideshow image source URLs</h1>

         <?php

            if (isset($_POST["slideshowImages"]))
            {
				$time=time();
				$slide1 = $_POST["slideOne"];
				$dbQuery=$db->prepare("update config set value=:slideOne, lastmodified=:time where setting='slideone'");
         		$dbParams=array('slideOne'=>$slide1,'time'=>$time);
         		$dbQuery->execute($dbParams);
				
				$slide2 = $_POST["slideTwo"];
				$dbQuery=$db->prepare("update config set value=:slideTwo, lastmodified=:time where setting='slidetwo'");
         		$dbParams=array('slideTwo'=>$slide2,'time'=>$time);
         		$dbQuery->execute($dbParams);
				
				$slide3 = $_POST["slideThree"];
				$dbQuery=$db->prepare("update config set value=:slideThree, lastmodified=:time where setting='slidethree'");
         		$dbParams=array('slideThree'=>$slide3,'time'=>$time);
         		$dbQuery->execute($dbParams);
				
				echo "<script>window.location.href = 'index.php?success=1'</script>";
            }
			
			if(isset($_GET["success"]))
			{
				echo "<h1 style=\"background:green\">Slideshow images updated!<span style='float:right;font-size:20px;'><a href='index.php'>x</a>&nbsp;</span></h1>";
			}

         ?>

         <form action="index.php" method="post">
            
               <?php
                  $dbQuery=$db->prepare("select * from config");
   			      $dbQuery->execute();

      		      while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC)) {
                     $setting = $dbRow["setting"];
					 
					 if ($setting == "slideone") {
						$slideOne = $dbRow["value"];
						echo '<label for="slideOne">Slide one:</label>
								<input type="text" name="slideOne" value="'.$slideOne.'">';
					 }
					 else if ($setting == "slidetwo") {
						$slideTwo = $dbRow["value"];
						echo '<label for="slideTwo">Slide two:</label>
								<input type="text" name="slideTwo" value="'.$slideTwo.'">';
					 }
					 else if ($setting == "slidethree") {
						$slideThree = $dbRow["value"];
						echo '<label for="slideThree">Slide three:</label>
								<input type="text" name="slideThree" value="'.$slideThree.'">';
					 }
                  }
               ?>
            <input type="hidden" name="slideshowImages" />
          <br><input type="submit" />
       </form>

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
