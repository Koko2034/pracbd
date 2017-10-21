<?php
//Establezco ver todos los errores
ini_set('error_reporting', E_ALL ^ E_NOTICE);
ini_set('display_errors', 'on');
date_default_timezone_set('Europe/Madrid');

$oConni = new mysqli('localhost', 'aluR', '56_44_kKpo', 'INMOLOSA');
$oConni->set_charset('utf8');