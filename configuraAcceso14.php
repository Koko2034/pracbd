<?php

require_once __DIR__ . '/cabecera14.php';

//Version estamentos
$stmt = $oConni->prepare("SELECT count(NOMBRE)  from  EMPLEADOS");
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($NumEmple);
if ($stmt->fetch()) {
   $nTotalReg = $NumEmple;
}
//Version Trigger

/*CREATE DEFINER = 'root'@'%' PROCEDURE `CONT_EMPLEADOS`(
        OUT `cemp` INTEGER UNSIGNED
    )
    NOT DETERMINISTIC
    CONTAINS SQL
    SQL SECURITY DEFINER
    COMMENT ''
BEGIN
	SELECT count(*) from EMPLEADOS INTO cemp;
END;*/

$stmt->close();

echo json_encode(array("nTotalReg"=>$nTotalReg,"nTotalPag"=>ceil($nTotalReg/15)));

