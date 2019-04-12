<?php
	session_start();
	include("config.php");
	include("lib.php");
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
    <link rel="icon" href="images/favicon.ico">

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
	
    <title><?php echo $sitename;?></title>
	
	<!--DK CSS-->
	<link href="styles.css" rel="stylesheet">
	
	</head>

	<body>

		<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: <?php echo $theme;?>;">
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

			<div class="search">
				<form method="get" action="courses/index.php">
						<div class="form-row">
							<div class="form-group col-md-11">
								<input class="form-control form-control-lg" type="text" name="search" placeholder="Search for courses">
							</div>
							
							<div class="form-group col-md-1">
								<button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-search"></i></button>
							</div>
						</div>
				</form>
			</div>

			<div class="row">
				<div class="col-sm-4">
					<div class="card h-100">
						<div class="card-body">
							<h5 class="card-title"><i class="fas fa-graduation-cap"></i>&nbsp;&nbsp;Course library</h5>
							<p class="card-text">View the full list of courses. Note, an account is required to access courses.</p>
							<a href="course/" class="btn btn-primary">View</a>
						</div>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="card h-100">
						<div class="card-body">
							<h5 class="card-title"><i class="fas fa-graduation-cap"></i>&nbsp;&nbsp;Contact the team</h5>
							<p class="card-text">Click below to access the contact information for our team.</p>
							<a href="contact/" class="btn btn-primary">View</a>
						</div>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="card h-100">
						<div class="card-body">
							<?php
								if (isset($_SESSION["currentUserID"]))
								{
							?>

							<h5 class="card-title"><i class="fas fa-user"></i>&nbsp;&nbsp;Profile</h5>
							<p class="card-text">Click below to view and manage your personal profile.</p>
							<a href="profile/" class="btn btn-primary">View</a>

							<?php
								}
								else {
							?>

							<h5 class="card-title"><i class="fas fa-user"></i>&nbsp;&nbsp;Register / Log in</h5>
							<p class="card-text">Click below to sign in or register.</p>
							<a href="login/" class="btn btn-primary">View</a>

							<?php
								}
							?>
						</div>
					</div>
				</div>
			</div>

			<div class="site-news">
				<h1>Site news</h1>

				<div class="row">
					<div class="col-4">
						<div class="list-group" id="list-tab" role="tablist">
							<?php

								$dbQuery=$db->prepare("select * from site_news where `visible`='1' order by `id` asc");
								$dbQuery->execute();

								$active = " active";

								while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC)) {
									$postid = $dbRow["id"];
									$heading = $dbRow["heading"];
									$fullcontent = $dbRow["content"];
									$postedtime = $dbRow["postedtime"];
									$postedby = $dbRow["postedby"];
									$visible = $dbRow["visible"];

									$summary = substr($fullcontent,0,95);

									$summary = $summary . "...";

									if ($visible == "1")
									{
										echo '<a class="list-group-item list-group-item-action flex-column align-items-start'.$active.'" id="list-'.$postid.'-list" data-toggle="list" href="#list-'.$postid.'" role="tab" aria-controls="'.$postid.'">
														<div class="d-flex w-100 justify-content-between">
															<h5 class="mb-1">'.$heading.'</h5>
															<small>'.$postedtime.'</small>
														</div>
														<p class="mb-1">'.$summary.'</p>
														<small>Posted by '.$postedby.'</small>
													</a>';

										$active = "";
									}
								}

							?>
						</div>
					</div>
					<div class="col-8">
						<div class="tab-content" id="nav-tabContent">
							<?php
								$dbQuery=$db->prepare("select * from site_news where `visible`='1' order by `id` asc");
								$dbQuery->execute();

								$activeContent = " active";

								while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC)) {
									$contentid = $dbRow["id"];
									$content = $dbRow["content"];
									$contentvis = $dbRow["visible"];

									if ($contentvis == "1")
									{
										echo '<div class="tab-pane fade show'.$activeContent.'" id="list-'.$contentid.'" role="tabpanel" aria-labelledby="list-'.$contentid.'-list">'.$content.'</div>';

										$activeContent = "";
									}
								}
							?>
						</div>
					</div>
				</div>
			</div>

		</div>
	  
	  <footer>
		<p class="copyright"><?php echo $sitename ." | &copy ". date("Y"); ?></p>
		<ul class="v-links">
			<li><a href="index.php">Home</a></li>
			<li><a href="course/">Courses</a></li>
			<li><a href="dashboard/">Dashboard</a></li>
			<li><a href="contact/">Contact</a></li>
			<li><a href="profile/">Profile</a></li>
		</ul>
	  </footer>
		<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/js/bootstrap.min.js" integrity="sha384-a5N7Y/aK3qNeh15eJKGWxsqtnX/wWdSZSKp+81YjTmS15nvnvxKHuzaWwXHDli+4" crossorigin="anonymous"></script>
	</body>
</html>
