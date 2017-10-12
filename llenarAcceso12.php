<?php

//Establezco ver todos los errores
ini_set('error_reporting', E_ALL ^ E_NOTICE);
ini_set('display_errors', 'on');
date_default_timezone_set('Europe/Madrid');

$html="";
$debug=null;
$campo = $_POST['campo'];
$orden =  $_POST['orden'];
$paginaAct =  $_POST['paginaAct'];
$pagLimit = (int)(($paginaAct-1)*25);
$oConni = new mysqli('213.32.71.33', 'root', 'andujar34', 'INMOLOSA');
$oConni->set_charset('utf8');

$orden == "D" ? $campo = "APELLIDOS DESC":$campo="APELLIDOS ASC";
if($campo == "I"){
    $orden == "D" ? $campo = "ID_EMPLEADO DESC":$campo="ID_EMPLEADO ASC";
}elseif($campo == "N"){
    $orden == "D" ? $campo = "NOMBRE DESC":$campo="NOMBRE ASC";
}

$stmt = $oConni->prepare("select ID_EMPLEADO,NOMBRE,APELLIDOS from EMPLEADOS order by ". $campo ." LIMIT ?,25");
$stmt->bind_param('i',$pagLimit);
if($stmt->execute()){
    $stmt->store_result();
    $stmt->bind_result($IdEmpleado,$NomEmpleado,$ApeEmpleado);
    while ($stmt->fetch()) {
          $html.= "<tr><td>".$IdEmpleado . "</td><td>".$NomEmpleado . "</td><td>" . $ApeEmpleado . "</td></tr>";
    }
}else{
    $debug = $stmt->errno . " " . $stmt->error;
    }

echo json_encode(array("html"=>$html,"debug"=>$debug));

