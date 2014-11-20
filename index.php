<?php 
  require_once("security.php");
  require_once("utils.php");

  header("Cache-Control: no-cache, must-revalidate");
  # Expires date is in the past, to discourage caching.
  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

  session_start();

  error_log(  "TRACE: " . __FILE__ . ":" . __LINE__ . ":" . __METHOD__ . "(): " . "session_id=" . session_id() );

?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
   <html xmlns="http://www.w3.org/1999/xhtml">
<?php

  
 error_log(  "TRACE: " . __FILE__ . ":" . __LINE__ . ":" . __METHOD__ . "(): \$_POST=" . Utils::var_dump_str($_POST) );

 $msg = "";

  if(array_key_exists('verb', $_POST))
  {
     if($_POST['verb'] == "login")
	 {
		 error_log(  "TRACE: " . __FILE__ . ":" . __LINE__ . ":" . __METHOD__ . "(): Attempting login...");
	
	     if(Security::login_user($_POST['username'], $_POST['password'], session_id() ))
		 {
	  		error_log(  "TRACE: " . __FILE__ . ":" . __LINE__ . ":" . __METHOD__ . "(): Login succeeded!");
		 }
		 else
		 {
	  		error_log(  "TRACE: " . __FILE__ . ":" . __LINE__ . ":" . __METHOD__ . "(): Login failed!");
			$msg="Login has failed";
	     }
  	 }
	 else if($_POST['verb'] == "logout")
	 {
		 error_log(  "TRACE: " . __FILE__ . ":" . __LINE__ . ":" . __METHOD__ . "(): Attempting logout...");
	     Security::logout_user(session_id());
  	 }
  }

  error_log(  "TRACE: " . __FILE__ . ":" . __LINE__ . ":" . __METHOD__ . "(): Check for login...");

  $user_out = new stdClass();

  if(Security::is_logged_in(session_id(), $user_out))
  {
  	error_log(  "TRACE: " . __FILE__ . ":" . __LINE__ . ":" . __METHOD__ . "(): Logged in!");
	require("logged_in_header.php");
	if(array_key_exists('view', $_GET))
	{
		$index=$_GET['view'];
		require("view_item.php");
	}
	else
	{
		require("view_all.php");
	}
  }
  else
  {
  	error_log(  "TRACE: " . __FILE__ . ":" . __LINE__ . ":" . __METHOD__ . "(): Not Logged in!");
  	error_log(  "TRACE: " . __FILE__ . ":" . __LINE__ . ":" . __METHOD__ . "(): msg='$msg'");
	$title="Log In to your Account";
	require("not_logged_in_header.php");
  }
?>
</html>
