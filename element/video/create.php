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
			$link = $_POST["youtube"];
			$width = $_POST["width"];
			$height = $_POST["height"];
			$visiblity = $_POST["visiblity"];
			
			$topicid = $_POST["topicid"];
			$courseid = $_POST["courseid"];

			$embed = preg_replace("/\s*[a-zA-Z\/\/:\.]*youtu(be.com\/watch\?v=|.be\/)([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i","<iframe width=\"".$width."\" height=\"".$height."\" src=\"//www.youtube.com/embed/$2\" allowfullscreen></iframe>",$link);
			
			$dbQuery=$db->prepare("insert into videos values (null,:name,:link,:embed,:courseid,:visiblity)");
			$dbParams=array('name'=>$name, 'link'=>$link, 'embed'=>$embed, 'visiblity'=>$visiblity, 'courseid'=>$courseid);
			$dbQuery->execute($dbParams);
			
			addElement($courseid, $topicid, "video");
			
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
	
    <title><?php echo $sitename;?> | Add a video</title>
	
	<!--DK CSS-->
	<link href="../../styles.css" rel="stylesheet">
	
	<!--CKEDITOR JS-->
	<script src="https://cdn.ckeditor.com/4.8.0/standard/ckeditor.js"></script>
	
	</head>

	<body>
		<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: <?php echo $theme;?>;">
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
	  <h1>Add a YouTube video</h1>
	
		<br>
		<form method="post" action="create.php">
			<div class="form-row">
				<div class="form-group col-md-12">
					<label for="name">Title</label>
					<input type="text" class="form-control" id="name" name="name" placeholder="Video title">
				</div>
			</div>

			<div class="form-row">
				<div class="form-group col-md-12">
					<label for="youtube">Youtube link</label>
					<input type="text" class="form-control" id="youtube" name="youtube" aria-describedby="youtubeHelp" placeholder="YouTube link">
					<small id="youtubeHelp" class="form-text text-muted">e.g. https://www.youtube.com/watch?v=i_2mWhfOhGU or https://youtu.be/i_2mWhfOhGU</small>
				</div>
			</div>

			<div class="form-row">
				<div class="form-group col-md-6">
					<label for="width">Video width</label>
					<input type="text" class="form-control" id="width" name="width" placeholder="recommended 600px">
				</div>

				<div class="form-group col-md-6">
					<label for="height">Video height</label>
					<input type="text" class="form-control" id="height" name="height" placeholder="recommended 400px">
				</div>
			</div>

			<div class="form-row">
				<div class="form-group col-md-12">
					<label for="visiblity">Page visiblity</label>
					<select id="visiblity" name="visiblity" class="form-control">
						<option value="1" selected>Show</option>
						<option value="0">Hide</option>
					</select>
				</div>
			</div>
			
			
			<?php

			echo '<input type="hidden" name="topicid" value="'.$topicid.'" />';
			echo '<input type="hidden" name="courseid" value="'.$courseid.'" />';
				
			?>
			
			<input class="btn btn-primary" value="Add video" name="submit" type="submit" />
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
