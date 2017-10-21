<?php

require_once __DIR__ . './cabecera14.php';

//Version estamentos
$stmt = $oConni->prepare("SELECT count(NOMBRE)  from  EMPLEADOS");
$nTotalReg =0;
$NumRegPag= 15;

$stmt->execute();
$stmt->store_result();

$stmt->bind_result($NumEmple);


if ($stmt->fetch()) {
   $nTotalReg = $NumEmple;
}

//Version Trigger


$stmt->close();
$nTotalPag = ceil($nTotalReg/$NumRegPag);

echo json_encode(array("nTotalReg"=>$nTotalReg,"nTotalPag"=>$nTotalPag));

