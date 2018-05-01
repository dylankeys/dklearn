<?php
	session_start();
	include("../config.php");
	include("../lib.php");
	
	if (isset($_POST["enrol"]))
	{
		$userids=$_POST["unenrolled"];
		$courseid=$_POST["courseid"];
		
		foreach ($userids as $index => $userid) {
			
			$dbQuery=$db->prepare("insert into enrolments values(null,:courseid,:userid,'student')");
			$dbParams=array('userid'=>$userid, 'courseid'=>$courseid);
			$dbQuery->execute($dbParams);
		}
		
		redirect("enrolments.php?id=".$courseid."&success=1");
	}
	else if (isset($_POST["unenrol"]))
	{
		$userids=$_POST["enrolled"];
		$courseid=$_POST["courseid"];
		
		foreach ($userids as $index => $userid) {
			
			$dbQuery=$db->prepare("delete from enrolments where userid=:userid and courseid=:courseid and role='student'");
			$dbParams=array('userid'=>$userid, 'courseid'=>$courseid);
			$dbQuery->execute($dbParams);
		}
		
		redirect("enrolments.php?id=".$courseid."&success=2");
	}
	else if (isset($_POST["enrolTeacher"]))
	{
		$userids=$_POST["unenrolled"];
		$courseid=$_POST["courseid"];
		
		foreach ($userids as $index => $userid) {
			
			$dbQuery=$db->prepare("insert into enrolments values(null,:courseid,:userid,'teacher')");
			$dbParams=array('userid'=>$userid, 'courseid'=>$courseid);
			$dbQuery->execute($dbParams);
		}
		
		redirect("enrolments.php?id=".$courseid."&success=1");
	}
	else if (isset($_POST["unenrolTeacher"]))
	{
		$userids=$_POST["enrolled"];
		$courseid=$_POST["courseid"];
		
		foreach ($userids as $index => $userid) {
			
			$dbQuery=$db->prepare("delete from enrolments where userid=:userid and courseid=:courseid and role='teacher'");
			$dbParams=array('userid'=>$userid, 'courseid'=>$courseid);
			$dbQuery->execute($dbParams);
		}
		
		redirect("enrolments.php?id=".$courseid."&role=teacher&success=2");
	}
	else {
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
		
		if (isset($_GET["id"]))
		{
            if($_GET["id"]==null)
            {
				error("No course ID has been supplied in the URL", "../");
            }
			$id = $_GET["id"];
			
			$dbQuery=$db->prepare("select * from courses where id=:id");
			$dbParams=array('id'=>$id);
			$dbQuery->execute($dbParams);

			while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC))
			{
				$title=$dbRow["title"];
				
				echo "<title>".$title." | Enrolments</title>";
			}
		}
		else
		{
			error("No course ID has been supplied in the URL", "../");
		}
		
		$dbQueryEnrolments = $db->prepare("select * from enrolments where userID=:userID AND courseID=:courseID");
		$dbParamsEnrolments = array('userID' => $userID, 'courseID' => $id);
		$dbQueryEnrolments->execute($dbParamsEnrolments);
		$dbRowEnrolments=$dbQueryEnrolments->fetch(PDO::FETCH_ASSOC);
		$role = $dbRowEnrolments["role"];
		
		if(!has_capability("course:admin",$userID) && $role != "teacher")
		{
			error("You do not have permission to access this page", "../");
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
	
    <title><?php echo $sitename;?> | Courses</title>
	
	<!--CKEDITOR JS-->
	<script src="https://cdn.ckeditor.com/4.8.0/standard/ckeditor.js"></script>
	
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
			if (isset($_GET["success"]) && $_GET["success"]=="1")
            {
				echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
						<strong>Success!</strong> Enrolment(s) successful.
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>';
            }
			else if (isset($_GET["success"]) && $_GET["success"]=="2")
            {
				echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
						<strong>Success!</strong> Unenrolment(s) successful.
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>';
            }
		?>
		
            <h1>
				<?php echo $title; ?>
				<small class="text-muted">Enrolments</small>
			</h1>
            
			<br>
			
		<form method="get" action="enrolments.php">
			<label for="role">Role select:</label>
			<select id="role" name="role" onchange="this.form.submit()">
				<option value="student" <?php if (isset($_GET["role"]) && $_GET["role"] != "teacher") { echo "selected"; } ?>>Student</option>
				<option value="teacher" <?php if (isset($_GET["role"]) && $_GET["role"] == "teacher") { echo "selected"; } ?> >Teacher</option>
			</select>
			<input type="hidden" name="id" value="<?php echo $id; ?>" />
		</form>
		
		<?php
			if (isset($_GET["role"]) && $_GET["role"] == "teacher")
			{
		?>
			
			<form method="post" action="enrolments.php">
					<div class="form-row">
						<div class="form-group col-md-5">
							<h4><label for="enrolled">Enrolled users</label></h4>
							<select id="enrolled" name="enrolled[]" class="custom-select" size="10" multiple>
								<?php
									$enrolledUsers=array();
									
									$dbQuery=$db->prepare("select users.id, users.fullname, users.username from `enrolments` inner join `users` on enrolments.userid=users.id where enrolments.courseid=:id and enrolments.role='teacher'");
									$dbParams=array('id'=>$id);
									$dbQuery->execute($dbParams);

									while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC))
									{
										$userid=$dbRow["id"];
										$userfullname=$dbRow["fullname"];
										$userusername=$dbRow["username"];
										
										array_push($enrolledUsers,$userid);
										
										echo "<option value='".$userid."'>".$userfullname." (".$userusername.")</option>";
									}
								?>
							</select>
						</div>
						
						<div class="form-group col-md-2 enrol-btns">
							<div style="padding: 10px">
								<input class="btn btn-danger" type="submit" value="Unenrol user(s) >>" name="unenrolTeacher" />
								
							</div>
						<input type="hidden" name="courseid" value="<?php echo $id; ?>" />
				</form>
				<form method="post" action="enrolments.php">	
							<input class="btn btn-primary" type="submit" value="<< Enrol user(s)" name="enrolTeacher" />
						</div>
						<div class="form-group col-md-5">
							<h4><label for="unenrolled">Not enrolled users</label></h4>
							<select id="unenrolled" name="unenrolled[]" class="custom-select" size="10" multiple>
								<?php
									$dbQuery=$db->prepare("select id, fullname, username from `users`");
									$dbQuery->execute();

									while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC))
									{
										$userEnrolled = 0;
										$userid=$dbRow["id"];
										$userfullname=$dbRow["fullname"];
										$userusername=$dbRow["username"];
										
										foreach ($enrolledUsers as $enrolledUser) {
											if ($enrolledUser == $userid)
											{
												$userEnrolled = 1;
											}
										}
										
										if ($userEnrolled == 0)
										{
											echo "<option value='".$userid."'>".$userfullname." (".$userusername.")</option>";
										}
									}
								?>
							</select>
						</div>
					</div>
					
					<input type="hidden" name="courseid" value="<?php echo $id; ?>" />
				</form>
		
		<?php
			}
			else
			{
		?>
				<form method="post" action="enrolments.php">
					<div class="form-row">
						<div class="form-group col-md-5">
							<h4><label for="enrolled">Enrolled users</label></h4>
							<select id="enrolled" name="enrolled[]" class="custom-select" size="10" multiple>
								<?php
									$enrolledUsers=array();
									
									$dbQuery=$db->prepare("select users.id, users.fullname, users.username from `enrolments` inner join `users` on enrolments.userid=users.id where enrolments.courseid=:id and enrolments.role='student'");
									$dbParams=array('id'=>$id);
									$dbQuery->execute($dbParams);

									while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC))
									{
										$userid=$dbRow["id"];
										$userfullname=$dbRow["fullname"];
										$userusername=$dbRow["username"];
										
										array_push($enrolledUsers,$userid);
										
										echo "<option value='".$userid."'>".$userfullname." (".$userusername.")</option>";
									}
								?>
							</select>
						</div>
						
						<div class="form-group col-md-2 enrol-btns">
							<div style="padding: 10px">
								<input class="btn btn-danger" type="submit" value="Unenrol user(s) >>" name="unenrol" />
								
							</div>
						<input type="hidden" name="courseid" value="<?php echo $id; ?>" />
				</form>
				<form method="post" action="enrolments.php">	
							<input class="btn btn-primary" type="submit" value="<< Enrol user(s)" name="enrol" />
						</div>
						<div class="form-group col-md-5">
							<h4><label for="unenrolled">Not enrolled users</label></h4>
							<select id="unenrolled" name="unenrolled[]" class="custom-select" size="10" multiple>
								<?php
									$dbQuery=$db->prepare("select id, fullname, username from `users`");
									$dbQuery->execute();

									while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC))
									{
										$userEnrolled = 0;
										$userid=$dbRow["id"];
										$userfullname=$dbRow["fullname"];
										$userusername=$dbRow["username"];
										
										foreach ($enrolledUsers as $enrolledUser) {
											if ($enrolledUser == $userid)
											{
												$userEnrolled = 1;
											}
										}
										
										if ($userEnrolled == 0)
										{
											echo "<option value='".$userid."'>".$userfullname." (".$userusername.")</option>";
										}
									}
								?>
							</select>
						</div>
					</div>
					
					<input type="hidden" name="courseid" value="<?php echo $id; ?>" />
				</form>
		<?php
			}
		?>
		<br>

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