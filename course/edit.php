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

	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../images/favicon.ico">

	<?php
        $userID=$_SESSION["currentUserID"];
		
		if (isset($_POST["deleteid"]))
		{
			$deleteid = $_POST["deleteid"];
			$courseid = $_POST["courseid"];
			
			$dbQuery=$db->prepare("delete from topics where id=:deleteid");
			$dbParams=array('deleteid'=>$deleteid);
			$dbQuery->execute($dbParams);
			
			$dbQuery=$db->prepare("select elementid from topic_content where topicid=:deleteid");
			$dbParams=array('deleteid'=>$deleteid);
			$dbQuery->execute($dbParams);
			
			while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC))
			{
				$elementid=$dbRow["elementid"];
			
				$dbQueryTopicContent=$db->prepare("delete from topic_content where topicid=:deleteid");
				$dbParamsTopicContent=array('deleteid'=>$deleteid);
				$dbQueryTopicContent->execute($dbParamsTopicContent);
				
				$dbQueryElementContent=$db->prepare("select * from elements where id=:elementid");
				$dbParamsElementContent=array('elementid'=>$elementid);
				$dbQueryElementContent->execute($dbParamsElementContent);
				
				while ($dbRowElementContent = $dbQueryElementContent->fetch(PDO::FETCH_ASSOC))
				{
					$typeid=$dbRowElementContent["typeid"];
					$contentid=$dbRowElementContent["contentid"];
					
					$dbQueryDeleteElement=$db->prepare("delete from elements where id=:elementid");
					$dbParamsDeleteElement=array('elementid'=>$elementid);
					$dbQueryDeleteElement->execute($dbParamsDeleteElement);
					
					$dbQueryElementType=$db->prepare("select tablename from elements_type where id=:typeid");
					$dbParamsElementType=array('typeid'=>$typeid);
					$dbQueryElementType->execute($dbParamsElementType);
				
					while ($dbRowElementType = $dbQueryElementType->fetch(PDO::FETCH_ASSOC))
					{
						$tablename=$dbRowElementType["tablename"];
						
						$dbQueryElementContentDelete=$db->prepare("delete from ".$tablename." where id=:contentid");
						$dbParamsElementContentDelete=array('contentid'=>$contentid);
						$dbQueryElementContentDelete->execute($dbParamsElementContentDelete);
					}
				}
			}
			redirect("view.php?id=".$courseid);
		}
		else if (isset($_POST["deleteElement"]))
		{
			$topicid = $_POST["topicid"];
			$courseid = $_POST["courseid"];
			$elementid = $_POST["elementid"];
			
			redirect("../function/deleteElement.php?topicid=".$topicid."&courseid=".$courseid."&elementid=".$elementid);
		}
		else if (isset($_POST["editid"]))
		{
			$topicid = $_POST["editid"];
			$courseid = $_POST["courseid"];
			$order = $_POST["order"];
			$name = $_POST["header"];
			$summary = $_POST["summary"];
			$visible = $_POST["visible"];
			
			$dbQuery=$db->prepare("update `topics` set `name`=:name, `summary`=:summary, `order`=:order, `visible`=:visible where `id`=:topicid");
			$dbParams=array('topicid'=>$topicid, 'order'=>$order, 'name'=>$name, 'summary'=>$summary, 'visible'=>$visible);
			$dbQuery->execute($dbParams);
						
			redirect("view.php?id=".$courseid);
		}
		else if (isset($_GET["topicid"]) && isset($_GET["action"]) && isset($_GET["courseid"]))
		{
            if($_GET["topicid"]==null || $_GET["action"]==null || $_GET["courseid"]==null)
            {
                error("No course ID has been supplied in the URL", "../");
            }
			$topicid = $_GET["topicid"];
			$action = $_GET["action"];
			$courseid = $_GET["courseid"];
			
			if(isset($_GET["elementid"]))
			{
				$elementid = $_GET["elementid"];
			}
			
			if ($action == "edit")
			{
				$dbQuery=$db->prepare("select * from topics where id=:topicid");
				$dbParams=array('topicid'=>$topicid);
				$dbQuery->execute($dbParams);

				while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC))
				{
					$order=$dbRow["order"];
					$name=$dbRow["name"];	
					$summary=$dbRow["summary"];	
					$visible=$dbRow["visible"];
					
					echo "<title>".$name." | Edit</title>";
				}
			}
			else
			{
				echo "<title>".$sitename." | Delete</title>";
			}
		}
		else
		{
			error("No course ID has been supplied in the URL", "../");
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
		$rowCount = $dbQuery->rowCount();
		
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
			if ($action == "delete")
			{
				echo '<h3>Are you sure?</h3>';
				echo '<p>This topic will be deleted. All included elements will also be deleted.</p>';
				echo '<form class="confirm-delete" method="post" action="edit.php">
						<input type="hidden" name="deleteid" value="'.$topicid.'" />
						<input type="hidden" name="courseid" value="'.$courseid.'" />
						<button type="submit" class="btn btn-success">Yes</button>
					</form>';
				echo '<button type="button" onclick="window.location.href=\'view.php?id='.$courseid.'\'" class="btn btn-danger confirm-delete">No</button>';
			}
			else if ($action == "deleteElement")
			{
				echo '<h3>Are you sure?</h3>';
				echo '<p>This course element will be deleted from the system.</p>';
				echo '<form class="confirm-delete" method="post" action="edit.php">
						<input type="hidden" name="topicid" value="'.$topicid.'" />
						<input type="hidden" name="courseid" value="'.$courseid.'" />
						<input type="hidden" name="elementid" value="'.$elementid.'" />
						<input type="hidden" name="deleteElement" />
						<button type="submit" class="btn btn-success">Yes</button>
					</form>';
				echo '<button type="button" onclick="window.location.href=\'view.php?id='.$courseid.'\'" class="btn btn-danger confirm-delete">No</button>';
			}
			else
			{
		?>
		
            <h1><?php echo $name . " Settings"; ?></h1>
            
			<br>
			
			<form method="post" action="edit.php">
			<form>
				<div class="form-row">
					<div class="form-group col-md-12">
						<label for="header">Topic header</label>
						<input type="text" class="form-control" id="header" name="header" value="<?php echo $name; ?>">
					</div>
				</div>
				
				<div class="form-row">
					<div class="form-group col-md-12">
						<label for="summary">Topic summary</label>
						<textarea class="form-control" id="summary" name="summary" rows="5"><?php echo $summary; ?></textarea>
					</div>
				</div>
				
				<div class="form-row">
					<div class="form-group col-md-6">
						<label for="order">Order</label>
						<input type="number" class="form-control" id="order" name="order" value="<?php echo $order; ?>">
					</div>
					
					<div class="form-group col-md-6">
						<label for="visible">Visibility</label>
						<select id="visible" name="visible" class="form-control">
							<option value="1" <? if ($visible == '1') { echo "selected"; } ?>>Show</option>
							<option value="0" <? if ($visible == '0') { echo "selected"; } ?>>Hide</option>
						</select>
					</div>
				</div>
				
				<input type="hidden" name="editid" value="<?php echo $topicid; ?>" />
				<input type="hidden" name="courseid" value="<?php echo $courseid; ?>" />
				
				<input class="btn btn-primary" type="submit" name="submit" />
			</form>
			
			<br>
		
		<?php
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