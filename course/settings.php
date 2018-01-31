<?php
	include("../config.php");
	
	if (isset($_POST["submit"]))
	{
		$id=$_POST["id"]; 
		$title=$_POST["courseName"];
		$description=$_POST["description"];
		$topicCount=$_POST["topicCount"];
		$active=$_POST["active"];
		
		$dbQuery=$db->prepare("update courses set `title`=:title, `description`=:description, `topiccount`=:topiccount, `active`=:active where `id`=:id");
		$dbParams=array('id'=>$id, 'title'=>$title, 'description'=>$description, 'topiccount'=>$topicCount, 'active'=>$active);
		$dbQuery->execute($dbParams);
		
		echo "<script>window.location.href = 'view.php?id=".$id."'</script>";
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
		include("../config.php");
		include("../lib.php");
		session_start();
        $userID=$_SESSION["currentUserID"];
		
		if(!has_capability("course:admin",$userID))
		{
			echo "<script>window.location.href = 'index.php?permission=0'</script>";
		}
		
		if (isset($_GET["id"]))
		{
            if($_GET["id"]==null)
            {
                echo "<script>window.location.href = 'index.php?course=noid'</script>";
            }
			$id = $_GET["id"];

			$dbQuery=$db->prepare("select * from courses where id=:id");
			$dbParams=array('id'=>$id);
			$dbQuery->execute($dbParams);

			while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC))
			{
				$title=$dbRow["title"];
				$description=$dbRow["description"];	
				$topiccount=$dbRow["topiccount"];	
				$active=$dbRow["active"];
				
				echo "<title>".$title." | Settings</title>";
			}
		}
		else
		{
			echo "<script>window.location.href = 'index.php?course=noid'</script>";
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
		
            <h1><?php echo $title . " Settings"; ?></h1>
            
			<br>
		<form method="post" action="settings.php">
		<form>
			<div class="form-row">
				<div class="form-group col-md-12">
					<label for="courseName">Course name</label>
					<input type="text" class="form-control" id="courseName" name="courseName" aria-describedby="courseNameHelp" value="<?php echo $title; ?>">
					<small id="courseNameHelp" class="form-text text-muted">Your course name must be between 5-50 characters</small>
				</div>
			</div>
			
			<div class="form-row">
				<div class="form-group col-md-12">
					<label for="description">Course description</label>
					<textarea class="form-control" id="description" name="description" rows="5"><?php echo $description; ?></textarea>
				</div>
			</div>
			
			<div class="form-row">
				<div class="form-group col-md-6">
					<label for="topicCount">Topics</label>
					<input type="number" class="form-control" aria-describedby="topicCountHelp" id="topicCount" value="<?php echo $topiccount; ?>" name="topicCount">
					<small id="topicCountHelp" class="form-text text-muted">Number of topics on this course</small>
				</div>
				
				<div class="form-group col-md-6">
					<label for="active">Active</label>
					<select id="active" name="active" aria-describedby="activeHelp" class="form-control">
						<option value="y" <? if ($active == 'y') { echo "selected"; } ?>>Yes</option>
						<option value="n" <? if ($active == 'n') { echo "selected"; } ?>>No</option>
					</select>
					<small id="activeHelp" class="form-text text-muted">Is this course active?</small>
				</div>
			</div>
			
			<input type="hidden" name="id" value="<?php echo $id; ?>" />
			
			<input class="btn btn-primary" type="submit" name="submit" />
		</form>
		<br>

        </div>
		
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
<?php
	}
?>