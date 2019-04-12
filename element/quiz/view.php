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

		if (isset($_GET["id"]))
		{
			if($_GET["id"]==null)
			{
				echo "<script>window.location.href = '../../course/index.php?course=noid'</script>";
			}
			$id = $_GET["id"];

			if (!isset($_SESSION["currentUserID"]))
			{
				echo "<script>window.location.href = '../../login/index.php?failCode=3'</script>";
			}

			$dbQuery=$db->prepare("select * from quiz where id=:id");
			$dbParams=array('id'=>$id);
			$dbQuery->execute($dbParams);

			while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC))
			{
				$name=$dbRow["title"];
				$summary=$dbRow["summary"];
				$courseid=$dbRow["courseid"];
				$start=$dbRow["start"];
				$end=$dbRow["end"];
				$toPass = $dbRow["pass"];
				$visible=$dbRow["visible"];
			}
		}
		else
		{
			echo "<script>window.location.href = '../../course/index.php?course=noid'</script>";
		}
		
		$dbQuery = $db->prepare("select courseid from quiz where id=:id");
		$dbParams = array('id' => $id);
		$dbQuery->execute($dbParams);
		$dbRow=$dbQuery->fetch(PDO::FETCH_ASSOC);
		$courseid = $dbRow["courseid"];
		
		$dbQueryEnrolments = $db->prepare("select * from enrolments where userID=:userID AND courseID=:courseID");
		$dbParamsEnrolments = array('userID' => $userID, 'courseID' => $courseid);
		$dbQueryEnrolments->execute($dbParamsEnrolments);
		$dbRowEnrolments=$dbQueryEnrolments->fetch(PDO::FETCH_ASSOC);
		$role = $dbRowEnrolments["role"];
		
		if ($visible == 0 && !has_capability("site:config",$userID) && $role != "teacher")
		{
			error("You do not have permission to access this page", "../../");
		}

		echo "<title>".$sitename." | ".$name."</title>";
		
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
			
			if (!has_capability("site:config",$userID) && !isEnrolled($courseid, $userID)) {
				redirect("../../course/view.php?id=".$courseid);
			}
        }
		
		$dbQuery=$db->prepare("select * from quiz_attempts where quizid=:quizid and userid=:userid");
        $dbParams = array('quizid'=>$id, 'userid'=>$userID);
        $dbQuery->execute($dbParams);
		$attemptCount = $dbQuery->rowCount();
		
		if ($attemptCount > 0)
		{
			while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC))
			{
				$score=$dbRow["grade"];
				$complete=$dbRow["complete"];
			}
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
					echo "<img src='".$profileimage."' width='28px' alt='Profile Image' class='rounded-circle'>&nbsp;<a href='../../profile/'>".$fullname." (<a href='../../profile/killSession.php'>Log out</a>)</a>";
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
		
		<h1>Quiz: <?php echo $name; ?></h1>
		<div class="course-btn">
		<?php 
			if(has_capability("course:admin",$userID) || $role == "teacher") { 
				echo '<button type="button" class="btn btn-primary btn-sm" onclick="window.location.href=\'../../function/editElement.php?id='.$id.'&element=quiz\'">Edit</button>&nbsp;';
				echo '<button type="button" class="btn btn-primary btn-sm" onclick="window.location.href=\'create.php?id='.$id.'&action=questions\'">Add questions</button>&nbsp;';
				echo '<button type="button" class="btn btn-primary btn-sm" onclick="window.location.href=\'view.php?id='.$id.'&action=preview\'">Preview</button>&nbsp;';
				echo '<button type="button" class="btn btn-primary btn-sm" onclick="window.location.href=\'view.php?id='.$id.'&action=attempts\'">Attempts</button>&nbsp;';
			}
		?>
		</div>
		<div class="p-3 mb-2 bg-light text-dark">
			<p>
			<?php 
				echo $summary;
				
				if ($attemptCount > 0)
				{
					echo "<br><br>";
					echo "<span class='score'>Score: ".$score."%</span>";
					
					if ($complete == 1)
					{
						echo "<span class='complete'>Status: Complete</span>";
					}
					else
					{
						echo "<span class='fail'>Status: Failed</span>";
					}
				}
			?>
			</p>
		</div>
		<br>
		
		<?php
		if (($attemptCount == 0 && !has_capability("course:admin",$userID)) && $role != "teacher" || ((has_capability("course:admin",$userID) || $role == "teacher") && (isset($_GET["action"]) && $_GET["action"] == "preview")))
		{
			if ((new DateTime() < new DateTime($start)) && ((!has_capability("course:admin",$userID)) && $role != "teacher"))
			{
				echo "<p class='quiz-time'>Quiz will open at " . $start . "</p>";
			}
			else if ((new DateTime() > new DateTime($end)) && ((!has_capability("course:admin",$userID)) && $role != "teacher"))
			{
				echo "<p class='quiz-time'>Quiz ended at " . $end . "</p>";
			}
			else
			{
				echo "<p class='quiz-time'>Quiz will end at " . $end . "</p>";
				
				echo "<form action='grade.php' method='post'>";
				
				$dbQuery=$db->prepare("select * from quiz_questions where quizid=:id");
				$dbParams = array('id'=>$id);
				$dbQuery->execute($dbParams);
						
				while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC))
				{
					$questionid=$dbRow["id"];
					$question=$dbRow["question"];
					
					echo "<h3>".$question."</h3>";
					
					$dbQueryQuestion=$db->prepare("select * from quiz_answers where questionid=:questionid order by RAND()");
					$dbParamsQuestion = array('questionid'=>$questionid);
					$dbQueryQuestion->execute($dbParamsQuestion);
					
					while ($dbRowQuestion = $dbQueryQuestion->fetch(PDO::FETCH_ASSOC))
					{
						$answerid=$dbRowQuestion["id"];
						$answer=$dbRowQuestion["answer"];
						
						echo '<div class="custom-control custom-radio">
								<input type="radio" id="'.$answerid.'" value="'.$answer.'" name="'.$questionid.'" class="custom-control-input">
								<label class="custom-control-label" for="'.$answerid.'">'.$answer.'</label>
							</div>';
					}
					echo "<br>";
				}
				
				echo '<input type="hidden" value="'.$id.'" name="quizid">';
				echo '<input type="hidden" value="'.$userid.'" name="userid">';
				echo '<input type="hidden" value="'.$toPass.'" name="pass">';
				if ((has_capability("course:admin",$userID) || $role == "teacher") && isset($_GET["action"]) && $_GET["action"] == "preview") {
					echo '<input class="btn btn-primary" value="Submit answers" name="submit" type="submit" disabled/>';
				}
				else {
					echo '<input class="btn btn-primary" value="Submit answers" name="submit" type="submit" />';
				}
				echo '</form>';
			}
		}
		else if ((has_capability("course:admin",$userID) || $role == "teacher") || (has_capability("course:admin",$userID) || $role == "teacher" && isset($_GET["action"]) && $_GET["action"] == "attempts"))
		{	
			$dbQuery=$db->prepare("select * from quiz_attempts inner join users on quiz_attempts.userid = users.id where quiz_attempts.quizid=:quizid order by quiz_attempts.complete desc");
			$dbParams = array('quizid'=>$id);
			$dbQuery->execute($dbParams);
			$userAttempts = $dbQuery->rowCount();
			
			if ($userAttempts > 0)
			{
				echo '<table class="table table-hover">
					<thead>
						<tr>
							<th scope="col">Student</th>
							<th scope="col">Time submitted</th>
							<th scope="col">Grade</th>
							<th scope="col">Complete</th>
						</tr>
					</thead>
				<tbody>';
				
				while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC))
				{
					$fullname=$dbRow["fullname"];
					$score=$dbRow["grade"];
					$complete=$dbRow["complete"];
					$submitted=$dbRow["submitted"];
					$timesubmitted = date('m/d/Y H:i:s', $submitted);
					
					if ($complete == 1)
					{
						$completed = "Yes";
					}
					else
					{
						$completed = "No";
					}
					
					echo '<tr>
							<td>'.$fullname.'</td>
							<td>'.$timesubmitted.'</td>
							<td>'.$score.'%</td>
							<td>'.$completed.'</td>
						</tr>';
				}
				
				echo '</tbody>
				</table>';
			}
			else
			{
				echo '<p class="no-results"><strong>No attempts</strong></p>';
			}
		}
		else
		{
			echo "<p style='text-align:center'><strong>Quiz attempted (see score and status above)</strong></p>";
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
