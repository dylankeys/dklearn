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
    <link rel="icon" href="images/favicon.ico">

	<?php
		include("config.php");
		include("lib.php");
		session_start();
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
	
    <title><?php echo $sitename;?></title>
	
	<!--DK CSS-->
	<link href="styles.css" rel="stylesheet">
	
	</head>

	<body>

		<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #1E88FF;">
		<!--<nav class="navbar navbar-expand-lg navbar-light bg-light">-->
		  <a class="navbar-brand" href="index.php"><?php echo $sitename;?></a>
		  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		  </button>
		  <div class="collapse navbar-collapse" id="navbarText">
			<ul class="navbar-nav mr-auto">
			  <li class="nav-item active">
				<a class="nav-link" href="#">Home</a>
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
			  <li class="nav-item">
				<?php if (has_capability("site:config",$userID)) { echo '<a class="nav-link" href="settings/">Administration</a>'; } ?>
			  </li>
			</ul>
			<span class="navbar-text">
			
			  <?php
				if (isset($username)) {
					echo "<img src='".$profileimage."' width='28px' alt='Profile Image' class='rounded-circle'>&nbsp;<a href='../profile/'>".$fullname." (<a href='../profile/killSession.php'>Log out</a>)</a>";
				}
				else {
					echo "<a href='login/'>Log in or sign up</a>";
				}
			  ?>
			</span>
		  </div>
		</nav>

		<div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
		  <ol class="carousel-indicators">
			<?php
				$dbQuery=$db->prepare("select * from slideshow");
				$dbQuery->execute();
				$rows = $dbQuery->rowCount();
				
				for($count=0;$count<$rows;$count++)
				{
					if($count == 0){
						echo '<li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>';
					}
					else{
						echo '<li data-target="#carouselExampleIndicators" data-slide-to="'.$count.'"></li>';
					}
				}			
			?>
		  </ol>
		  
		  <div class="carousel-inner">
		  
			<?php
				
				$dbQuery=$db->prepare("select * from slideshow");
				$dbQuery->execute();

				while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC)) {
					$slideid = $dbRow["id"];
					$image = $dbRow["image"];
					$heading = $dbRow["heading"];
					$text = $dbRow["text"];
					
					if ($slideid == 1){
						echo '<div class="carousel-item active">';
					}
					else {
						echo '<div class="carousel-item">';
					}
					echo '<img class="d-block w-100" src="'.$image.'" alt="'.$heading.'">
							<div class="carousel-caption d-none d-md-block">
								<h5>'.$heading.'</h5>
								<p>'.$text.'</p>
							</div>
						</div>';
				}
			?>
		  <!--
		  
			<div class="carousel-item active">
			  <img class="d-block w-100" src="" alt="Slide 1">
			  <div class="carousel-caption d-none d-md-block">
				<h5>Slide 1</h5>
				<p>Lorem ipsum</p>
			  </div>
			</div>
			
			<div class="carousel-item">
			  <img class="d-block w-100" src="" alt="Slide 3">
			  <div class="carousel-caption d-none d-md-block">
				<h5>Slide 3</h5>
				<p>Lorem ipsum</p>
			  </div>
			</div>
		  
		  -->
		  </div>
		  <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
			<span class="carousel-control-prev-icon" aria-hidden="true"></span>
			<span class="sr-only">Previous</span>
		  </a>
		  <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
			<span class="carousel-control-next-icon" aria-hidden="true"></span>
			<span class="sr-only">Next</span>
		  </a>
		</div>
		
		<div class="container">
			
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
