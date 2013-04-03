<?php

define('WEBTEST_URL',"http://opbe.webnet32.com/tests/runnable/WebTest.php");

//Detect if running locally and include files as such
if($_SERVER['REMOTE_ADDR'] == "127.0.0.1" OR $_SERVER['REMOTE_ADDR'] == "::1") {
	die(header("location: tests/runnable/WebTest.php"));
}
else
	die(header("location: ".WEBTEST_URL));