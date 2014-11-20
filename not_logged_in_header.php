<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php print($title); ?></title>
<link rel="stylesheet" type="text/css" href="mzw.css"> 
</head>
<body>
<?php
error_log(  "TRACE: " . __FILE__ . ":" . __LINE__ . ":" . __METHOD__ . "(): \$msg='$msg'");
if(isset($msg) && !is_null($msg) && strlen($msg) > 0)
{
	print("<p class=\"msg\">$msg</p>" . PHP_EOL);
}
?>
<p>You are not logged in.</p>
<form name="login" action="index.php" method="post"> 
<input type="hidden" name="verb" value="login" >
Username: <input type="text" name="username" value="joes">
Password: <input type="password" name="password" value="Aa.1">
<input type="submit" value="LOGIN">
</form>
</body>
</html>
