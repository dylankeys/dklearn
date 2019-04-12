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
	<script defer src="https://use.fontawesome.com/releases/v5.0.8/js/all.js" integrity="sha384-SlE991lGASHoBfWbelyBPLsUlwY1GwNDJo3jSJO04KZ33K2bwfV9YBauFfnzvynJ" crossorigin="anonymous"></script>

	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../images/favicon.ico">

	<?php
        $userID=$_SESSION["currentUserID"];
		
		if (isset($_POST["addtopic"]))
		{
			$courseid = $_POST["courseid"];
			$order = $_POST["order"];
			$name = $_POST["header"];
			$summary = $_POST["summary"];
			$visible = $_POST["visible"];
			
			$dbQuery=$db->prepare("insert into topics values (null,:courseid,:order,:name,:summary,:visible)");
			$dbParams=array('courseid'=>$courseid, 'order'=>$order, 'name'=>$name, 'summary'=>$summary, 'visible'=>$visible);
			$dbQuery->execute($dbParams);
			
			redirect("view.php?id=".$courseid);
		}
		else if (isset($_GET["action"]) && isset($_GET["courseid"]))
		{
            if($_GET["action"]==null || $_GET["courseid"]==null)
            {
                error("Required variables not set in URL", "../");
            }
			else {
				$action = $_GET["action"];
				$courseid = $_GET["courseid"];
			}
		}
		else
		{
			error("Required variables not set in URL", "../");
		}
		
		if ($action == "element")
		{
			if (isset($_GET["topicid"]) && $_GET["topicid"]!=null)
			{
				$topicid = $_GET["topicid"];
			}
			else
			{
				error("No topic ID set in URL", "../");
			}
		}
		
		$dbQueryEnrolments = $db->prepare("select * from enrolments where userID=:userID AND courseID=:courseID");
		$dbParamsEnrolments = array('userID' => $userID, 'courseID' => $courseid);
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
	
	<!--DK CSS-->
	<link href="../styles.css" rel="stylesheet">
	
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
		
			if ($action=="topic")
			{
		?>
            <h1>Add a topic</h1>
            
			<br>
			
			<form method="post" action="add.php">
			<form>
				<div class="form-row">
					<div class="form-group col-md-12">
						<label for="header">Topic header</label>
						<input type="text" class="form-control" id="header" name="header" placeholder="Header">
					</div>
				</div>
				
				<div class="form-row">
					<div class="form-group col-md-12">
						<label for="summary">Topic summary</label>
						<textarea class="form-control" id="summary" name="summary" rows="5"></textarea>
					</div>
				</div>
				
				<div class="form-row">
					<div class="form-group col-md-6">
						<label for="order">Order</label>
						<input type="number" class="form-control" id="order" name="order" placeholder="e.g. 1, 2, 3 or 4" value="<?php echo $order; ?>">
					</div>
					
					<div class="form-group col-md-6">
						<label for="visible">Visibility</label>
						<select id="visible" name="visible" class="form-control">
							<option value="1" selected>Show</option>
							<option value="0">Hide</option>
						</select>
					</div>
				</div>
				
				<input type="hidden" name="courseid" value="<?php echo $courseid; ?>" />
				<input type="hidden" name="addtopic" />
				
				<input class="btn btn-primary" type="submit" name="submit" />
			</form>
		<?php
			}
			else if ($action=="element")
			{
		?>
			<h1>Add an course element</h1>
			<br>
			<h3>
				<small class="text-muted">Activities (completable elements)</small>
			</h3>
			
			<div class="row">
				<div class="col-sm-6">
					<div class="card h-100">
						<div class="card-body">
							<h5 class="card-title"><i class="fas fa-clipboard-list"></i>&nbsp;&nbsp;Assignment</h5>
							<p class="card-text">A submittable assignment, constrained by deadlines, that can be graded.</p>
							<a href="../element/assignment/create.php?topicid=<?php echo $topicid; ?>&courseid=<?php echo $courseid; ?>" class="card-link">Add to course</a>
						</div>
					</div>
				</div>
				
				<div class="col-sm-6">
					<div class="card h-100">
						<div class="card-body">
							<h5 class="card-title"><i class="fas fa-question"></i>&nbsp;&nbsp;Quiz</h5>
							<p class="card-text">A multi-choice quiz that is automatically graded on completion.</p>
							<a href="../element/quiz/create.php?topicid=<?php echo $topicid; ?>&courseid=<?php echo $courseid; ?>" class="card-link">Add to course</a>
						</div>
					</div>
				</div>
			</div>
			
			<br>
			<h3>
				<small class="text-muted">Resources (non-completable elements)</small>
			</h3>
			
			<div class="row">
				<div class="col-sm-6">
					<div class="card h-100">
						<div class="card-body">
							<h5 class="card-title"><i class="far fa-file"></i>&nbsp;&nbsp;File</h5>
							<p class="card-text">A file that students can download.</p>
							<a href="../element/file/create.php?topicid=<?php echo $topicid; ?>&courseid=<?php echo $courseid; ?>" class="card-link">Add to course</a>
						</div>
					</div>
				</div>
				
				<div class="col-sm-6">
					<div class="card h-100">
						<div class="card-body">
							<h5 class="card-title"><i class="fas fa-link"></i>&nbsp;&nbsp;Link</h5>
							<p class="card-text">An embedded web link on the course page.</p>
							<a href="../element/link/create.php?topicid=<?php echo $topicid; ?>&courseid=<?php echo $courseid; ?>" class="card-link">Add to course</a>
						</div>
					</div>
				</div>
			</div>
			<br>
			<div class="row">
				<div class="col-sm-6">
					<div class="card h-100">
						<div class="card-body">
							<h5 class="card-title"><i class="far fa-file-alt"></i>&nbsp;&nbsp;Page</h5>
							<p class="card-text">A site page that students can access and read.</p>
							<a href="../settings/sitepages/create.php?topicid=<?php echo $topicid; ?>&courseid=<?php echo $courseid; ?>" class="card-link">Add to course</a>
						</div>
					</div>
				</div>
				
				<div class="col-sm-6">
					<div class="card h-100">
						<div class="card-body">
							<h5 class="card-title"><i class="fab fa-youtube"></i>&nbsp;&nbsp;YouTube video</h5>
							<p class="card-text">A youtube video that is embedded on the course page.</p>
							<a href="../element/video/create.php?topicid=<?php echo $topicid; ?>&courseid=<?php echo $courseid; ?>" class="card-link">Add to course</a>
						</div>
					</div>
				</div>
			</div>
  
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
