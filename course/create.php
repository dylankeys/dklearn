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
		
		if(!has_capability("course:create",$userID))
		{
			echo "<script>window.location.href = 'index.php?permission=0'</script>";
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
	
    <title><?php echo $sitename;?> | Create Course</title>
	
	<!--DK CSS-->
	<link href="../styles.css" rel="stylesheet">
	
	<script>
	function disableField()
	{
		cb = document.getElementById('useCourseDates').checked;
		
		document.getElementById('dayStart').disabled = !cb;
		document.getElementById('monthStart').disabled = !cb;
		document.getElementById('yearStart').disabled = !cb;
		document.getElementById('dayEnd').disabled = !cb;
		document.getElementById('monthEnd').disabled = !cb;
		document.getElementById('yearEnd').disabled = !cb;
	}
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

      <div class="container">
	  
	  <h1>Create course</h1>
	
		<br>
		<form method="post" action="createcourse-query.php">
		<form>
			<div class="form-row">
				<div class="form-group col-md-12">
					<label for="courseName">Course name</label>
					<input type="text" class="form-control" id="courseName" name="courseName" aria-describedby="courseNameHelp" placeholder="Course name">
					<small id="courseNameHelp" class="form-text text-muted">Your course name must be between 5-50 characters</small>
				</div>
			</div>
			
			<div class="form-row">
				<div class="form-group col-md-12">
					<div class="form-check">
						<label class="form-check-label">
							<input id="useCourseDates" name="useCourseDates" value="yes" onclick="disableField();" class="form-check-input" type="checkbox" checked> Use start and end dates
						</label>
					</div>
				</div>
			</div>
			
			<div class="form-row">
				<div class="form-group col-md-4">
					<label for="dayStart">Course start date</label>
					<select id="dayStart" name="dayStart" class="form-control">
						<option selected>Day</option>
						<?php
							for($loop=1;$loop<32;$loop++)
							{
								echo "<option value='".$loop."'>".$loop."</option>";
							}
						?>
					</select>
				</div>
				<div class="form-group col-md-4">
					<label for="monthStart">&nbsp;</label>
					<select id="monthStart" name="monthStart" class="form-control">
						<option selected>Month</option>
						<?php
							for($loop=1;$loop<13;$loop++)
							{
								echo "<option value='".$loop."'>".$loop."</option>";
							}
						?>
					</select>
				</div>
				<div class="form-group col-md-4">
					<label for="yearStart">&nbsp;</label>
					<select id="yearStart" name="yearStart" class="form-control">
						<option selected>Year</option>
						<?php
							for($loop=1990;$loop<2031;$loop++)
							{
								echo "<option value='".$loop."'>".$loop."</option>";
							}
						?>
					</select>
				</div>
			</div>
			
			<div class="form-row">
				<div class="form-group col-md-4">
					<label for="dayEnd">Course end date</label>
					<select id="dayEnd" name="dayEnd" class="form-control">
						<option selected>Day</option>
						<?php
							for($loop=1;$loop<32;$loop++)
							{
								echo "<option value='".$loop."'>".$loop."</option>";
							}
						?>
					</select>
				</div>
				<div class="form-group col-md-4">
					<label for="monthEnd">&nbsp;</label>
					<select id="monthEnd" name="monthEnd" class="form-control">
						<option selected>Month</option>
						<?php
							for($loop=1;$loop<13;$loop++)
							{
								echo "<option value='".$loop."'>".$loop."</option>";
							}
						?>
					</select>
				</div>
				<div class="form-group col-md-4">
					<label for="yearEnd">&nbsp;</label>
					<select id="yearEnd" name="yearEnd" class="form-control">
						<option selected>Year</option>
						<?php
							for($loop=1990;$loop<2031;$loop++)
							{
								echo "<option value='".$loop."'>".$loop."</option>";
							}
						?>
					</select>
				</div>
			</div>
			
			<div class="form-row">
				<div class="form-group col-md-12">
					<label for="description">Course description</label>
					<textarea class="form-control" id="description" name="description" rows="5"></textarea>
				</div>
			</div>
			
			<div class="form-row">
				<div class="form-group col-md-6">
					<label for="topicCount">Topics</label>
					<input type="number" class="form-control" aria-describedby="topicCountHelp" id="topicCount" name="topicCount">
					<small id="topicCountHelp" class="form-text text-muted">Number of topics on this course</small>
				</div>
				
				<div class="form-group col-md-6">
					<label for="active">Active</label>
					<select id="active" name="active" aria-describedby="activeHelp" class="form-control">
						<option value="y" selected>Yes</option>
						<option value="n">No</option>
					</select>
					<small id="activeHelp" class="form-text text-muted">Is this course active?</small>
				</div>
			</div>
			
			<input type="submit" />
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
