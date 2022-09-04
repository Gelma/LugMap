<?php

/*
	Il fetch degli eventi esterni è sospeso
*/
header ("Content-Type: application/json");
echo "[]";
return;

$path = '../data/geoevents.txt';

if (file_exists ($path) == false || filemtime ($path) < (time() - (60 * 30))) {
	$contents = @file_get_contents ('http://www.linux.it/data/geoevents.txt');
	@file_put_contents ($path, $contents);
}

header ("Content-Type: application/json");
echo file_get_contents ($path);
