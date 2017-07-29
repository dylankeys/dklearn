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

    <title>dklearn</title>
    <!-- Bootstrap core CSS -->
    <link href="dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="assets/js/ie-emulation-modes-warning.js"></script>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

		<?php
        session_start();
        include ("dbConnect.php");

        if (!isset($_SESSION["currentUserID"]))
        {
            //header("Location: login.php");
            echo "<script>window.location.href = 'login.php'</script>";
        }

        $id=$_SESSION["currentUserID"];

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

		<h1>Enroled courses</h1>

              	<!-- Table -->
              	<table class="table">

					<tr><th style="text-align:left;width:150px">Course</th><th style="text-align:left;max-width:500px">Description</th></tr>
					<?php
	  
						$dbQuery=$db->prepare("select * from enrolments inner join courses on enrolments.courseid = courses.id where enrolments.userid=:id");
						$dbParams=array('id'=>$id);
						$dbQuery->execute($dbParams);
   
						while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC)) 
						{
							$courseId=$dbRow["courseID"];
							$title=$dbRow["title"];
							$description=$dbRow["description"];
							//$theIcon=$dbRow["icon"];
							//$start=$dbRow["start"];
							//$end=$dbRow["end"];
							//$active=$dbRow["active"];
        
							echo "<tr> <td><a class='a' href='course.php?id=".$courseId."'>".$title."</a></td> <td>".$description."</td></tr>";
							//echo "";
						}
	 				?>

	 			</table>
      </div>
		<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    	<script src="assets/js/ie10-viewport-bug-workaround.js"></script>
	</body>
</html>