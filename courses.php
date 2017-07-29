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

    <title>Courses</title>
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

            <!--
                        <div id="header">

                            <div style="float:left;margin-left:5px;"><a id="logo"><b>dk</b>learn</a></div>
                            <div style="float:right;margin-top:25px;margin-right:10px;">

                            <a id="navtext" href="index.php">home</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <a id="navtext" href="courses.php">courses</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                            </div>

                        </div>

                    <div class="container" style="padding:5px;">
            -->
            <?php
            if (isset($_GET["course"]) && $_GET["course"]=="created")
            {
                echo "<h1 style=\"background:green\">Course successfully created!<span style='float:right;font-size:20px;'><a href='courses.php'>x</a>&nbsp;</span></h1>";
            }
            if (isset($_GET["course"]) && $_GET["course"]=="noid")
            {
                echo "<h1 style=\"background:red;color:white;\">ERROR: No course id selected. Please contact the site administrator if this was a system fault.<span style='float:right;font-size:20px;'><a href='courses.php'>x</a>&nbsp;</span></h1>";
            }
            ?>
            <h1>All courses</h1>
            <button style="float:right" onclick="window.location.href='createcourse.php'">Create a course</button>
            <br><br>

            <div class="panel panel-default">
                <!-- Default panel contents -->
                <div class="panel-heading">Courses</div>

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

                        echo "<tr> <td><a class='a' href='course.php?id=$courseId'>$title</a></td> <td>$description</td></tr>";
                        //echo "";
                    }
                    ?>

                </table>
            </div>

        </div>
	</body>
</html>