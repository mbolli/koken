<?php

    $autoloader =  __DIR__ . '/vendor/autoload.php';
    require $autoloader;
    
	// Set this so the system will know they are using index.php?/this/that type URLs
	$rewrite = false;
	require 'app' . DIRECTORY_SEPARATOR . 'site' . DIRECTORY_SEPARATOR . 'site.php';