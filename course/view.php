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
    <link rel="icon" href="../images/favicon.ico">

	<?php
		include("../config.php");
		include("../lib.php");
		session_start();
		
		$userID=$_SESSION["currentUserID"];

			if (isset($_GET["id"]))
			{
                if($_GET["id"]==null)
                {
                    echo "<script>window.location.href = 'index.php?course=noid'</script>";
                }
				$id = $_GET["id"];

                if (!isset($_SESSION["currentUserID"]))
                {
                    //header("Location: login.php?failCode=3");
                    echo "<script>window.location.href = '../login/index.php?failCode=3&courseid=". $id ."'</script>";
                }

				$dbQuery=$db->prepare("select * from courses where id=:id");
				$dbParams=array('id'=>$id);
				$dbQuery->execute($dbParams);

				while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC))
				{
					$title=$dbRow["title"];

					echo "<title>".$sitename." | $title</title>";
				}
			}
			else
			{
				echo "<script>window.location.href = 'index.php?course=noid'</script>";
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
			  <li class="nav-item active">
				<a class="nav-link" href="../course/">Courses</a>
			  </li>
			  <li class="nav-item">
				<a class="nav-link" href="../dashboard/">Dashboard</a>
			  </li>
			  <li class="nav-item">
				<a class="nav-link" href="../contact/">Contact</a>
			  </li>
			  <li class="nav-item">
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
		<br>

      <div class="container">
		<?php

			$id = $_GET["id"];

			$dbQuery=$db->prepare("select * from courses where id=:id");
			$dbParams=array('id'=>$id);
			$dbQuery->execute($dbParams);

   			while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC)) {
                $title = $dbRow["title"];
                $description = $dbRow["description"];
                $usemedia = $dbRow["usemedia"];
                $media = $dbRow["media"];
				
                echo "<h1>$title</h1>";

                $dbQuery2 = $db->prepare("select * from enrolments where userID=:userID AND courseID=:courseID");
                $dbParams2 = array('userID' => $userID, 'courseID' => $id);
                $dbQuery2->execute($dbParams2);
                $rows = $dbQuery2->rowCount();

                if ($rows>0 || has_capability("course:admin",$userID)) {
					
					if (!has_capability("course:admin",$userID)) {
						echo "<form style='float:right;padding:10px;' name='enrol' method='post' action='unenrol.php'>";
							echo '<input type="hidden" name="courseID" value="' . $id . '">';
							echo '<input type="hidden" name="userID" value="' . $userID . '">';
							echo '<input type="submit" value="Unenrol" class="btn btn-default" role="button">';
						echo "</form>";
					}
					echo "<div class='course-desc'>$description</div>";
					
					//course content
					
                } else {
					echo "<div class='course-desc'>$description";
					
                    echo "<form name='enrol' method='post' action='enrol.php'>";
                    echo '<input type="hidden" name="courseID" value="' . $id . '">';
                    echo '<input type="hidden" name="userID" value="' . $userID . '">';
                    echo '<br><p style="color:red"><b>You are not enroled on this course!</b></p>';
                    echo '<p>If you\'d like to access this course, please enrol using the button below</p><br>';
                    echo '<input type="submit" value="Enrol" class="btn btn-default" role="button">';
                    echo "</form>";
					
					echo "</div>";
                }
            }

		?>
	  </div>
		<br>
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
