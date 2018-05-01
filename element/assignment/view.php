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

			$dbQuery=$db->prepare("select * from assignments where id=:id");
			$dbParams=array('id'=>$id);
			$dbQuery->execute($dbParams);

			while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC))
			{
				$title=$dbRow["title"];
				$brief=$dbRow["brief"];
				$deadline=$dbRow["deadline"];
				$courseid=$dbRow["courseid"];
				$visibility=$dbRow["visible"];
				
				if ($visibility == 0 && !has_capability("site:config",$userID))
				{
					echo "<script>window.location.href = '../../course/index.php?course=hidden'</script>";
				}

				echo "<title>".$sitename." | ".$title."</title>";
			}
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
		   
		    $dbQuery2 = $db->prepare("select * from enrolments where userID=:userid AND courseID=:courseid");
            $dbParams2 = array('userid' => $userid, 'courseid' => $courseid);
            $dbQuery2->execute($dbParams2);
            $rows = $dbQuery2->rowCount();

            if ($rows<1 && !has_capability("course:admin",$userID)) {
				echo "<script>window.location.href = '../../course/view.php?id=".$courseid."'</script>";
			}
		   
			$dbQuery3=$db->prepare("select * from assignment_submissions where assignmentid=:assignmentid and userid=:userid");
			$dbParams3=array('assignmentid'=>$id, 'userid'=>$userid);
			$dbQuery3->execute($dbParams3);
			$rowCount = $dbQuery3->rowCount();
			
			if ($rowCount > 0)
			{
				$submitted = true;
			}
			else {
				$submitted = false;
			}
        }
	?>
	
	<!--DK CSS-->
	<link href="../../styles.css" rel="stylesheet">
	
	</head>

	<body>
		<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #1E88FF;">
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
		
		<h1>Assignment: <?php echo $title; ?></h1>
		<div class="course-btn">
		<?php if(has_capability("course:admin",$userID)) { echo '<button type="button" class="btn btn-primary btn-sm" onclick="window.location.href=\'../../function/editElement.php?id='.$id.'&element=assignment\'">Edit</button>'; } ?>
		</div>
		<div class="p-3 mb-2 bg-light text-dark">
			<p><?php echo $brief; ?></p>
		</div>
		<br>
		
		<?php
		if (has_capability("course:admin",$userID))
		{
			echo "<h3>Submissions</h3>";
			
			$dbQuery=$db->prepare("select assignment_grades.id, users.fullname, assignment_grades.isgraded, assignment_submissions.submitted, assignment_submissions.file, assignment_grades.grade from assignment_grades INNER JOIN users ON assignment_grades.userid = users.id INNER JOIN assignment_submissions ON assignment_grades.assignmentid = assignment_submissions.assignmentid and assignment_grades.userid = assignment_submissions.userid where assignment_grades.assignmentid=:id order by assignment_grades.isgraded asc");
			$dbParams = array('id'=>$id);
			$dbQuery->execute($dbParams);
			$submissionCount = $dbQuery->rowCount();
			
			if ($submissionCount > 0)
			{
				echo '<table class="table table-hover">
					<thead>
						<tr>
							<th scope="col">Student</th>
							<th scope="col">Time submitted</th>
							<th scope="col">File submission</th>
							<th scope="col">Grade</th>
						</tr>
					</thead>
				<tbody>';
					
				while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC))
				{
					$gradeid=$dbRow["id"];
					$fullname=$dbRow["fullname"];
					$isgraded=$dbRow["isgraded"];
					$submitted=$dbRow["submitted"];
					$file=$dbRow["file"];
					$grade=$dbRow["grade"];
					$timesubmitted = date('m/d/Y H:i:s', $submitted);
					
					if ($isgraded==0)
					{
						echo '<tr>
								<td class="bold">'.$fullname.'</td>
								<td class="bold">'.$timesubmitted.'</td>
								<td class="bold"><a href="submissions/'.$file.'">Download file</a></td>
								<td class="bold"><a href="grade.php?id='.$gradeid.'&assignid='.$id.'">Grade</a></td>
							</tr>';
					}
					else
					{
						echo '<tr>
								<td>'.$fullname.'</td>
								<td>'.$timesubmitted.'</td>
								<td><a href="submissions/'.$file.'">Download file</a></td>
								<td>'.$grade.'%</td>
							</tr>';
					}	
				}
				echo '</tbody>
					</table>';
			}
			else
			{
				echo '<p class="no-results"><strong>No submissions</strong></p>';
			}
		}
		else
		{
		?>
		<div id="accordion">
			<div class="card">
				<div class="card-header" id="headingOne">
				  <h5 class="mb-0">
					<button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
					  Your submission
					</button>
					</h5>
				</div>
				
				<div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
					<div class="card-body">
					
						<?php 
						if ($submitted)
						{
							$dbQuery=$db->prepare("select assignment_grades.isgraded, assignment_grades.feedback, assignment_submissions.submitted, assignment_submissions.file, assignment_grades.grade from assignment_grades INNER JOIN assignment_submissions ON assignment_grades.assignmentid = assignment_submissions.assignmentid and assignment_grades.userid = assignment_submissions.userid where assignment_grades.assignmentid=:id and assignment_grades.userid=:userid");
							$dbParams = array('id'=>$id, 'userid'=>$userID);
							$dbQuery->execute($dbParams);
							
							while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC))
							{
								$isgraded=$dbRow["isgraded"];
								$submitted=$dbRow["submitted"];
								$file=$dbRow["file"];
								$feedback=$dbRow["feedback"];
								$grade=$dbRow["grade"];
								$timesubmitted = date('m/d/Y H:i:s', $submitted);
							}
							
							if ($isgraded == "1")
							{
								echo "<p><strong>Submitted and graded</strong></p><br>";
								echo "<p>Submitted on: ".$timesubmitted."</p><br>";
								echo "<p>Submission: <a href='submissions/".$file."'>Download</a></p><br>";
								echo "<p>Grade: ".$grade."%</p>";
							}
							else 
							{
								echo "Successfully submitted, to be graded.";
							}
						}
						else if (new DateTime() < new DateTime($deadline))
						{
						?>
						<p>You have yet to submit. You have until <strong><?php echo $deadline; ?></strong> to do so.</p>
						<form action="upload.php" method="post" enctype="multipart/form-data">
            				<input type="hidden" name="userid" value="<?php echo $userid; ?>">
            				<input type="hidden" name="assignid" value="<?php echo $id; ?>">
							<div class="form-row">
									<input type="file" name="file">
							</div>
							<br>
							<input class="btn btn-primary" value="Submit assignment" name="submit" type="submit" />
						</form>
						<?php
						}
						else 
						{
							echo "<p style='color:red'><strong>Deadline has passed.</strong></p>";
						}
						?>
					</div>
				</div>
			</div>
			  
			<div class="card">
				<div class="card-header" id="headingThree">
					<h5 class="mb-0">
						<button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
							Feedback
						</button>
					</h5>
				</div>
				<div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
					<div class="card-body">
						<?php
						if ($isgraded == "1")
						{
							echo "<p>".$feedback."</p>";
						}
						else {
							echo "<p>No feedback received yet.</p>";
						}
						?>
					</div>
				</div>
			</div>
		</div>
		<?php
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
