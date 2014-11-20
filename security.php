<?php

require_once("config.php");
require_once("log.php");

/**
*
* Login Security
* --------------
* Requirements:
*
* Please create a database-driven security class to protect a section of a website with password security.  
*
* -- Include the ability for an admin user to create new system accounts by generating "salted" MD5 passwords (or equivalent).
* -- A logged in user should be automatically logged out after a predetermined amount of inactivity.
* -- Ensure the user-selected password fits a standard, modern, set of parameters (ie password must include 1 capital letter and 1 number, etc.)
* -- Apply the class to a section of the site (or group of pages) that will redirect unauthorized users.
*
*/
class Security
{
	const SITEWIDE_SALT = "5Ttf#0s0JW3g){4QmY?V";

	protected static $_dbh = null;

	/** Will do a "lazy init" of DB handle and return the DB handle.
	 * Connects to database as per params found in Config class and return the DB handle.
	 * Will re-use DB handle if already created.
	*/
	public static function get_dbh()
	{
		if(!self::$_dbh)
		{
				self::$_dbh = new PDO(Config::DB_DSN, Config::DB_USERNAME, Config::DB_PASSWORD);

				self::$_dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, TRUE);
				self::$_dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				self::$_dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ); // Fetch each row as an object

		}

		return self::$_dbh;
	}/* get_dbh() */

	/** Create a new system account storing the password as a "salted" MD5 "hash".
	* @returns: user_id of new account
    */
	public static function new_account($username, $password, $firstname, $lastname)
	{
		$random_salt = self::make_random_salt();
		$hash_algorithm = "sha256"; // Supposed to be much better than "MD5", which has already been "cracked".
		$password_hash = hash($hash_algorithm, $random_salt . self::SITEWIDE_SALT . $password); 

		$dbh = self::get_dbh();

		/* Use prepared statements to avoid SQL injection attacks. */
		$stmt = $dbh->prepare("INSERT INTO user (username, hash_algorithm, password_hash, password_salt, firstname, lastname, created) VALUES (:username, :hash_algorithm, :password_hash, :password_salt, :firstname, :lastname, NOW())");
		$stmt->bindParam(':username', $username);
		$stmt->bindParam(':hash_algorithm', $hash_algorithm);
		$stmt->bindParam(':password_hash', $password_hash);
		$stmt->bindParam(':password_salt', $random_salt);
		$stmt->bindParam(':firstname', $firstname);
		$stmt->bindParam(':lastname', $lastname);

		$stmt->execute();

		return $dbh->lastInsertId();
	}


	public static function check_password($username, $password, &$msg_out=null)
	{
		$dbh = self::get_dbh();

		/* Use prepared statements to avoid SQL injection attacks. */
		$stmt = $dbh->prepare("SELECT hash_algorithm, password_hash, password_salt, firstname, lastname, created FROM user WHERE username = :username");
		$stmt->bindParam(':username', $username);

		$count = 0;
		if ($stmt->execute())
		{
			while ($row = $stmt->fetch())
			{
				$count++;

				$db_hash_algorithm=$row->hash_algorithm;
				$db_password_hash=$row->password_hash;
				$db_password_salt=$row->password_salt;
				$firstname=$row->firstname;
				$lastname=$row->lastname;
				$created=$row->created;
			}
		}

		if($count == 0)
		{
			if(!is_null($msg_out))
			{
				$msg_out = "username '$username' not found.";
			}
			return FALSE;
		}

		$my_password_hash = hash($db_hash_algorithm, $db_password_salt . self::SITEWIDE_SALT . $password); 

		if($my_password_hash != $db_password_hash)
		{
			if(!is_null($msg_out))
			{
				$msg_out = "invalid password for username '$username'";
			}
			return FALSE;
		}
		else
		{
			return TRUE;
		}

	}/* check_password() */

	public static function logout_user($session_id)
	{
		$dbh = self::get_dbh();

		/* Use prepared statements to avoid SQL injection attacks. */
		$stmt = $dbh->prepare("UPDATE user SET logged_in=0 WHERE logged_in=1 AND session_id=:session_id");

		$stmt->bindParam(':session_id', $session_id);

		$stmt->execute();
	}

	public static function login_user($username, $password, $session_id, &$msg_out=null) 
	{
		if(!self::check_password($username, $password, $msg_out))
		{
			return FALSE;
		}
		else	
		{
			self::mark_as_logged_in($username, $session_id);
			return TRUE;
		}
	}

	public static function mark_as_logged_in($username, $session_id)
	{
		$dbh = self::get_dbh();

		/* Use prepared statements to avoid SQL injection attacks. */
		$stmt = $dbh->prepare("UPDATE user SET logged_in=1, last_login=NOW(), last_access=NOW(), session_id=:session_id WHERE username=:username");

		$stmt->bindParam(':username', $username);
		$stmt->bindParam(':session_id', $session_id);

		$stmt->execute();
	}

	public static function is_logged_in($session_id, &$user_out=null)
	{
		$dbh = self::get_dbh();

		/* Use prepared statements to avoid SQL injection attacks. */
		$stmt = $dbh->prepare("SELECT logged_in, last_login, last_access, username, firstname, lastname FROM user WHERE session_id = :session_id and logged_in=1");
		$stmt->bindParam(':session_id', $session_id);

		$count = 0;
		if ($stmt->execute())
		{
			while ($row = $stmt->fetch())
			{
				$count++;
				$logged_in=$row->logged_in;
				$last_login=$row->last_login;
				$last_access=$row->last_access;
				$username=$row->username;
				$firstname=$row->firstname;
				$lastname=$row->lastname;
			}
		}

		if($count == 0)
		{
			return FALSE;
		}
		else
		{
			if(!is_null($user_out))
			{
				$user_out->logged_in = $logged_in;
				$user_out->last_login = $last_login;
				$user_out->username = $username;
				$user_out->firstname = $firstname;
				$user_out->lastname = $lastname;
			}

			if($logged_in)
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
	}

	/** Create a random "salt" string to append to the user-supplied password
	 * and enhance security.
	*/
	public static function make_random_salt($length=20)
	{
				$pool=array(
						array("a","b","c","d","e","f","g","h","i","j","k","m","n","p","q","r","s","t","u","v","w","x","y","z")
						,array("A","B","C","D","E","F","G","H","I","J","K","M","N","P","Q","R","S","T","U","V","W","X","Y","Z")
						,array("0","1","2","3","4","5","6","7","8","9")
						,array("!","\"", "#", "\$", "%", "&", "'", "(", ")", "*", "+", ",", "-", ".", "/", ":", ";", "<", "=", ">", "?", "@", "[", "\\", "]", "^", "_", "`", "{", "|", "}", "~")
				);

				$output = "";


				for($i=0; $i<$length; $i++)
				{
					$choice1 = rand(0, count($pool)-1);
					
					$choice2 = rand(0, count($pool[$choice1])-1);

					$output .= $pool[$choice1][$choice2];
				}

				return $output;
	}


	/** Checks criteria for a strong password:
	* 1. Password must include 1 capital letter.
	* 2. Password must include 1 lowercase letter.
	* 3. Password must include 1 number.
	* 4. Password must include 1 form of punctuation. 
	*
	* @@returns: a. TRUE if strong password
	*                   OR
	*            b. FALSE if not a strong password, in which case you may 
	*				check pass-by-reference $msg_out for an error string to explain
	*   			why it's not a strong password.
	*/
	public static function is_strong_password($password, &$msg_out = null)
	{
		$password = (string)$password; // cast to a string to be safe

		$found_lc=FALSE;		
		$found_uc=FALSE;
		$found_num=FALSE;	
		$found_punct=FALSE;	

		$length = strlen($password);

		for($i=0; $i<$length; $i++)
		{
			$c = $password[$i];

			
			if( !$found_lc && ctype_alpha($c) && ctype_lower($c) )
			{
				$found_lc = TRUE;
			}

			if( !$found_uc && ctype_alpha($c) && ctype_upper($c) )
			{
				$found_uc = TRUE;
			}

			if( !$found_num && ctype_digit($c) )
			{
				$found_num = TRUE;
			}

			if( !$found_punct && ctype_punct($c) )
			{
				$found_punct = TRUE;
			}

			if($found_lc && $found_uc && $found_num && $found_punct)
			{
				return TRUE;
			}
		}

		if( ! is_null( $msg_out ) )
		{
			if(!$found_lc)
			{
				$msg_out .= $msg_out=="" ? "" : ", ";
				$msg_out .= "needs a lowercase letter";
			}
			if(!$found_uc)
			{
				$msg_out .= $msg_out=="" ? "" : ", ";
				$msg_out .= "needs an uppercase letter";
			}
			if(!$found_num)
			{
				$msg_out .= $msg_out=="" ? "" : ", ";
				$msg_out .= "needs a numerical character";
			}
			if(!$found_punct)
			{
				$msg_out .= $msg_out=="" ? "" : ", ";
				$msg_out .= "needs a punctuation mark";
			}

			$msg_out = "password '$password' " . $msg_out;
		}

		return FALSE;

	}/* is_strong_password() */
}

?>
