<?php

 // -------- START - Block title
 /**
  * Author: Mauro Mandracchia
  * Desc: This is the earth of all framework, here you can find declare MAIN and then called <a href="?file=/core/bootstrap.php">/core/bootstrap.php</a>
  */
 
	//place this before any script you want to calculate time
	$time_start = microtime(true); 
	define('MAIN', dirname(__FILE__));
	
	require(MAIN.'/core/bootstrap.php');
	$bootstrap = new Bootstrap();
	
	$time_end = microtime(true);
	//dividing with 60 will give the execution time in minutes other wise seconds
	$execution_time = ($time_end - $time_start)/60;
	//execution time of the script
	// echo '<b>Total Execution Time:</b> '.$execution_time.' Mins';

 // -------- END
 
 
 