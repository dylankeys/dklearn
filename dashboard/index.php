<?php
	session_start();
	include("../config.php");
	include("../lib.php");
?>
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
        $userID=$_SESSION["currentUserID"];
		
		if (!isset($_SESSION["currentUserID"]))
        {
            redirect("../login/");
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
	
    <title><?php echo $sitename;?> | Dashboard</title>
	
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
			  <li class="nav-item active">
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

      <div class="container">
	  
		<br>
		<h1>Enrolled courses</h1>

		<!-- Table -->
		<table class="table">

			<tr><th style="text-align:left;width:150px">Course</th><th style="text-align:left;max-width:500px">Description</th><th>Progress</th></tr>
			<?php

				$dbQuery=$db->prepare("select * from enrolments inner join courses on enrolments.courseid = courses.id where enrolments.userid=:id");
				$dbParams=array('id'=>$userID);
				$dbQuery->execute($dbParams);

				while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC))
				{
					$courseId=$dbRow["courseid"];
					$title=$dbRow["title"];
					$description=$dbRow["description"];

					echo "<tr> <td><a class='a' href='../course/view.php?id=".$courseId."'>".$title."</a></td> <td>".$description."</td><td><div class='progress'><div class='progress-bar bg-warning' role='progressbar' style='width: 50%' aria-valuenow='50' aria-valuemin='0' aria-valuemax='100'></div></div></td></tr>";
					//echo "";
				}
			?>

		</table>
		
		<br>
		

			<?php

				$dbQuery=$db->prepare("select courses.id, courses.title, courses.description from course_completions inner join courses on course_completions.courseid = courses.id where course_completions.userid=:id");
				$dbParams=array('id'=>$userID);
				$dbQuery->execute($dbParams);
				$rows = $dbQuery->rowCount();
				
				if ($rows > 0)
				{
					echo '<h1>Completed courses</h1>
						<table class="table">';
				
					echo '<tr><th style="text-align:left;width:150px">Course</th><th style="text-align:left;max-width:500px">Description</th><th>Progress</th></tr>';

					while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC))
					{
						$courseId=$dbRow["id"];
						$title=$dbRow["title"];
						$description=$dbRow["description"];

						echo "<tr> <td><a class='a' href='../course/view.php?id=".$courseId."'>".$title."</a></td> <td>".$description."</td><td><div class='progress'><div class='progress-bar bg-success' role='progressbar' style='width: 100%' aria-valuenow='100' aria-valuemin='0' aria-valuemax='100'></div></div></td></tr>";
					}
					
					echo '</table><br>
							<button type="button" class="btn btn-primary btn-lg btn-block" onclick="window.location.href=\'learningrecord/generate.php?userid='.$userID.'\'">View learning record</button><br>';
				}
			?>
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
