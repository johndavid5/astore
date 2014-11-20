<?php

require_once 'security.php';
require_once 'PHPUnit.php';

class SecurityTestCase extends PHPUnit_TestCase
{
    // contains the object handle of the string class
    var $user;

    // constructor of the test suite
    function __construct($name) {
       $this->PHPUnit_TestCase($name);
    }

    // called before the test functions will be executed
    // this function is defined in PHPUnit_TestCase and overwritten
    // here
    function setUp() {
        // create a new instance of String with the
        // string 'abc'

		$time = time(); // Use timestamp to ensure unique username

        $this->user = new stdClass();
		$this->user->firstname = "Joe";
		$this->user->lastname = "Kovacs-$time";
		$this->user->username= "joeko-$time";
		$this->user->password= "Aa.1";
		$this->user->session_id= "ginseng-$time";

		Security::new_account($this->user->username, $this->user->password, $this->user->firstname, $this->user->lastname);
    }

    // called after the test functions are executed
    // this function is defined in PHPUnit_TestCase and overwritten
    // here
    function tearDown() {
        // delete your instance
        unset($this->user);
    }

    // test the toString function
    function testLogin() {
        $result = Security::login_user($this->user->username, $this->user->password, $this->user->session_id);
        $expected = TRUE;
        $this->assertTrue($result == $expected, "Using correct password, login should succeed.");
        Security::logout_user($this->user->session_id);

        $result = Security::login_user($this->user->username, $this->user->password . "-x", $this->user->session_id);
        $expected = FALSE;
        $this->assertTrue($result == $expected, "Using incorrect password, login should fail.");
    }

  }
?> 

