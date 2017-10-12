<?php

//Establezco ver todos los errores
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'on');
date_default_timezone_set('Europe/Madrid');

$oConni = new mysqli('213.32.71.33', 'root', 'andujar34', 'INMOLOSA');
$oConni->set_charset('utf8');

$stmt = $oConni->prepare("SELECT count(NOMBRE)  from  EMPLEADOS");
$nTotalReg =0;
$NumRegPag= 15;

$stmt->execute();
$stmt->store_result();

$stmt->bind_result($NumEmple);


if ($stmt->fetch()) {
   $nTotalReg = $NumEmple;
}
$stmt->close();
$nTotalPag = ceil($nTotalReg/$NumRegPag);

echo json_encode(array("nTotalReg"=>$nTotalReg,"nTotalPag"=>$nTotalPag));

