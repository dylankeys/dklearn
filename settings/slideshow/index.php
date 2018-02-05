<?php
	include("../../config.php");
	
	if (isset($_POST["slideUpdate"]))
	{
		$id = $_POST["slideID"];
		$image = $_POST["slideImage"];
		$heading = $_POST["slideHeading"];
		$text = $_POST["slideText"];
		
		$dbQuery=$db->prepare("update slideshow set image=:image, heading=:heading, text=:text where id=:id");
		$dbParams=array('id'=>$id,'image'=>$image,'heading'=>$heading,'text'=>$text);
		$dbQuery->execute($dbParams);
		
		echo "<script>window.location.href = 'index.php?success=1'</script>";
	}
	else if(isset($_POST["slideAdd"]))
	{
		$image = $_POST["slideImage"];
		$heading = $_POST["slideHeading"];
		$text = $_POST["slideText"];
		
		$dbQuery=$db->prepare("insert into slideshow values(null,:image,:heading,:text)");
		$dbParams=array('image'=>$image,'heading'=>$heading,'text'=>$text);
		$dbQuery->execute($dbParams);
		
		echo "<script>window.location.href = 'index.php?success=1'</script>";
	}
	else
	{
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/css/bootstrap.min.css" integrity="sha384-Zug+QiDoJOrZ5t4lssLdxGhVrurbmBWopoEl+M6BdEfwnCJZtKxi1KgxUyJq13dy" crossorigin="anonymous">

	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="slideText" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../images/favicon.ico">

	<?php
		include("../../config.php");
		include("../../lib.php");
		session_start();
        $userID=$_SESSION["currentUserID"];
		
		if(!has_capability("site:config",$userID))
		{
			echo "<script>window.location.href = '../../index.php?permission=0'</script>";
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
	
    <title><?php echo $sitename;?> | Slideshow Settings</title>
	
	<!--DK CSS-->
	<link href="../../styles.css" rel="stylesheet">
	
	</head>

	<body>

		<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #1E88FF;">
		<!--<nav class="navbar navbar-expand-lg navbar-light bg-light">-->
		  <a class="navbar-brand" href="../../"><?php echo $sitename;?></a>
		  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		  </button>
		  <div class="collapse navbar-collapse" id="navbarText">
			<ul class="navbar-nav mr-auto">
			  <li class="nav-item">
				<a class="nav-link" href="../../">Home</a>
			  </li>
			  <li class="nav-item">
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
			  <li class="nav-item active">
				<?php if (has_capability("site:config",$userID)) { echo '<a class="nav-link" href="../../settings/">Administration</a>'; } ?>
			  </li>
			</ul>
			<span class="navbar-text">
			
			  <?php
				if (isset($username)) {
					echo "<img src='".$profileimage."' width='28px' alt='Profile Image' class='rounded-circle'>&nbsp;<a href='../profile/'>".$fullname." (<a href='../profile/killSession.php'>Log out</a>)</a>";
				}
				else {
					echo "<a href='../../login/'>Log in or sign up</a>";
				}
			  ?>
			</span>
		  </div>
		</nav>

      <div class="container">
		
		<br>
		
		<?php
			if(isset($_GET["success"]))
			{				
				echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
						<strong>Success!</strong> Slideshow updated!
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>';
			}

			if (isset($_POST["slideSelect"]))
			{
				echo '<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="../">Settings</a></li>
							<li class="breadcrumb-item"><a href="index.php">Slideshow settings</a></li>
							<li class="breadcrumb-item active" aria-current="page">Edit slide</li>
						</ol>
					</nav>';
			}
			else if(isset($_GET["slide"]) && $_GET["slide"]=="add")
			{
				echo '<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="../">Settings</a></li>
							<li class="breadcrumb-item"><a href="index.php">Slideshow settings</a></li>
							<li class="breadcrumb-item active" aria-current="page">Add slide</li>
						</ol>
					</nav>';
			}
			else {
				echo '<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="../">Settings</a></li>
							<li class="breadcrumb-item active" aria-current="page">Slideshow settings</li>
						</ol>
					</nav>';
			}
		?>
		
        <h1>Slideshow settings</h1>

         <?php
			if (!isset($_POST["slideSelect"]) && !isset($_GET["slide"]))
			{
         ?>
		<form method="post" action="index.php">
			<div class="form-row">
				<div class="form-group col-md-12">
					<label for="slideSelect">Select a slide to edit:</label>
					<select id="slideSelect" name='slideSelect' class="form-control" onchange='this.form.submit()'>
						
						<option selected>Choose from the options...</option>
						<?php 
							$dbQuery=$db->prepare("select * from slideshow");
							$dbQuery->execute();

							while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC)) {
								$slideid = $dbRow["id"];
								$heading = $dbRow["heading"];
								
								echo '<option value="'.$slideid.'">Slide '.$slideid.' ('.$heading.')</option>';
							}
							
							
						?>
					</select>
					<noscript><input type="submit" value="Submit"></noscript>
				</div>
			</div>
		</form>
		<p style="text-align:center;">or</p>
		<button type="button" onclick="window.location.href='index.php?slide=add'" class="btn btn-primary btn-lg btn-block">Add a new slide</button>
		<br>
		
		<?php
			}
			else if (isset($_POST["slideSelect"]))
			{
				$slideid = $_POST["slideSelect"];
				
				$dbQuery=$db->prepare("select * from slideshow where id=:id");
				$dbParams = array('id'=>$slideid);
				$dbQuery->execute($dbParams);

				while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC)) {
					$image = $dbRow["image"];
					$heading = $dbRow["heading"];
					$text = $dbRow["text"];
				}
				
				echo '<img class="slide-img-preview" src="'.$image.'" alt="'.$heading.'" class="rounded" />';
				
				echo '<form method="post" action="index.php">
						<div class="form-row">
							<div class="form-group col-md-12">
								<label for="slideImage">Slide image source</label>
								<input type="text" class="form-control" id="slideImage" name="slideImage" value="'.$image.'" >
							</div>
						</div>
						
						<div class="form-row">
							<div class="form-group col-md-12">
								<label for="slideHeading">Slide heading</label>
								<input type="text" class="form-control" id="slideHeading" name="slideHeading" value="'.$heading.'" >
							</div>
						</div>
				
						<div class="form-row">
							<div class="form-group col-md-12">
								<label for="slideText">Slide text</label>
								<textarea class="form-control" id="slideText" name="slideText" rows="5">'.$text.'</textarea>
							</div>
						</div>
						
						<input type="hidden" name="slideID" value="'.$slideid.'" />
						<input type="hidden" name="slideUpdate" />
						<input type="submit" class="btn btn-primary" value="Update slide" />
					</form>';
			}
			else if(isset($_GET["slide"]) && $_GET["slide"]=="add")
			{
			
			?>
				<form method="post" action="index.php">
					<div class="form-row">
						<div class="form-group col-md-12">
							<label for="slideImage">Image source</label>
							<input type="text" class="form-control" id="slideImage" name="slideImage">
						</div>
					</div>
						
					<div class="form-row">
						<div class="form-group col-md-12">
							<label for="slideHeading">Slide heading</label>
							<input type="text" class="form-control" id="slideHeading" name="slideHeading">
						</div>
					</div>
				
					<div class="form-row">
						<div class="form-group col-md-12">
							<label for="slideText">Slide text</label>
							<textarea class="form-control" id="slideText" name="slideText" rows="5"></textarea>
						</div>
					</div>
						
					<input type="hidden" name="slideAdd" />
					<input type="submit" class="btn btn-primary" value="Add slide" />
				</form>
		
		<?php
			}
		?>
		<br>
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
<?php
	}
?>