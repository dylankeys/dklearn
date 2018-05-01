<?php
	session_start();
	include("../config.php");
	include("../lib.php");
	
	if (isset($_POST["addCriteria"]))
	{
		$elementids=$_POST["elements"];
		$courseid=$_POST["courseid"];
		
		foreach ($elementids as $index => $elementid) {
			
			$dbQuery=$db->prepare("insert into course_completion_criteria values(null,:courseid,:elementid)");
			$dbParams=array('elementid'=>$elementid, 'courseid'=>$courseid);
			$dbQuery->execute($dbParams);
		}
		
		//recalculateCompletion($courseid);
		redirect("completion.php?id=".$courseid."&success=1");
	}
	else if (isset($_POST["removeCriteria"]))
	{
		$elementids=$_POST["criteria"];
		$courseid=$_POST["courseid"];
		
		foreach ($elementids as $index => $elementid) {
			
			$dbQuery=$db->prepare("delete from course_completion_criteria where elementid=:elementid and courseid=:courseid");
			$dbParams=array('elementid'=>$elementid, 'courseid'=>$courseid);
			$dbQuery->execute($dbParams);
		}
		
		//recalculateCompletion($courseid);
		redirect("completion.php?id=".$courseid."&success=2");
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
				
				echo "<title>".$title." | Completion</title>";
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
						<strong>Success!</strong> Completion criteria added.
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>';
            }
			else if (isset($_GET["success"]) && $_GET["success"]=="2")
            {
				echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
						<strong>Success!</strong> Completion criteria removed.
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>';
            }
		?>
			<h1>
				<?php echo $title; ?>
				<small class="text-muted">Course completion</small>
			</h1>
			
			<div class="p-3 mb-2 bg-light text-dark">
				<p>The course completion criteria denotes the course elements that must be completed before a student is deemed to have completed the course.</p>
			</div>
            
			<br>
			
			<form method="post" action="completion.php">
					<div class="form-row">
						<div class="form-group col-md-5">
							<h4><label for="criteria">Completion criteria</label></h4>
							<select id="criteria" name="criteria[]" class="custom-select" size="10" multiple>
								<?php
									$currentCriteria=array();
									
									$dbQuery=$db->prepare("select elements.id, elements.contentid, elements.typeid from course_completion_criteria inner join elements on course_completion_criteria.elementid = elements.id where course_completion_criteria.courseid = :courseid");
									$dbParams=array('courseid'=>$id);
									$dbQuery->execute($dbParams);

									while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC))
									{
										$typeid=$dbRow["typeid"];
										$contentid=$dbRow["contentid"];
										$elementid=$dbRow["id"];
										
										array_push($currentCriteria,$elementid);
										
										$dbQueryElementType=$db->prepare("select name, tablename from elements_type where id=:typeid");
										$dbParamsElementType=array('typeid'=>$typeid);
										$dbQueryElementType->execute($dbParamsElementType);
										$dbRowElementType=$dbQueryElementType->fetch(PDO::FETCH_ASSOC);
										
										$tablename=$dbRowElementType["tablename"];
										$elementtype=$dbRowElementType["name"];
										
										$dbQueryElement=$db->prepare("select title from ".$tablename." where id=:contentid");
										$dbParamsElement=array('contentid'=>$contentid);
										$dbQueryElement->execute($dbParamsElement);
										$dbRowElement=$dbQueryElement->fetch(PDO::FETCH_ASSOC);
										
										$title=$dbRowElement["title"];
										
										echo "<option value='".$elementid."'>".$title." (".$elementtype.")</option>";
									}
								?>
							</select>
						</div>
						
						<div class="form-group col-md-2 enrol-btns">
							<div style="padding: 10px">
								<input class="btn btn-danger" type="submit" value="Remove >>" name="removeCriteria" />
								
							</div>
						<input type="hidden" name="courseid" value="<?php echo $id; ?>" />
				</form>
				<form method="post" action="completion.php">	
							<input class="btn btn-primary" type="submit" value="<< Add" name="addCriteria" />
						</div>
						<div class="form-group col-md-5">
							<h4><label for="elements">Course elements</label></h4>
							<select id="elements" name="elements[]" class="custom-select" size="10" multiple>
								<?php
									$dbQuery=$db->prepare("select topic_content.elementid from topic_content inner join topics on topic_content.topicid = topics.id where topics.courseid = :courseid");
									$dbParams=array('courseid'=>$id);
									$dbQuery->execute($dbParams);

									while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC))
									{
										$inCriteria = 0;
										$elementid=$dbRow["elementid"];
										
										$dbQueryElements=$db->prepare("select contentid, typeid from elements where id=:elementid");
										$dbParamsElements=array('elementid'=>$elementid);
										$dbQueryElements->execute($dbParamsElements);
										$dbRowElements=$dbQueryElements->fetch(PDO::FETCH_ASSOC);
										
										$contentid=$dbRowElements["contentid"];
										$typeid=$dbRowElements["typeid"];
										
										$dbQueryElementType=$db->prepare("select name, tablename, type from elements_type where id=:typeid");
										$dbParamsElementType=array('typeid'=>$typeid);
										$dbQueryElementType->execute($dbParamsElementType);
										$dbRowElementType=$dbQueryElementType->fetch(PDO::FETCH_ASSOC);
										
										$tablename=$dbRowElementType["tablename"];
										$elementname=$dbRowElementType["name"];
										$elementtype=$dbRowElementType["type"];
										
										$dbQueryElement=$db->prepare("select title from ".$tablename." where id=:contentid");
										$dbParamsElement=array('contentid'=>$contentid);
										$dbQueryElement->execute($dbParamsElement);
										$dbRowElement=$dbQueryElement->fetch(PDO::FETCH_ASSOC);
										
										$title=$dbRowElement["title"];
										
										if ($elementtype == "activity")
										{
											foreach ($currentCriteria as $criteria) {
												if ($criteria == $elementid)
												{
													$inCriteria = 1;
												}
											}
											
											if ($inCriteria == 0)
											{
												echo "<option value='".$elementid."'>".$title." (".$elementname.")</option>";
											}
										}
										
									}
								?>
							</select>
						</div>
					</div>
					
					<input type="hidden" name="courseid" value="<?php echo $id; ?>" />
				</form>

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