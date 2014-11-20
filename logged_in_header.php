<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php print($title); ?></title>
<link rel="stylesheet" type="text/css" href="mzw.css"> 
</head>
<body>
<p>You are logged in as <?php print($user_out->firstname . " " . $user_out->lastname); ?>.</p>
<form name="logout" action="index.php" method="post"> 
<input type="hidden" name="verb" value="logout" >
<input type="submit" value="LOGOUT">
</form>
</body>
</html>
