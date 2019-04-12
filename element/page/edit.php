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
		
		if(!has_capability("site:config",$userID))
		{
			echo "<script>window.location.href = '../../course/index.php?permission=0'</script>";
		}
		
		if(isset($_POST["edit"]))
		{
			$id = $_POST["id"];
			$title = $_POST["pageName"];
			$content = $_POST["pageContent"];
			$visiblity = $_POST["visiblity"];
			
			$dbQuery=$db->prepare("update site_pages set title=:title,content=:content,visible=:visiblity where id=:id");
			$dbParams=array('title'=>$title, 'content'=>$content, 'visiblity'=>$visiblity, 'id'=>$id);
			$dbQuery->execute($dbParams);
			
			echo "<script>window.location.href = '../../settings/sitepages/index.php?success=edited&title=".$title."'</script>";
		}
		else if (isset($_GET["id"]))
		{
			if($_GET["id"]==null)
			{
				echo "<script>window.location.href = '../../settings/sitepages/index.php?success=0'</script>";
			}
			$id = $_GET["id"];
			
			$dbQuery=$db->prepare("select * from site_pages where id=:id");
			$dbParams=array('id'=>$id);
			$dbQuery->execute($dbParams);

			while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC))
			{
				$title=$dbRow["title"];
				$content=$dbRow["content"];
				$visibility=$dbRow["visible"];
			}
		}
		else
		{
			echo "<script>window.location.href = '../../settings/sitepages/index.php?success=0'</script>";
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
	
    <title><?php echo $sitename;?> | Site pages</title>
	
	<!--DK CSS-->
	<link href="../../styles.css" rel="stylesheet">
	
	<!--CKEDITOR JS-->
	<script src="https://cdn.ckeditor.com/4.8.0/standard/ckeditor.js"></script>
	
	</head>

	<body>

		<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: <?php echo $theme;?>;">
		<!--<nav class="navbar navbar-expand-lg navbar-light bg-light">-->
		  <a class="navbar-brand" href="../../"><?php echo $sitename;?></a>
		  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		  </button>
		  <div class="collapse navbar-collapse" id="navbarText">
			<ul class="navbar-nav mr-auto">
			  <li class="nav-item">
				<a class="nav-link" href="../../">Home</a>
			  </li>
			  <li class="nav-item">
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
			  <li class="nav-item active">
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

      <div class="container">
		 <br>
         <h1>Edit site page</h1>
		 <br>
		 
		 <?php
		 
			if (isset($_GET["action"]) && $_GET["action"] == "edit")
            {
		?>
		<form method="post" action="edit.php">
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
					<label for="visiblity">Page visiblity</label>
					<select id="visiblity" name="visiblity" class="form-control">
						<option value="1" <?php if ($visibility == 1) { echo "selected"; } ?>>Show</option>
						<option value="0" <?php if ($visibility == 0) { echo "selected"; } ?>>Hide</option>
					</select>
				</div>
			</div>
			
			<input type="hidden" value="<?php echo $id; ?>" name="id" />
			<input class="btn btn-primary" value="Update page" name="edit" type="submit" />
		</form>
		
		<?php
			}
			else if (isset($_GET["action"]) && $_GET["action"] == "delete")
            {
				$dbQuery=$db->prepare("delete from site_pages where id=:id");
				$dbParams=array('id'=>$id);
				$dbQuery->execute($dbParams);
				
				echo "<script>window.location.href = '../../settings/sitepages/index.php?success=deleted'</script>";
			}
			else if (isset($_GET["action"]) && $_GET["action"] == "Hide")
            {
				$dbQuery=$db->prepare("update site_pages set visible='0' where id=:id");
				$dbParams=array('id'=>$id);
				$dbQuery->execute($dbParams);
				
				echo "<script>window.location.href = '../../settings/sitepages/index.php?success=hidden'</script>";
			}
			else if (isset($_GET["action"]) && $_GET["action"] == "Show")
            {
				$dbQuery=$db->prepare("update site_pages set visible='1' where id=:id");
				$dbParams=array('id'=>$id);
				$dbQuery->execute($dbParams);
				
				echo "<script>window.location.href = '../../settings/sitepages/index.php?success=visible'</script>";
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
