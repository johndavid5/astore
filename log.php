<?php

class Log
{
	public static $_verbosity = self::WARN;

	const TRACE=0;
	const DEBUG=1;
	const WARN=2;
	const ERROR=3;
	const FATAL=4;

	public static function message($level, $msg)
	{
		if(wouldLog($level))
		{
			error_log( levelToString($level) . ": " . $msg );
		}
	}

	public static function trace($msg)
	{
		self::message(self::TRACE, $msg);
	}

	public static function debug($msg)
	{
		self::message(self::DEBUG, $msg);
	}

	public static function warn($msg)
	{
		self::message(self::WARN, $msg);
	}

	public static function error($msg)
	{
		self::message(self::ERROR, $msg);
	}

	public static function fatal($msg)
	{
		self::message(self::FATAL, $msg);
	}

	public static function wouldLog($level)
	{
		return ( $level >= self::$_verbosity );
	}

	public static function levelToString($level)
	{
		switch($level)
		{	
			case self::TRACE:
				return "TRACE";

			case self::DEBUG:
				return "DEBUG";

			case self::WARN:
				return "WARN";

			case self::ERROR:
				return "ERROR";

			case self::FATAL:
				return "FATAL";

			default:
				return $level;
		}
	}

	public static function setVerbosity($verbosity)
	{
		self::$_verbosity = $verbosity;		
	}

	public static function getVerbosity()
	{
		return self::$_verbosity;
	}
}

?>
