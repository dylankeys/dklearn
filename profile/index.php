<?php
    session_start();
    include ("../config.php");
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
		
		if (!isset($_SESSION["currentUserID"]))
        {
            redirect("../login/");
        }
		
		$dbQuery=$db->prepare("select * from users where id=:id");
        $dbParams = array('id'=>$userID);
        $dbQuery->execute($dbParams);
        //$dbRow=$dbQuery->fetch(PDO::FETCH_ASSOC);

        while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC))
        {
			$profileimage=$dbRow["profileimage"];
			$username=$dbRow["username"];
			$fullname=$dbRow["fullname"];
			$password=$dbRow["password"];
			$email=$dbRow["email"];
			$bio=$dbRow["bio"];
			$country=$dbRow["country"];
			$dob=$dbRow["dob"];
			$timestamp=$dbRow["lastlogin"];
        }
		
		$lastlogin = date("Y-m-d H:i:s", $timestamp);
	?>
	
    <title><?php echo $username;?> | Profile</title>
	
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
			  <li class="nav-item">
				<a class="nav-link" href="../course/">Courses</a>
			  </li>
			  <li class="nav-item">
				<a class="nav-link" href="../dashboard/">Dashboard</a>
			  </li>
			  <li class="nav-item">
				<a class="nav-link" href="../contact/">Contact</a>
			  </li>
			  <li class="nav-item active">
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

		<div class="container">

			<br>

			<?php
				echo "<h5 style='float:left; vertical-align:middle;'>Logged in as ". $username ."</h5>";
			?>

			<button type="button" class="btn btn-primary btn-sm" style="float:right" onclick="window.location.href='killSession.php'">Log off</button>

			<br><br>
			
			<ul class="nav nav-tabs">
			  <li class="nav-item">
				<a class="nav-link active" href="../profile">Profile</a>
			  </li>
			  <li class="nav-item">
				<a class="nav-link" href="edit.php">Edit</a>
			  </li>
			</ul>
			
			<br>
		  
			<h1><?php echo $fullname; ?></h1>
			
			<div class="profile-left">
				<img src="<?php echo $profileimage; ?>" alt="Profile Image" class="rounded profile-image">
			
				<br>
				<p>Last login: <strong><?php echo $lastlogin; ?></strong></p>
			</div>
			
			<div class="profile-right">
				
				<div class="p-3 mb-2 bg-light text-dark">
					<strong>Bio</strong><br>
					<?php echo $bio; ?>
				</div>
				
				<table class="table">
					<tbody>
						<tr>
							<th scope="row">Email</th>
							<td><?php echo $email; ?></td>
						</tr>
						<tr>
							<th scope="row">Country</th>
							<td><?php echo $country; ?></td>
						</tr>
						<tr>
							<th scope="row">Date of birth</th>
							<td><?php echo $dob; ?></td>
						</tr>
					</tbody>
				</table>
			</div>
			<br>
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
