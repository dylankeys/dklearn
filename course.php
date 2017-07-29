<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="images/favicon.ico">

    <!-- Bootstrap core CSS -->
    <link href="dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="assets/js/ie-emulation-modes-warning.js"></script>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
		<?php
			include("dbConnect.php");
			session_start();

            $userID=$_SESSION["currentUserID"];

			if (isset($_GET["id"]))
			{
                if($_GET["id"]==null)
                {
                    echo "<script>window.location.href = 'courses.php?course=noid'</script>";
                }
				$id = $_GET["id"];

                if (!isset($_SESSION["currentUserID"]))
                {
                    //header("Location: login.php?failCode=3");
                    echo "<script>window.location.href = 'login.php?failCode=3&courseid=". $id ."'</script>";
                }

				$dbQuery=$db->prepare("select * from courses where id=:id");
				$dbParams=array('id'=>$id);
				$dbQuery->execute($dbParams);
   
				while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC)) 
				{
					$title=$dbRow["title"];

					echo "<title>$title</title>";
				}
			}
			else
			{
				echo "<script>window.location.href = 'courses.php?course=noid'</script>";
			}


		?>
	</head>

	<body>

		<!-- The justified navigation menu is meant for single line per list item.
           Multiple lines will require custom code not provided by Bootstrap. -->
      <div class="masthead" style="background-image: url('images/bg-color.png');background-repeat: repeat-yx; width: 100%;">
	  <!--
        <h3 class="text-muted">Project name</h3> -->
		
		<p style="text-align:center; font-size:2.5em"><a class="logo" href="index.php"><b>dk</b>learn</a></p>
		
        <nav>
          <ul class="nav nav-justified">
            <li class="active"><a href="index.php">Home</a></li>
            <li><a href="courses.php">Courses</a></li>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="contact.php">Contact</a></li>
            <li><a href="profile.php">Profile</a></li>
            
      
          </ul>
        </nav>
      </div>

      <div class="container">

		<?php

			$id = $_GET["id"];

			$dbQuery=$db->prepare("select * from courses where id=:id");
			$dbParams=array('id'=>$id);
			$dbQuery->execute($dbParams);
   
   			while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC)) {
                $title = $dbRow["title"];
                $description = $dbRow["description"];
                $usemedia = $dbRow["usemedia"];
                $media = $dbRow["media"];

                echo "<h1>$title</h1>";

                echo "<div style='background:#D3D3D3;padding:10px;border-radius:10px;'>$description<br>";

                $dbQuery2 = $db->prepare("select * from enrolments where userID=:userID AND courseID=:courseID");
                $dbParams2 = array('userID' => $userID, 'courseID' => $id);
                $dbQuery2->execute($dbParams2);
                $rows = $dbQuery2->rowCount();

                if ($rows<1) {
                    echo "<form name='enrol' method='post' action='enrol.php'>";
                    echo '<input type="hidden" name="courseID" value="' . $id . '">';
                    echo '<input type="hidden" name="userID" value="' . $userID . '">';
                    echo '<br><p style="color:red"><b>You are not enroled on this course!</b></p>';
                    echo '<p>If you\'d like to access this course, please enrol using the button below</p><br>';
                    echo '<input type="submit" value="Enrol" class="btn btn-default" role="button">';
                    echo "</form>";
                } else {
                    //course content

                    if ($usemedia == "y") {
                        echo "<br>$media";
                    }
                    echo "</div>";
                }
            }

		?>
		</div>
	</body>
</html>