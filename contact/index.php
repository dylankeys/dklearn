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
	
    <title><?php echo $sitename;?> | Contact</title>
	
	<!--DK CSS-->
	<link href="../styles.css" rel="stylesheet">
	
	<style>
	html, body {
		width: 100%;
		height: 100%;
	}
	.map-frame {
		position: absolute;
		width: 100%;
		left: 0;
		height:400px;
	}
	.contacts {
		width:100%;
	}
	</style>
	
	<script>
		// Code from https://stackoverflow.com/questions/31593297/using-execcommand-javascript-to-copy-hidden-text-to-clipboard
		function setClipboard(value, id) {
			var tempInput = document.createElement("input");
			tempInput.style = "position: absolute; left: -1000px; top: -1000px";
			tempInput.value = value;
			document.body.appendChild(tempInput);
			tempInput.select();
			document.execCommand("copy");
			document.body.removeChild(tempInput);
			document.getElementById("confirm" + id).innerHTML = "&nbsp;&nbsp;&nbsp;Email copied!";
		}
  </script>
	
	</head>

	<body>

		<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: <?php echo $theme;?>;">
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
			  <li class="nav-item active">
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

		<div class="container">

			<div class="map-frame">
				<iframe width="100%" height="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.google.com/maps/embed/v1/place?q=place_id:ChIJz_s33b4kYEgR4RoqeXKG_DI&key=AIzaSyCKS0u2JECOB6eaPDeA4uOervS66LTDKYk&maptype=satellite" allowfullscreen></iframe>
			</div>
        
			<div class="contact">	
				<h3 style="text-align:center;">Site contacts</h3><br>
				<div class="row">
					<div class="col-sm-4">
						<div class="card h-100">
							<div class="card-body">
								<h5 class="card-title"><i class="fas fa-graduation-cap"></i>&nbsp;Developer</h5>
								<p class="card-text">Contact the system developer</p>
								<button class="btn btn-primary" id="copyText" onclick="setClipboard('<?php echo $devEmail ?>', '1')">Copy email</button>
								<p class="card-text copy-confirm" id="confirm1">&nbsp;</p>
							</div>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="card h-100">
							<div class="card-body">
								<h5 class="card-title"><i class="fas fa-graduation-cap"></i>&nbsp;Support</h5>
								<p class="card-text">Contact the support team regarding any system queries.</p>
								<button class="btn btn-primary" id="copyText" onclick="setClipboard('<?php echo $supportEmail ?>', '2')">Copy email</button>
								<p class="card-text copy-confirm" id="confirm2">&nbsp;</p>
							</div>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="card h-100">
							<div class="card-body">
								<h5 class="card-title"><i class="fas fa-user"></i>&nbsp;Administration</h5>
								<p class="card-text">Contact the system administrator.</p>
								<button class="btn btn-primary" id="copyText" onclick="setClipboard('<?php echo $adminEmail ?>', '3')">Copy email</button>
								<p class="card-text copy-confirm" id="confirm3">&nbsp;</p>
							</div>
						</div>
					</div>
				</div>
			</div>

      </div>
	  
	  <br><br><br><br><br><br><br><br>
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
