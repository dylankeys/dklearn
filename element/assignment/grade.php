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
		
		if(!has_capability("course:admin",$userID))
		{
			echo "<script>window.location.href = '../../course/index.php?permission=0'</script>";
		}

		if (isset($_GET["id"]) && isset($_GET["assignid"]))
		{
			if($_GET["id"]==null || $_GET["assignid"]==null)
			{
				echo "<script>window.location.href = '../../course/index.php?course=noid'</script>";
			}
			$id = $_GET["id"];
			$assignid = $_GET["assignid"];

			$dbQuery=$db->prepare("select users.fullname from assignment_grades INNER JOIN users ON assignment_grades.userid = users.id where assignment_grades.id=:id");
			$dbParams = array('id'=>$id);
			$dbQuery->execute($dbParams);

			while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC))
			{
				$studentfullname=$dbRow["fullname"];

				echo "<title>".$studentfullname." | Grading </title>";
			}
		}
		else if (isset($_POST["gradeid"]))
		{
			$gradeid=$_POST["gradeid"];
			$assignid=$_POST["assignid"];
			$grade=$_POST["grade"];
			$feedback=$_POST["feedback"];
			
			$dbQuery=$db->prepare("update assignment_grades set isgraded='1', grade=:grade, feedback=:feedback where id=:id");
			$dbParams = array('id'=>$gradeid, 'grade'=>$grade, 'feedback'=>$feedback);
			$dbQuery->execute($dbParams);
			
			redirect("view.php?id=" . $assignid);
		}
		else
		{
			echo "<script>window.location.href = '../../course/index.php?course=noid'</script>";
		}
		
		$dbQuery=$db->prepare("select * from users where id=:id");
        $dbParams = array('id'=>$userID);
        $dbQuery->execute($dbParams);
        //$dbRow=$dbQuery->fetch(PDO::FETCH_ASSOC);

        while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC))
        {
		   $userid=$dbRow["id"];
		   $username=$dbRow["username"];
		   $fullname=$dbRow["fullname"];
		   $profileimage=$dbRow["profileimage"];
		}
		
	?>
	
	<!--DK CSS-->
	<link href="../../styles.css" rel="stylesheet">
	
	</head>

	<body>
		<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: <?php echo $theme;?>;">
		<!--<nav class="navbar navbar-expand-lg navbar-light bg-light">-->
		  <a class="navbar-brand" href="../../index.php"><?php echo $sitename;?></a>
		  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		  </button>
		  <div class="collapse navbar-collapse" id="navbarText">
			<ul class="navbar-nav mr-auto">
			  <li class="nav-item">
				<a class="nav-link" href="../../">Home</a>
			  </li>
			  <li class="nav-item active">
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
			  <li class="nav-item">
				<?php if (has_capability("site:config",$userID)) { echo '<a class="nav-link" href="../../settings/">Administration</a>'; } ?>
			  </li>
			</ul>
			<span class="navbar-text">
			
			  <?php
				if (isset($username)) {
					echo "<img src='".$profileimage."' width='28px' alt='Profile Image' class='rounded-circle'>&nbsp;<a href='../../profile/'>".$fullname."(<a href='../../profile/killSession.php'>Log out</a>)";
				}
				else {
					echo "<a href='../../login/'>Log in or sign up</a>";
				}
			  ?>
			</span>
		  </div>
		</nav>
		<br>

      <div class="container">
		
		<h1>Grading: <?php echo $studentfullname; ?></h1>
		<br>
		
		<form method="post" action="grade.php">
			<div class="form-group">
				<label for="grade">Grade</label>
				<input type="number" class="form-control" id="grade" name="grade" placeholder="%">
			</div>

			<div class="form-group">
				<label for="feedback">Feedback</label>
				<textarea class="form-control" id="feedback" name="feedback"></textarea>
			</div>
			
			<input type="hidden" name="gradeid" value="<?php echo $id; ?>">
			<input type="hidden" name="assignid" value="<?php echo $assignid; ?>">
		  
			<button type="submit" class="btn btn-primary">Grade</button>
		</form>
		
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
