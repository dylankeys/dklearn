<?php 
 // you have to open the session first 
 session_start(); 
 
 //remove all the variables in the session 
 session_unset(); 
 
 // destroy the session 
 session_destroy();  
 
 //back to events
header("Location: index.php");
?> 