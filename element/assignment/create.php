<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/css/bootstrap.min.css" integrity="sha384-Zug+QiDoJOrZ5t4lssLdxGhVrurbmBWopoEl+M6BdEfwnCJZtKxi1KgxUyJq13dy" crossorigin="anonymous">

	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="brief" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../images/favicon.ico">

	<?php
		include("../../config.php");
		include("../../lib.php");
		session_start();
        $userID=$_SESSION["currentUserID"];
		
		if (isset($_POST["submit"]))
		{
			$name = $_POST["name"];
			$brief = $_POST["brief"];
			$grade = $_POST["grade"];
			$visiblity = $_POST["visiblity"];
			
			$day = $_POST["day"];
			$month = $_POST["month"];
			$year = $_POST["year"];
			$hour = $_POST["hour"];
			$minute = $_POST["minute"];
				
			$dayFormatted = sprintf("%02d", $day);
			$monthFormatted = sprintf("%02d", $month);
				
			$deadline = $year."-".$month."-".$day." ".$hour.":".$minute.":00";
			
			$topicid = $_POST["topicid"];
			$courseid = $_POST["courseid"];
			
			$dbQuery=$db->prepare("insert into assignments values (null,:name,:brief,:deadline,:grade,:courseid,:visiblity)");
			$dbParams=array('name'=>$name, 'brief'=>$brief, 'deadline'=>$deadline, 'grade'=>$grade, 'visiblity'=>$visiblity, 'courseid'=>$courseid);
			$dbQuery->execute($dbParams);
				
			addElement($courseid, $topicid, "assignment");
			redirect("../../course/view.php?id=".$courseid);
			
		}
		else if (isset($_GET["topicid"]) && isset($_GET["courseid"]))
		{
			if($_GET["topicid"]==null || $_GET["courseid"]==null)
            {
                error("Variables not set in the URL", "../../");
            }
			$topicid = $_GET["topicid"];
			$courseid = $_GET["courseid"];
			$addToCourse = true;
		}
		else {
			error("Variables not set in the URL", "../../");
		}
		
		$dbQueryEnrolments = $db->prepare("select * from enrolments where userID=:userID AND courseID=:courseID");
		$dbParamsEnrolments = array('userID' => $userID, 'courseID' => $courseid);
		$dbQueryEnrolments->execute($dbParamsEnrolments);
		$dbRowEnrolments=$dbQueryEnrolments->fetch(PDO::FETCH_ASSOC);
		$role = $dbRowEnrolments["role"];
		
		if(!has_capability("course:admin",$userID) && $role != "teacher")
		{
			error("You do not have permission to access this page", "../../");
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
	
    <title><?php echo $sitename;?> | Create an assignment</title>
	
	<!--DK CSS-->
	<link href="../../styles.css" rel="stylesheet">
	
	<!--CKEDITOR JS-->
	<script src="https://cdn.ckeditor.com/4.8.0/standard/ckeditor.js"></script>
	
	<script>
	/*function disableField()
	{
		cb = document.getElementById('useDeadline').checked;
		
		document.getElementById('day').disabled = !cb;
		document.getElementById('month').disabled = !cb;
		document.getElementById('year').disabled = !cb;
		document.getElementById('hour').disabled = !cb;
		document.getElementById('minute').disabled = !cb;
	}*/
	</script>
	
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
					echo "<img src='".$profileimage."' width='28px' alt='Profile Image' class='rounded-circle'>&nbsp;<a href='../profile/'>".$fullname." (<a href='../../profile/killSession.php'>Log out</a>)</a>";
				}
				else {
					echo "<a href='../../login/'>Log in or sign up</a>";
				}
			  ?>
			</span>
		  </div>
		</nav>

      <div class="container">
	  
	  <br>
	  <h1>Create an assignment</h1>
	
		<br>
		<form method="post" action="create.php">
			<div class="form-row">
				<div class="form-group col-md-12">
					<label for="name">Assignment name</label>
					<input type="text" class="form-control" id="name" name="name" placeholder="Page name">
				</div>
			</div>
			
			<div class="form-row">
				<div class="form-group col-md-12">
					<label for="brief">Assignment brief</label>
					<textarea class="form-control" id="brief" aria-describedby="briefHelp" name="brief" rows="10"></textarea>
					<small id="briefHelp" class="form-text text-muted">Link any supporting documents here</small>
					
					<script>
						CKEDITOR.replace( 'brief' );
					</script>
				</div>
			</div>
			<!-- MAY NOT BE NEEDED
			<div class="form-row">
				<div class="form-group col-md-12">
					<div class="custom-control custom-checkbox">
						<input type="checkbox" class="custom-control-input" onclick="disableField();" id="useDeadline" name="useDeadline">
						<label class="custom-control-label" for="useDeadline">Set a deadline</label>
					</div>
				</div>
			</div>
			-->
			<div class="form-row">
				<div class="form-group col-md-2">
					<label for="day">Deadline date</label>
					<select id="day" name="day" class="form-control">
						<option selected>Day</option>
						<?php
							for($loop=1;$loop<32;$loop++)
							{
								echo "<option value='".$loop."'>".$loop."</option>";
							}
						?>
					</select>
				</div>
				
				<div class="form-group col-md-2">
					<label for="month">&nbsp;</label>
					<select id="month" name="month" class="form-control">
						<option selected>Month</option>
						<?php
							for($loop=1;$loop<13;$loop++)
							{
								echo "<option value='".$loop."'>".$loop."</option>";
							}
						?>
					</select>
				</div>
				
				<div class="form-group col-md-2">
					<label for="year">&nbsp;</label>
					<select id="year" name="year" class="form-control">
						<option selected>Year</option>
						<?php
							$currentYear=date("Y");
							for($loop=$currentYear;$loop<=$currentYear+20;$loop++)
							{
								echo "<option value='".$loop."'>".$loop."</option>";
							}
						?>
					</select>
				</div>
				
				<div class="form-group col-md-2">
					&nbsp;
				</div>
				
				<div class="form-group col-md-2">
					<label for="hour">Deadline time</label>
					<select id="hour" name="hour" class="form-control">
						<option selected>Hour</option>
						<?php
							for($loop=0;$loop<=23;$loop++)
							{
								$hour = sprintf("%02d", $loop);
								echo "<option value='".$hour."'>".$hour."</option>";
							}
						?>
					</select>
				</div>
				
				<div class="form-group col-md-2">
					<label for="minute">&nbsp;</label>
					<select id="minute" name="minute" class="form-control">
						<option selected>Minute</option>
						<?php
							for($loop=0;$loop<=59;$loop++)
							{
								$minute = sprintf("%02d", $loop);
								echo "<option value='".$minute."'>".$minute."</option>";
							}
						?>
					</select>
				</div>
				
			</div>
			
			<div class="form-row">
				<div class="form-group col-md-6">
					<label for="visiblity">Page visiblity</label>
					<select id="visiblity" name="visiblity" class="form-control">
						<option value="1" selected>Show</option>
						<option value="0">Hide</option>
					</select>
				</div>
	
				<div class="form-group col-md-6">
					<label for="grade">Grade</label>
					<input type="number" class="form-control" id="grade" name="grade" placeholder="%">
				</div>
			</div>
			
			<?php

			echo '<input type="hidden" name="topicid" value="'.$topicid.'" />';
			echo '<input type="hidden" name="courseid" value="'.$courseid.'" />';
				
			?>
			
			<input class="btn btn-primary" value="Create assignment" name="submit" type="submit" />
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
