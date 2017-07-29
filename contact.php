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

          <iframe width="100%" height="400px" frameborder="0" style="border:0" src="https://www.google.com/maps/embed/v1/place?q=place_id:ChIJz_s33b4kYEgR4RoqeXKG_DI&key=AIzaSyCKS0u2JECOB6eaPDeA4uOervS66LTDKYk&maptype=satellite" allowfullscreen></iframe>

          <h3 style="text-align:center;">Site contacts</h3>

          <table border=0 style="text-align:center;" width="100%">
              <tr>
                  <td width="33.3%"><h3>Developer</h3><br><h5>keys-d2@email.ulster.ac.uk</h5></td>
                  <td width="33.3%"><h3>Support</h3><br><h5>Send a message</h5></td>
                  <td width="33.3%"><h3>Administration</h3><br><h5>Send a message</h5></td>
              </tr>
          </table>

      </div>
		<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    	<script src="assets/js/ie10-viewport-bug-workaround.js"></script>
	</body>
</html>