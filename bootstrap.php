<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

try {

	require __DIR__ . '/vendor/autoload.php';
	require __DIR__ . '/server.php';

} catch(\Exception $e){
	echo $e->getMessage();
}
