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
		
		if(isset($_POST["edit"]))
		{
			$id=$_POST["id"];
			$element=$_POST["element"];
			$table_name=$_POST["tablename"];
			
			if ($element == "assignment")
			{
				$title=$_POST["name"];
				$brief=$_POST["brief"];
				$deadline=$_POST["deadline"];
				$pass=$_POST["pass"];
				$visible=$_POST["visible"];
				
				$query = "title=:title,brief=:brief,deadline=:deadline,pass=:pass,visible=:visible";
				$dbParams=array('id'=>$id, 'title'=>$title, 'brief'=>$brief, 'deadline'=>$deadline, 'pass'=>$pass, 'visible'=>$visible);
				$redirect = "../element/assignment/view.php?id=" . $id;
			}
			else if ($element == "page")
			{
				$title=$_POST["pageName"];
				$content=$_POST["pageContent"];
				$visible=$_POST["visible"];
				
				$query = "title=:title,content=:content,visible=:visible";
				$dbParams=array('id'=>$id, 'title'=>$title, 'content'=>$content, 'visible'=>$visible);
				$redirect = "../element/page/view.php?id=" . $id;
			}
			else if ($element == "quiz")
			{
				$title=$_POST["title"];
				$summary=$_POST["summary"];
				$start=$_POST["open"];
				$end=$_POST["close"];
				$visible=$_POST["visible"];
				$pass=$_POST["pass"];
				
				$query = "title=:title,summary=:summary,start=:start,end=:end,visible=:visible,pass=:pass";
				$dbParams=array('id'=>$id, 'title'=>$title, 'summary'=>$summary, 'start'=>$start, 'end'=>$end, 'visible'=>$visible, 'pass'=>$pass);
				$redirect = "../element/quiz/view.php?id=" . $id;
			}
			
			$dbQuery=$db->prepare("update ".$table_name." set ".$query." where id=:id");
			$dbQuery->execute($dbParams);
			
			regrade($id);
			redirect($redirect);
		}
		else if (isset($_GET["id"]) && isset($_GET["element"]))
		{
			if($_GET["id"]==null || $_GET["element"]==null)
			{
				error("Page variables not set","../");
			}
			$id = $_GET["id"];
			$element = $_GET["element"];
			
			$dbQuery=$db->prepare("select tablename from elements_type where name=:element");
			$dbParams=array('element'=>$element);
			$dbQuery->execute($dbParams);
			$dbRow=$dbQuery->fetch(PDO::FETCH_ASSOC);
			$tablename=$dbRow["tablename"];
			
			$dbQuery=$db->prepare("select * from " . $tablename . " where id=:id");
			$dbParams=array('id'=>$id);
			$dbQuery->execute($dbParams);
			
			while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC))
			{
				$courseid=$dbRow["courseid"];
				
				if ($element == "assignment")
				{
					$title=$dbRow["title"];
					$brief=$dbRow["brief"];
					$deadline=$dbRow["deadline"];
					$pass=$dbRow["pass"];
					$visible=$dbRow["visible"];
				}
				else if ($element == "page")
				{
					$title=$dbRow["title"];
					$content=$dbRow["content"];
					$visible=$dbRow["visible"];
				}
				else if ($element == "quiz")
				{
					$title=$dbRow["title"];
					$summary=$dbRow["summary"];
					$start=$dbRow["start"];
					$end=$dbRow["end"];
					$pass=$dbRow["pass"];
					$visible=$dbRow["visible"];
				}
			}
		}
		else
		{
			error("Page variables not set","../");
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

        while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC))
        {
           $username=$dbRow["username"];
		   $fullname=$dbRow["fullname"];
		   $profileimage=$dbRow["profileimage"];
        }
	?>
	
    <title><?php echo $sitename;?> | Edit element</title>
	
	<!--DK CSS-->
	<link href="../styles.css" rel="stylesheet">
	
	<!--CKEDITOR JS-->
	<script src="https://cdn.ckeditor.com/4.8.0/standard/ckeditor.js"></script>
	
	</head>

	<body>

		<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: <?php echo $theme;?>;">
		<!--<nav class="navbar navbar-expand-lg navbar-light bg-light">-->
		  <a class="navbar-brand" href="../"><?php echo $sitename;?></a>
		  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		  </button>
		  <div class="collapse navbar-collapse" id="navbarText">
			<ul class="navbar-nav mr-auto">
			  <li class="nav-item">
				<a class="nav-link" href="../">Home</a>
			  </li>
			  <li class="nav-item">
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
			  <li class="nav-item active">
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
		<br>
		<h1>Edit <?php echo $element; ?></h1>
		<br>
		
		<form method="post" action="editElement.php">
		<?php
		if ($element == "assignment")
		{
		?>
			<div class="form-row">
				<div class="form-group col-md-12">
					<label for="name">Assignment name</label>
					<input type="text" class="form-control" id="name" name="name" placeholder="Page name" value="<?php echo $title; ?>">
				</div>
			</div>
			
			<div class="form-row">
				<div class="form-group col-md-12">
					<label for="brief">Assignment brief</label>
					<textarea class="form-control" id="brief" name="brief" rows="10"><?php echo $brief; ?></textarea>
					
					<script>
						CKEDITOR.replace( 'brief' );
					</script>
				</div>
			</div>
			
			<div class="form-row">
				<div class="form-group col-md-6">
					<label for="deadline">Assignment deadline</label>
					<input type="text" id="deadline" name="deadline" class="form-control" value="<?php echo $deadline; ?>">
				</div>
				
				<div class="form-group col-md-6">
					<label for="pass">Grade to pass</label>
					<input type="text" id="pass" name="pass" class="form-control" value="<?php echo $pass; ?>">
				</div>
			</div>
			
			<div class="form-row">
				<div class="form-group col-md-12">
					<label for="visible">Assignment visiblity</label>
					<select id="visible" name="visible" class="form-control">
						<option value="1" <?php if ($visible == 1) { echo "selected"; } ?>>Show</option>
						<option value="0" <?php if ($visible == 0) { echo "selected"; } ?>>Hide</option>
					</select>
				</div>
			</div>
		<?php
		}
		else if ($element == "page")
		{
		?>
			<div class="form-row">
				<div class="form-group col-md-12">
					<label for="pageName">Page name</label>
					<input type="text" class="form-control" id="pageName" name="pageName" value="<?php echo $title; ?>">
				</div>
			</div>
			
			<div class="form-row">
				<div class="form-group col-md-12">
					<label for="pageContent">Page content</label>
					<textarea class="form-control" id="pageContent" name="pageContent" rows="10"><?php echo $content; ?></textarea>
					
					<script>
						CKEDITOR.replace( 'pageContent' );
					</script>
				</div>
			</div>
			
			<div class="form-row">
				<div class="form-group col-md-12">
					<label for="visible">Page visiblity</label>
					<select id="visible" name="visible" class="form-control">
						<option value="1" <?php if ($visible == 1) { echo "selected"; } ?>>Show</option>
						<option value="0" <?php if ($visible == 0) { echo "selected"; } ?>>Hide</option>
					</select>
				</div>
			</div>
		<?php
		}
		else if ($element == "quiz")
		{
		?>
			<div class="form-row">
				<div class="form-group col-md-12">
					<label for="title">Quiz name</label>
					<input type="text" class="form-control" id="title" name="title" value="<?php echo $title; ?>">
				</div>
			</div>
			
			<div class="form-row">
				<div class="form-group col-md-12">
					<label for="summary">Quiz summary</label>
					<textarea class="form-control" id="summary" name="summary" rows="5"><?php echo $summary; ?></textarea>
					
					<script>
						CKEDITOR.replace( 'summary' );
					</script>
				</div>
			</div>
			
			<!-- Quiz open/close date and time -->
			
			<div class="form-row">
				<div class="form-group col-md-6">
					<label for="open">Open date/time</label>
					<input type="text" id="open" name="open" value="<?php echo $start; ?>" class="form-control">
				</div>
	
				<div class="form-group col-md-6">
					<label for="close">Close date/time</label>
					<input type="text" id="close" name="close" value="<?php echo $end; ?>" class="form-control">
				</div>
			</div>
			
			<div class="form-row">
				<div class="form-group col-md-6">
					<label for="visible">Page visiblity</label>
					<select id="visible" name="visible" class="form-control">
						<option value="1" <?php if ($visible == 1) { echo "selected"; } ?>>Show</option>
						<option value="0" <?php if ($visible == 0) { echo "selected"; } ?>>Hide</option>
					</select>
				</div>
	
				<div class="form-group col-md-6">
					<label for="pass">Percentage to pass</label>
					<input type="number" id="pass" name="pass" value="<?php echo $pass; ?>" class="form-control">
				</div>
			</div>
		<?php
		}
		?>
			<input type="hidden" value="<?php echo $id; ?>" name="id" />
			<input type="hidden" value="<?php echo $element; ?>" name="element" />
			<input type="hidden" value="<?php echo $tablename; ?>" name="tablename" />
			<input class="btn btn-primary" name="edit" type="submit" />
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
