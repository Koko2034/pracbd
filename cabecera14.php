<?php
//Establezco ver todos los errores
ini_set('error_reporting', E_ALL ^ E_NOTICE);
ini_set('display_errors', 'on');
date_default_timezone_set('Europe/Madrid');

$oConni = new mysqli('213.32.71.33', 'root', 'andujar34', 'LUGARES');
$oConni->set_charset('utf8');