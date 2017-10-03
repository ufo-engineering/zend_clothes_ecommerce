<?php

error_reporting(0);					// show all errors
ini_set('display_errors', 0);			// output errors in response

require 'ZendCMF/Application.php';		// include main application class

$app = new ZendCMF\Application(			// create new application instance
	require('config.php'),				// configuration file
	require('routing.php')				// routing rules file
);

$app->run($_SERVER['REQUEST_URI']);		// run query
