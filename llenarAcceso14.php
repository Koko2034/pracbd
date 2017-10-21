<?php

require_once __DIR__ . '/cabecera14.php';
require_once __DIR__ . '/crudEmpleados.php';

$aux = json_decode($_POST['data'],true);
$pagLimit = (int)(($aux['paginaAct']-1)*15);
$campo = campo($aux['campo'],$aux['orden']);
$stmt = $oConni->prepare("SELECT ID_EMPLEADO,NOMBRE,APELLIDOS FROM EMPLEADOS ORDER BY ".$campo." LIMIT ?,15");
$stmt->bind_param('i',$pagLimit);
$stmt->execute();
$stmt->store_result();
$debug = $stmt->errno . " " . $stmt->error;
$html = iniTable($campo);
$stmt->bind_result($IdEmpleado,$NomEmpleado,$ApeEmpleado);
$stmt->bind_result($IdEmpleado,$NomEmpleado,$ApeEmpleado);
while ($stmt->fetch()) {
    $html.= "<tr id=".$IdEmpleado." onclick=guardaID(this.id)><td>".$IdEmpleado . "</td><td>".$NomEmpleado . "</td><td>" . $ApeEmpleado . "</td></tr>";
}
$html.='</tbody></table>';
$stmt->close();

echo json_encode(array("html"=>$html,"debug"=>$debug));

function campo($campo,$orden){
    if($campo === "A"){
        $orden === "D" ? $campo = "APELLIDOS DESC":$campo="APELLIDOS ASC";
    }
    if($campo === "I"){
        $orden === "D" ? $campo = "ID_EMPLEADO DESC":$campo="ID_EMPLEADO ASC";
    }
    if($campo === "N"){
        $orden === "D" ? $campo = "NOMBRE DESC":$campo="NOMBRE ASC";
    }
    return $campo;
}
function iniTable($campo){

    if($campo === "APELLIDOS DESC"){
        $html='<table class="table"><thead><tr><th id="divID">Id</th>';
        $html.='<th id="divNOMBRE">Nombre</th>';
        $html.='<th id="divAPELLIDOS"><img id="imgAPELLIDOS" src="arrow_up_blue.gif"/>Apellidos</th></tr></thead><tbody>';
    }
    if($campo === "APELLIDOS ASC"){
        $html='<table class="table"><thead><tr><th id="divID">Id</th>';
        $html.='<th id="divNOMBRE">Nombre</th>';
        $html.='<th id="divAPELLIDOS"><img id="imgAPELLIDOS" src="arrow_down_blue.gif"/>Apellidos</th></tr></thead><tbody>';
    }
    if($campo === "NOMBRE DESC"){
        $html='<table class="table"><thead><tr><th id="divID">Id</th>';
        $html.='<th id="divNOMBRE"><img id="imgNOMBRE" src="arrow_up_blue.gif"/>Nombre</th>';
        $html.='<th id="divAPELLIDOS">Apellidos</th></tr></thead><tbody>';
    }
    if($campo === "NOMBRE ASC"){
        $html='<table class="table"><thead><tr><th id="divID">Id</th>';
        $html.='<th id="divNOMBRE"><img id="imgNOMBRE" src="arrow_down_blue.gif"/>Nombre</th>';
        $html.='<th id="divAPELLIDOS">Apellidos</th></tr></thead><tbody>';
    }
    if($campo === "ID_EMPLEADO ASC"){
        $html='<table class="table"><thead><tr><th id="divID"><img id="imgID" src="arrow_down_blue.gif"/>Id</th>';
        $html.='<th id="divNOMBRE">Nombre</th>';
        $html.='<th id="divAPELLIDOS">Apellidos</th></tr></thead><tbody>';
    }
    if($campo === "ID_EMPLEADO DESC"){
    
        $html='<table class="table"><thead><tr><th id="divID"><img id="imgID" src="arrow_up_blue.gif"/>Id</th>';
        $html.='<th id="divNOMBRE">Nombre</th>';
        $html.='<th id="divAPELLIDOS">Apellidos</th></tr></thead><tbody>';
    }
    return $html;
}