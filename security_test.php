<?php

require_once 'security_test_case.php';
require_once 'PHPUnit.php';

$suite  = new PHPUnit_TestSuite("SecurityTestCase");
$result = PHPUnit::run($suite);

echo $result -> toString();
?>

