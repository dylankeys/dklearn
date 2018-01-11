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

            <?php
            if (isset($_GET["course"]) && $_GET["course"]=="created")
            {
				echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
						<strong>Success!</strong> The course has been created.
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>';
            }
            if (isset($_GET["course"]) && $_GET["course"]=="noid")
            {
				echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
						<strong>ERROR!</strong> No course ID selected. Please contact the site administrator if this was a system fault.
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>';
            }
			if (isset($_GET["unenrol"]) && $_GET["unenrol"]=="1")
            {
				echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
						<strong>Success!</strong> You have been unenrolled from the course.
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>';
            }
			if (isset($_GET["permission"]) && $_GET["permission"]=="0")
            {
				echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
						<strong>ERROR!</strong> Invalid permissions to access this page. Please contact the site administrator if this was a system fault.
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>';
            }
            ?>
            <h1>All courses</h1>
            <?php if(has_capability("course:create",$userID)) { echo '<button type="button" class="btn btn-primary btn-sm" style="float:right" onclick="window.location.href=\'create.php\'">Create a course</button>'; } ?>
            <br><br>

                <!-- Default panel contents --
                <!-- Table -->
                <table class="table">

                    <tr><th style="text-align:left;width:150px">Course</th><th style="text-align:left;max-width:500px">Description</th></tr>
                    <?php

                    $dbQuery=$db->prepare("select * from courses");
                    //$dbParams=array('id'=>$id);
                    $dbQuery->execute();

                    while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC))
                    {
                        $courseId=$dbRow["id"];
                        $title=$dbRow["title"];
                        $description=$dbRow["description"];
                        //$theIcon=$dbRow["icon"];
                        $start=$dbRow["start"];
                        $end=$dbRow["end"];
                        $active=$dbRow["active"];

                        echo "<tr> <td><a class='a' href='view.php?id=$courseId'>$title</a></td> <td>$description</td></tr>";
                        //echo "";
                    }
                    ?>

                </table>

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
