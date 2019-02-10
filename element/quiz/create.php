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
				if ($_POST["submit"] == "Create quiz")
				{
					$title = $_POST["title"];
					$summary = $_POST["summary"];
					$pass = $_POST["pass"];
					$visible = $_POST["visible"];
					
					$dayStart = $_POST["dayStart"];
					$monthStart = $_POST["monthStart"];
					$yearStart = $_POST["yearStart"];
					$hourStart = $_POST["hourStart"];
					$minuteStart = $_POST["minuteStart"];
					
					$dayEnd = $_POST["dayEnd"];
					$monthEnd = $_POST["monthEnd"];
					$yearEnd = $_POST["yearEnd"];
					$hourEnd = $_POST["hourEnd"];
					$minuteEnd = $_POST["minuteEnd"];
					
					$dayEndFormatted = sprintf("%02d", $dayEnd);
					$monthEndFormatted = sprintf("%02d", $monthEnd);
					
					$dayStartFormatted = sprintf("%02d", $dayStart);
					$monthStartFormatted = sprintf("%02d", $monthStart);
					
					$start = $yearStart."-".$monthStart."-".$dayStart." ".$hourStart.":".$minuteStart.":00";
					$end = $yearEnd."-".$monthEnd."-".$dayEnd." ".$hourEnd.":".$minuteEnd.":00";
					
					$topicid = $_POST["topicid"];
					$courseid = $_POST["courseid"];
					
					$dbQuery=$db->prepare("insert into quiz values (null,:title,:summary,:start,:end,:courseid,:visible,:pass)");
					$dbParams=array('title'=>$title, 'summary'=>$summary, 'start'=>$start, 'end'=>$end, 'visible'=>$visible, 'courseid'=>$courseid, 'pass'=>$pass);
					$dbQuery->execute($dbParams);
					
					addElement($courseid, $topicid, "quiz");
					
					$dbQuery=$db->prepare("select max(id) as maxID from quiz");
					$dbQuery->execute();
					$dbRow=$dbQuery->fetch(PDO::FETCH_ASSOC);
					
					$quizid = $dbRow["maxID"];
					
					redirect("create.php?action=questions&id=".$quizid);
				}
				else if ($_POST["submit"] == "Add question")
				{
					$id = $_POST["quizid"];
					$question = $_POST["question"];
					
					$dbQuery=$db->prepare("insert into quiz_questions values (null,:quizid,:question)");
					$dbParams=array('quizid'=>$id, 'question'=>$question);
					$dbQuery->execute($dbParams);
					
					$dbQuery=$db->prepare("select max(id) as maxID from quiz_questions");
					$dbQuery->execute();
					$dbRow=$dbQuery->fetch(PDO::FETCH_ASSOC);
					
					$questionid = $dbRow["maxID"];
					
					foreach($_POST['answer'] as $index => $answer) {
						
						if ($index == "0") {
							$correctanswer = 1;
						}
						else {
							$correctanswer = 0;
						}
						
						$dbQuery=$db->prepare("insert into quiz_answers values (null,:questionid,:answer,:correctanswer)");
						$dbParams=array('questionid'=>$questionid, 'answer'=>$answer, 'correctanswer'=>$correctanswer);
						$dbQuery->execute($dbParams);
					}
					
					redirect("create.php?action=questions&id=".$id);
				}
			}
			else if (isset($_POST["action"]) && $_POST["action"] == "deleteQuestion")
			{
				$deleteid = $_POST["questionid"];
				$quizid = $_POST["quizid"];
				
				$dbParams=array('id'=>$deleteid);
				
				$dbQuery=$db->prepare("delete from quiz_answers where questionid=:id");
				$dbQuery->execute($dbParams);
				
				$dbQuery=$db->prepare("delete from quiz_questions where id=:id");
				$dbQuery->execute($dbParams);
				
				redirect("create.php?action=questions&id=".$quizid);
			}
			else if (isset($_GET["action"]) && isset($_GET["id"]))
			{
				if($_GET["action"]==null || $_GET["id"]==null)
				{
					error("Page variables not set", "../../");
				}
				$action=$_GET["action"];
				$quizid=$_GET["id"];
				
				if(isset($_GET["questionid"]))
				{
					$questionid=$_GET["questionid"];
				}
			}
			else if (isset($_GET["topicid"]) && isset($_GET["courseid"]))
			{
				if($_GET["topicid"]==null || $_GET["courseid"]==null)
				{
					error("Page variables not set", "../../");
				}
				$topicid = $_GET["topicid"];
				$courseid = $_GET["courseid"];
				$action="quiz";
			}
			else {
				error("Page variables not set", "../../");
			}
			
			if (!isset($_GET["courseid"]))
			{
				$dbQuery = $db->prepare("select courseid from quiz where id=:id");
				$dbParams = array('id' => $quizid);
				$dbQuery->execute($dbParams);
				$dbRow=$dbQuery->fetch(PDO::FETCH_ASSOC);
				$courseid = $dbRow["courseid"];
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
		
		<title><?php echo $sitename;?> | Create a quiz</title>
		
		<!--DK CSS-->
		<link href="../../styles.css" rel="stylesheet">
		
		<!--CKEDITOR JS-->
		<script src="https://cdn.ckeditor.com/4.8.0/standard/ckeditor.js"></script>
		
		<script  src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
		<script type="text/javascript">
			// Code from https://www.sanwebe.com/2013/03/addremove-input-fields-dynamically-with-jquery
			$(document).ready(function() {
			var max_fields      = 5; //maximum input boxes allowed
			var wrapper         = $(".input_fields_wrap"); //Fields wrapper
			var add_button      = $(".add_field_button"); //Add button ID
			
			var x = 1; //initlal text box count
			$(add_button).click(function(e){ //on add input button click
				e.preventDefault();
				if(x < max_fields){ //max input box allowed
					x++; //text box increment
					$(wrapper).append('<div class="form-row"><div class="form-group col-md-11"><input type="text" class="form-control" name="answer[]" placeholder="Incorrect answer"/></div><div class="form-group col-md-1"><a href="#" class="remove_field">x</a></div></div>'); //add input box
				}
			});
			
			$(wrapper).on("click",".remove_field", function(e){ //user click on remove text
				e.preventDefault(); $(this).parents('.form-row').remove(); x--;
			})
		});
			
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
			
			<?php
				if ($action == "deleteQuestion")
				{
					echo '<h3>Are you sure?</h3>';
					echo '<p>This question will be permanently deleted.</p>';
					echo '<form class="confirm-delete" method="post" action="create.php">
							<input type="hidden" name="questionid" value="'.$questionid.'" />
							<input type="hidden" name="quizid" value="'.$quizid.'" />
							<input type="hidden" name="action" value="deleteQuestion" />
							<button type="submit" class="btn btn-success">Yes</button>
						</form>';
					echo '<button type="button" onclick="window.location.href=\'create.php?action=questions&id='.$quizid.'\'" class="btn btn-danger confirm-delete">No</button>';
				}
				else if ($action == "questions")
				{
			?>
				<h1>Create a quiz | Questions</h1>
				<div class="course-btn"> 
					<button type="button" class="btn btn-primary btn-sm" onclick="window.location.href='view.php?id=<?php echo $quizid; ?>'">Back to quiz</button>
				</div>
				<br>
				<h3>Questions</h3>
				
				<?php
					echo '<table class="table table-hover">
						<thead>
							<tr>
								<th scope="col">Question</th>
								<th scope="col">Answers</th>
								<th scope="col">Options</th>
							</tr>
						</thead>
					<tbody>';
					
					$dbQuery=$db->prepare("select * from quiz_questions where quizid=:quizid");
					$dbParams = array('quizid'=>$quizid);
					$dbQuery->execute($dbParams);
					
					while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC))
					{
						$questionid=$dbRow["id"];
						$question=$dbRow["question"];
										
						$answers = array();
						
						$dbQueryAnswers=$db->prepare("select * from quiz_answers where questionid=:questionid");
						$dbParamsAnswers = array('questionid'=>$questionid);
						$dbQueryAnswers->execute($dbParamsAnswers);
						
						while ($dbRowAnswers = $dbQueryAnswers->fetch(PDO::FETCH_ASSOC))
						{
							$answer=$dbRowAnswers["answer"];
							$correctanswer=$dbRowAnswers["correctanswer"];
							
							$answers[$answer] = $correctanswer;
						}
						
						echo '<tr>
								<td>'.$question.'</td><td>';
								
								foreach($answers as $answer => $isCorrect) {
									if ($isCorrect == "1") {
										echo $answer . " (*)";
									}
									else {
										echo $answer;
									}
									echo "<br>";
								}
								
						echo '</td><td><a href="create.php?action=deleteQuestion&questionid='.$questionid.'&id='.$quizid.'">Delete</a></td>
							</tr>';
					}
				
					echo '</tbody>
						</table>';
					
					echo '<p>(*) denotes a correct answer</p>';
				?>
				
				<h3>Add a question</h3>
				<form action="create.php" method="post">
				
					<div class="form-row">
						<div class="form-group col-md-12">
							<label for="question">Question</label>
							<textarea class="form-control" id="question" name="question" rows="2"></textarea>
						</div>
					</div>
					
					<div class="input_fields_wrap">
						<div class="course-btn"> 
							<button type="button" class="btn btn-primary btn-sm add_field_button">Add answer</button>
						</div>
						<div class="form-row"><div class="form-group col-md-11"><input type="text" class="form-control" name="answer[]" placeholder="Correct answer"/></div><div class="form-group col-md-1"></div></div>
					</div>
					
					<p>Maximum of 5 answers</p>
					<input type="hidden" name="quizid" value="<?php echo $quizid; ?>">
					<input class="btn btn-primary" value="Add question" name="submit" type="submit" />
				</form>
				
			<?php
				}
				else
				{
			?>
				<h1>Create a quiz</h1>
				<br>
				
				<form method="post" action="create.php">
					<div class="form-row">
						<div class="form-group col-md-12">
							<label for="title">Quiz name</label>
							<input type="text" class="form-control" id="title" name="title" placeholder="Quiz name">
						</div>
					</div>
					
					<div class="form-row">
						<div class="form-group col-md-12">
							<label for="summary">Quiz summary</label>
							<textarea class="form-control" id="summary" name="summary" rows="5"></textarea>
							
							<script>
								CKEDITOR.replace( 'summary' );
							</script>
						</div>
					</div>
					
					<!-- Quiz open date and time -->
					
					<div class="form-row">
						<div class="form-group col-md-2">
							<label for="dayStart">Quiz open</label>
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
						
						<div class="form-group col-md-2">
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
						
						<div class="form-group col-md-2">
							<label for="yearStart">&nbsp;</label>
							<select id="yearStart" name="yearStart" class="form-control">
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
							<label for="hourStart">&nbsp;</label>
							<select id="hourStart" name="hourStart" class="form-control">
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
							<label for="minuteStart">&nbsp;</label>
							<select id="minuteStart" name="minuteStart" class="form-control">
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
					
					<!-- Quiz close date and time -->
					
					<div class="form-row">
						<div class="form-group col-md-2">
							<label for="dayEnd">Quiz close</label>
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
						
						<div class="form-group col-md-2">
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
						
						<div class="form-group col-md-2">
							<label for="yearEnd">&nbsp;</label>
							<select id="yearEnd" name="yearEnd" class="form-control">
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
							<label for="hourEnd">&nbsp;</label>
							<select id="hourEnd" name="hourEnd" class="form-control">
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
							<label for="minuteEnd">&nbsp;</label>
							<select id="minuteEnd" name="minuteEnd" class="form-control">
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
							<label for="visible">Page visiblity</label>
							<select id="visible" name="visible" class="form-control">
								<option value="1" selected>Show</option>
								<option value="0">Hide</option>
							</select>
						</div>
			
						<div class="form-group col-md-6">
							<label for="pass">Percentage to pass</label>
							<input type="text" id="pass" name="pass" placeholder="%" class="form-control">
						</div>
					</div>
					
					<?php
						
						echo '<input type="hidden" name="topicid" value="'.$topicid.'" />';
						echo '<input type="hidden" name="courseid" value="'.$courseid.'" />';
						
					?>
					
					<input class="btn btn-primary" value="Create quiz" name="submit" type="submit" />
				</form>
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
