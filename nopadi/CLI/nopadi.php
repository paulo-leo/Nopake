#!/usr/bin/php
<?php

$options = isset($argv[1]) ? $argv : false;

$msg = 'Mensagem do Nopadi - Tente novamente e informe um argumento para o comando. www.nopadi.com/doc';

if($options){
	  
	  $arg0 = isset($options[1]) ? $options[1] : null;
	  $arg1 = isset($options[2]) ? $options[2] : null;
	  $arg2 = isset($options[3]) ? $options[3] : null;
	  $arg3 = isset($options[4]) ? $options[4] : null;
	  $arg4 = isset($options[5]) ? $options[5] : null;
	  $arg5 = isset($options[6]) ? $options[6] : null;
	  $arg6 = isset($options[7]) ? $options[7] : null;
	  $arg7 = isset($options[8]) ? $options[8] : null;
	  $arg8 = isset($options[9]) ? $options[9] : null;
	  $arg9 = isset($options[10]) ? $options[10] : null;
	  $arg10 = isset($options[11]) ? $options[11] : null;
	  
	  include('read.php');
	  
      $json = new read('nopadi.json');

	  $func = $json->get($arg0);
	  
	  if($func){
		 include($func); 
	  }else echo $msg;
	  
  }else echo $msg;




?>

