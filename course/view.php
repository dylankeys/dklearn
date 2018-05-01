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

			if (isset($_GET["id"]))
			{
                if($_GET["id"]==null)
                {
                    error("Invalid URL. No course ID set.","../");
                }
				$currentCourseID = $_GET["id"];
				
                if (!isset($_SESSION["currentUserID"]))
                {
                    redirect("../login/index.php?failCode=3&courseid=". $currentCourseID);
                }

				$dbQuery=$db->prepare("select * from courses where id=:id");
				$dbParams=array('id'=>$currentCourseID);
				$dbQuery->execute($dbParams);

				while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC))
				{
					$title=$dbRow["title"];

					echo "<title>".$sitename." | $title</title>";
				}
			}
			else
			{
				error("Invalid URL. No course ID set.","../");
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

			$dbQuery=$db->prepare("select * from courses where id=:id");
			$dbParams=array('id'=>$currentCourseID);
			$dbQuery->execute($dbParams);

   			while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC)) {
                $title = $dbRow["title"];
                $description = $dbRow["description"];
                $usemedia = $dbRow["usemedia"];
                $media = $dbRow["media"];
				
                echo "<h1>".$title."</h1>";
				
                $dbQueryEnrolments = $db->prepare("select * from enrolments where userID=:userID AND courseID=:courseID");
                $dbParamsEnrolments = array('userID' => $userID, 'courseID' => $currentCourseID);
                $dbQueryEnrolments->execute($dbParamsEnrolments);
                $rows = $dbQueryEnrolments->rowCount();

                if ($rows>0 || has_capability("course:admin",$userID)) {
					
					$dbRowEnrolments=$dbQueryEnrolments->fetch(PDO::FETCH_ASSOC);
					$role = $dbRowEnrolments["role"];
					
					echo '<div class="course-btn">';
						if(has_capability("course:admin",$userID) || $role == "teacher") { echo '<button type="button" class="btn btn-primary btn-sm" onclick="window.location.href=\'settings.php?id='.$currentCourseID.'\'">Settings</button>&nbsp;'; }
						if(has_capability("course:admin",$userID) || $role == "teacher") { echo '<button type="button" class="btn btn-primary btn-sm" onclick="window.location.href=\'enrolments.php?id='.$currentCourseID.'\'">Enrolments</button>&nbsp;'; }
						if(has_capability("course:admin",$userID) || $role == "teacher") { echo '<button type="button" class="btn btn-primary btn-sm" onclick="window.location.href=\'completion.php?id='.$currentCourseID.'\'">Completion</button>&nbsp;'; }
						if(!has_capability("course:admin",$userID) || $role == "teacher") { echo '<button type="button" class="btn btn-primary btn-sm" onclick="window.location.href=\'unenrol.php?courseid='.$currentCourseID.'&userid='.$userID.'\'">Unenrol</button>&nbsp;'; }
					echo '</div>';
					
					echo "<div class='course-desc'>".$description."</div>";
					
					if(!has_capability("course:admin",$userID) && $role != "teacher")
					{
						$courseProgress = checkProgress($currentCourseID, $userID);
					
						echo '<br><div class="progress" style="height: 15px;">
								<div class="progress-bar" role="progressbar" style="width: '.$courseProgress.'%;" aria-valuenow="'.$courseProgress.'" aria-valuemin="0" aria-valuemax="100">'.$courseProgress.'%</div>
							</div>';
					}
									
					$dbQueryTopics = $db->prepare("select * from topics where `courseid`=:courseID order by `order`");
					$dbParamsTopics = array('courseID' => $currentCourseID);
					$dbQueryTopics->execute($dbParamsTopics);
					
					while ($dbRowTopics = $dbQueryTopics->fetch(PDO::FETCH_ASSOC)) {
						
						$topicid = $dbRowTopics["id"];
						$title = $dbRowTopics["name"];
						$summary = $dbRowTopics["summary"];
						$visible = $dbRowTopics["visible"];
												
						if ($visible == 1)
						{
							echo '<br><div class="p-3 mb-2 bg-light text-dark">
								<h3>'.$title.'</h3>
								<p>'.$summary.'</p>
								<ul>';							
									$dbQueryTopicContent = $db->prepare("select elementid from `topic_content` where `topicid`=:topicid");
									$dbParamsTopicContent = array('topicid' => $topicid);
									$dbQueryTopicContent->execute($dbParamsTopicContent);
									
									while ($dbRowTopicContent = $dbQueryTopicContent->fetch(PDO::FETCH_ASSOC)) {
										$elementid = $dbRowTopicContent["elementid"];
										
										$dbQueryElement=$db->prepare("select * from elements where id=:elementid");
										$dbParamsElement=array('elementid'=>$elementid);
										$dbQueryElement->execute($dbParamsElement);
										
										while ($dbRowElement = $dbQueryElement->fetch(PDO::FETCH_ASSOC)) {
											$typeid = $dbRowElement["typeid"];
											$contentid = $dbRowElement["contentid"];
											
											$dbQueryElementType=$db->prepare("select * from elements_type where id=:typeid");
											$dbParamsElementType=array('typeid'=>$typeid);
											$dbQueryElementType->execute($dbParamsElementType);
											
											while ($dbRowElementType = $dbQueryElementType->fetch(PDO::FETCH_ASSOC)) {
												$name = $dbRowElementType["name"];
												$tablename = $dbRowElementType["tablename"];
												
												$dbQueryElementContent=$db->prepare("select * from ".$tablename." where id=:contentid");
												$dbParamsElementContent=array('contentid'=>$contentid);
												$dbQueryElementContent->execute($dbParamsElementContent);
												
												while ($dbRowElementContent = $dbQueryElementContent->fetch(PDO::FETCH_ASSOC)) {
													$id = $dbRowElementContent["id"];
													$title = $dbRowElementContent["title"];
													$visible = $dbRowElementContent["visible"];
													
													$icon = "";
													
													switch ($name) {
														case "assignment":
															$icon = '<i class="fas fa-clipboard-list"></i>&nbsp;&nbsp;&nbsp;';
															break;
														case "quiz":
															$icon = '<i class="fas fa-question"></i>&nbsp;&nbsp;&nbsp;';
															break;
														case "file":
															$icon = '<i class="far fa-file"></i>&nbsp;&nbsp;&nbsp;';
															break;
														case "link":
															$icon = '<i class="fas fa-link"></i>&nbsp;&nbsp;&nbsp;';
															break;
														case "page":
															$icon = '<i class="far fa-file-alt"></i>&nbsp;&nbsp;&nbsp;';
															break;
														case "video":
															$icon = '<i class="fab fa-youtube"></i>&nbsp;&nbsp;&nbsp;';
															break;
														default:
															$icon = '<i class="fas fa-circle"></i>&nbsp;&nbsp;&nbsp;';
													}
													
													if ($visible == 1)
													{
														if ($name == "video")
														{
															$embed = $dbRowElementContent["embed"];

															echo "<li class='course-element'>".$icon.$title;
															if(has_capability("course:admin",$userID) || $role == "teacher") { echo "<span class='course-icons'><a href='edit.php?topicid=".$topicid."&action=deleteElement&elementid=".$elementid."&courseid=".$currentCourseID."'><i class='far fa-trash-alt'></i></a></span>"; }
															echo "</li>";

															echo "<li class='no-bullet'>".$embed."</li>";
														}
														else if ($name == "link")
														{
															$embed = $dbRowElementContent["embed"];

															echo "<li class='course-element'>".$icon.$embed;
															if(has_capability("course:admin",$userID) || $role == "teacher") { echo "<span class='course-icons'><a href='edit.php?topicid=".$topicid."&action=deleteElement&elementid=".$elementid."&courseid=".$currentCourseID."'><i class='far fa-trash-alt'></i></a></span>"; }
															echo "</li>";
														}
														else
														{
															echo "<li class='course-element'>".$icon."<a href='../element/".$name."/view.php?id=".$id."'>".$title."</a>";
															if(has_capability("course:admin",$userID) || $role == "teacher") { echo "<span class='course-icons'><a href='../function/editElement?id=".$id."&element=".$name."'><i class='far fa-edit'></i></a>&nbsp;<a href='edit.php?topicid=".$topicid."&action=deleteElement&elementid=".$elementid."&courseid=".$currentCourseID."'><i class='far fa-trash-alt'></i></a></span>"; }
															echo "</li>";
														}
													}
													else if ($visible == 0 && (has_capability("course:admin",$userID) || $role == "teacher"))
													{
														if ($name == "video")
														{
															$embed = $dbRowElementContent["embed"];

															echo "<li class='dimmed course-element'>".$icon.$title."<br>".$embed;
															if(has_capability("course:admin",$userID) || $role == "teacher") { echo "<span class='course-icons'><a href='../function/editElement?id=".$id."&element=".$name."'><i class='far fa-edit'></i></a>&nbsp;<a href='edit.php?topicid=".$topicid."&action=deleteElement&elementid=".$elementid."&courseid=".$currentCourseID."'><i class='far fa-trash-alt'></i></a></span>"; }
															echo "</li>";
														}
														else if ($name == "link")
														{
															$embed = $dbRowElementContent["embed"];

															echo "<li class='dimmed course-element'>".$icon.$embed;
															if(has_capability("course:admin",$userID) || $role == "teacher") { echo "<span class='course-icons'><a href='edit.php?topicid=".$topicid."&action=deleteElement&elementid=".$elementid."&courseid=".$currentCourseID."'><i class='far fa-trash-alt'></i></a></span>"; }
															echo "</li>";
														}
														else
														{
															echo "<li class='dimmed course-element'>".$icon."<a href='../element/".$name."/view.php?id=".$id."'>".$title."</a>";
															if(has_capability("course:admin",$userID) || $role == "teacher") { echo "<span class='course-icons'><a href='../function/editElement?id=".$id."&element=".$name."'><i class='far fa-edit'></i></a>&nbsp;<a href='edit.php?topicid=".$topicid."&action=deleteElement&elementid=".$elementid."&courseid=".$currentCourseID."'><i class='far fa-trash-alt'></i></a></span>"; }
															echo "</li>";
														}
													}
												}
											}
										}
									}
									
							echo '</ul>';
							
							if (has_capability("course:admin",$userID) || $role == "teacher") {
								echo '<div style="float:right"><a href="edit.php?topicid='.$topicid.'&action=edit&courseid='.$currentCourseID.'">Edit</a> | <a href="edit.php?topicid='.$topicid.'&action=delete&courseid='.$currentCourseID.'">Delete</a> | <a href="add.php?action=element&courseid='.$currentCourseID.'&topicid='.$topicid.'">Add an activity or resource to this topic</a></div><br>';
							}
							
							echo '</div>';
						}
					}
					if(has_capability("course:admin",$userID) || $role == "teacher") { echo '<br><a href="add.php?action=topic&courseid='.$currentCourseID.'">Add a topic</a>'; }
					
                } else {
					echo "<div class='course-desc'>$description";
					
                    echo "<form name='enrol' method='post' action='enrol.php'>";
                    echo '<input type="hidden" name="courseID" value="' . $currentCourseID . '">';
                    echo '<input type="hidden" name="userID" value="' . $userID . '">';
                    echo '<br><p style="color:red"><b>You are not enroled on this course!</b></p>';
                    echo '<p>If you\'d like to access this course, please enrol using the button below</p><br>';
                    echo '<input type="submit" value="Enrol" class="btn btn-default" role="button">';
                    echo "</form>";
					
					echo "</div>";
                }
            }

		?>
		
	  </div>
		<br>
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
