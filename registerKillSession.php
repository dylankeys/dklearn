<?php 
 // you have to open the session first 
 session_start(); 
 
 //remove all the variables in the session 
 session_unset(); 
 
 // destroy the session 
 session_destroy();  
?> 

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="images/favicon.ico">

    <title>Tickets Now</title>

    <!-- Bootstrap core CSS -->
    <link href="dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="justified-nav.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="assets/js/ie-emulation-modes-warning.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <div class="container">

      <!-- The justified navigation menu is meant for single line per list item.
           Multiple lines will require custom code not provided by Bootstrap. -->
      <div class="masthead">
	  <!--
        <h3 class="text-muted">Project name</h3> -->
		<a href="index.php"><img src="images/logo.png" alt="logo" height="100px"/></a>
		<a href="#"><h4 style="float:right; margin-top:50px">Member's Area</h4></a>
        <nav>
          <ul class="nav nav-justified">
             <li><a href="index.html">Home</a></li>
            <li><a href="#">Events</a></li>
            <li><a href="#">Contact</a></li>
             <li class="active"><a href="accounts.php">My Account</a></li>
            
      
          </ul>
        </nav>
      </div>
<body>
 <h1>Registration Complete</h1>
 <p>The current browsing session has been terminated and all session variables destroyed</p>
 
 <form method="post" action="login.php">
     <br>
     <input type="submit" value="Return" class="btn btn-primary btn-lg active" role="button">
   
   </form>
 </body>


     

      <!-- Site footer -->
      <footer class="footer">
        <p>&copy; tickets <b>now!</b></p>
      </footer>

    </div> <!-- /container -->


    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="assets/js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>

