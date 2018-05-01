<?php
	session_start();
	include("../../config.php");
	include("../../lib.php");
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
    <link rel="icon" href="../../images/favicon.ico">

	<?php
        $userID=$_SESSION["currentUserID"];
		
		if(!has_capability("site:config",$userID))
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
		
		if (isset($_POST["submit"]))
		{
			$sitenameUpdate = $_POST["sitename"];
			$themeUpdate = $_POST["theme"];
			$userRegUpdate = $_POST["userReg"];
			
			$dbQuery=$db->prepare("update config set `value`=:sitename where `setting`='sitename'");
			$dbParams = array('sitename'=>$sitenameUpdate);
			$dbQuery->execute($dbParams);
			
			$dbQuery=$db->prepare("update config set `value`=:theme where `setting`='theme'");
			$dbParams = array('theme'=>$themeUpdate);
			$dbQuery->execute($dbParams);
			
			$dbQuery=$db->prepare("update config set `value`=:userReg where `setting`='userregistration'");
			$dbParams = array('userReg'=>$userRegUpdate);
			$dbQuery->execute($dbParams);
			
			redirect("index.php?success=updated");
		}
		
	?>
	
    <title><?php echo $sitename;?> | Settings</title>
	
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
					echo "<span style='float:left;'><img src='".$profileimage."' width='28px' alt='Profile Image' class='rounded-circle'></span><a href='../../profile/'>&nbsp;".$fullname." (<a href='../../profile/killSession.php'>Log out</a>)</a>";
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
		if(isset($_GET["success"]) && $_GET["success"] == "updated")
		{
			echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
					<strong>Success!</strong> Settings updated!
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>';
		}
	  ?>
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="../">Settings</a></li>
				<li class="breadcrumb-item active" aria-current="page">Application configuration</li>
			</ol>
		</nav>
			
		<h1>Application configuration</h1>
		
			<form method="post" action="index.php">
			
				<div class="form-row">
					<div class="form-group col-md-12">
						<label for="sitename">Site name</label>
						<input type="text" class="form-control" id="sitename" name="sitename" pattern=".{1,15}" title="The site name must be between 1-15 characters" aria-describedby="sitenameHelp" value="<?php echo $sitename; ?>">
						<small id="sitenameHelp" class="form-text text-muted">Update the site's name (this displays in the footer, navigation bar and page titles). The site name must be between 1-15 characters.</small>
					</div>
				</div>
				
				<div class="form-row">
					<div class="form-group col-md-12">
						<label for="theme">Navigation Colour</label><br>
						<input type="color" id="theme" name="theme" aria-describedby="themeHelp" value="<?php echo $theme; ?>">
						<small id="themeHelp" class="form-text text-muted">Update the site's navigation bar colour.</small>
					</div>
				</div>
				
				<div class="form-row">
					<div class="form-group col-md-12">
						<label for="userReg">User self-registration</label>
						<select id="userReg" name="userReg" class="form-control" aria-describedby="userRegHelp">
							<option value="1" <?php if ($userReg == "1") { echo "selected"; } ?>>Enabled</option>
							<option value="0" <?php if ($userReg == "0") { echo "selected"; } ?>>Disabled</option>
						</select>
						<small id="userRegHelp" class="form-text text-muted">This setting controls whether a user can self-register onto the system.</small>
					</div>
				</div>
				
				<button type="submit" name="submit" class="btn btn-primary">Update settings</button>
				
			</form>
		
      </div>
	  
	  <footer>
		<p class="copyright"><?php echo $sitename ." | &copy ". date("Y"); ?></p>
		<ul class="v-links">
			<li><a href="../../">Home</a></li>
			<li><a href="../../course">Courses</a></li>
			<li><a href="../../dashboard">Dashboard</a></li>
			<li><a href="../../contact">Contact</a></li>
			<li><a href="../../profile">Profile</a></li>
		</ul>
	  </footer>
		<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/js/bootstrap.min.js" integrity="sha384-a5N7Y/aK3qNeh15eJKGWxsqtnX/wWdSZSKp+81YjTmS15nvnvxKHuzaWwXHDli+4" crossorigin="anonymous"></script>
	</body>
</html>