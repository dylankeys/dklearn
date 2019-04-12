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
	<script defer src="https://use.fontawesome.com/releases/v5.0.8/js/all.js" integrity="sha384-SlE991lGASHoBfWbelyBPLsUlwY1GwNDJo3jSJO04KZ33K2bwfV9YBauFfnzvynJ" crossorigin="anonymous"></script>
	
	<!-- Chart.js scripts -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>
	

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
	?>
	
    <title><?php echo $sitename;?> | Reports</title>
	
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
			  <li class="nav-item active">
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
			  <li class="nav-item">
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
		<br>

        <div class="container">
		
			<h1>System Reports</h1>
			<br>
		
			<div class="row">
				<div class="col-sm-12">
					<div class="card h-100">
						<div class="card-body">
							<h5 class="card-title">Course enrolments (by course)</h5>
							<canvas id="courseEnrolments" width="400" height="400"></canvas>
						</div>
					</div>
				</div>
			</div>
				
				<?php
					$enrolmentLabels = array();
					$enrolmentData = array();
					$courseCompletions = array();
					
					$dbQuery=$db->prepare("select id, title from courses");
					$dbQuery->execute();
					
					while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC))
					{
						$courseid=$dbRow["id"];
						$course=$dbRow["title"];
						array_push($enrolmentLabels, $course);
						
						$dbQueryEnrolments=$db->prepare("select count(id) as enrolmentCount from enrolments where courseid=:courseid");
						$dbParamsEnrolments = array('courseid'=>$courseid);
						$dbQueryEnrolments->execute($dbParamsEnrolments);
						while ($dbRowEnrolments = $dbQueryEnrolments->fetch(PDO::FETCH_ASSOC))
						{
							$enrolmentCount=$dbRowEnrolments["enrolmentCount"];
						}
						
						array_push($enrolmentData, $enrolmentCount);
						
						$dbQueryCompletions=$db->prepare("select count(id) as completions from course_completions where courseid=:courseid");
						$dbParamsCompletions = array('courseid'=>$courseid);
						$dbQueryCompletions->execute($dbParamsCompletions);
						while ($dbRowCompletions = $dbQueryCompletions->fetch(PDO::FETCH_ASSOC))
						{
							$courseCompletion=$dbRowCompletions["completions"];
						}
						
						array_push($courseCompletions, $courseCompletion);
						
					}
				?>
				
				<script>
				var ctx = document.getElementById("courseEnrolments");
				var courseEnrolments = new Chart(ctx,{
					type: 'pie',
					data: {
						labels: [
							<?php
								foreach ($enrolmentLabels as $label) {
									echo '"' . $label . '", ';
								} 
							?>
						],
						datasets: [{
							label: '# of enrolments',
							data: [
								<?php
									foreach ($enrolmentData as $data) {
										echo $data . ', ';
									} 
								?>
							],
							backgroundColor: [
								<?php
									foreach ($enrolmentData as $data) {
										$r = mt_rand(0,255);
										$b = mt_rand(0,255);
										$g = mt_rand(0,255);
										
										echo "'rgba(".$r.", ".$b.", ".$g.", 0.2)',";
									} 
								?>
							]
						}]
					},
					options: {
					}
				});
				</script>  
			
			<br>
			
			<div class="row">
				<div class="col-sm-12">
					<div class="card h-100">
						<div class="card-body">
							<h5 class="card-title">Course completions (by course)</h5>
							<canvas id="myChart" width="400" height="400"></canvas>
						</div>
					</div>
				</div>
			</div>
			
			<script>
			var ctx = document.getElementById("myChart");
			var myChart = new Chart(ctx, {
				type: 'bar',
				data: {
					labels: [
						<?php
							foreach ($enrolmentLabels as $label) {
								echo '"' . $label . '", ';
							} 
						?>
					],
					datasets: [{
						label: '# of completions',
						data: [
							<?php
								foreach ($courseCompletions as $data) {
									echo $data . ', ';
								} 
							?>
						],
						backgroundColor: [
							<?php
								$r = mt_rand(0,255);
								$b = mt_rand(0,255);
								$g = mt_rand(0,255);
									
								foreach ($enrolmentLabels as $label) {									
									echo "'rgba(".$r.", ".$b.", ".$g.", 0.2)',";
								} 
							?>
						]
					}]
				},
				options: {
					scales: {
						yAxes: [{
							ticks: {
								beginAtZero:true
							}
						}]
					}
				}
			});
			</script>    

        </div>
		
		<br>
		
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
