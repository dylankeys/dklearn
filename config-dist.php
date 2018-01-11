<?php //Rocket Learn configuration file

// set up database connection
$dbHost = '';
$databaseName = '';
$databaseUsername = '';
$databasePassword 	= '';


// make the database connection
$db = new PDO("mysql:host=$dbHost;dbname=$databaseName;charset=utf8", "$databaseUsername", "$databasePassword");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 	// enable error handling
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); 			// turn off emulation mode

// get config variables
$dbQuery=$db->prepare("select * from config");
$dbQuery->execute();

while ($dbRow = $dbQuery->fetch(PDO::FETCH_ASSOC))
{
   $setting = $dbRow["setting"];

   if ($setting == "themecolour")
   {
      $theme = $dbRow["value"];
   }
   else if ($setting == "sitename")
   {
      $sitename = $dbRow["value"];
   }
}

?>
