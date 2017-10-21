<?php

require_once __DIR__ . '/cabecera14.php';


$aux = json_decode($_POST['data'], true);
$accion = $aux['accion'];
$id= $aux['id'];
$nombre =$aux['nombre'];
$apellidos = $aux['apellidos'];
$campo = campo($aux['campo'],$aux['orden']);

if($accion === "crear"){
    $stmt = $oConni->prepare("INSERT INTO EMPLEADOS (NOMBRE,APELLIDOS) VALUES (?,?)");
    $stmt->bind_param('ss', $nombre, $apellidos);
    $stmt->execute();
    $status ="OK";
    $debug =$stmt->errno . " " . $stmt->error;
    $mensaje = "Insercion realizada";
    if ($stmt->affected_rows==0) {
        $status = "KO";
        $mensaje = "Imposible borrar el registro";
    }
    $stmt = $oConni->prepare("SELECT MAX(ID_EMPLEADO) FROM EMPLEADOS");
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($Id_Empleado);
    if($stmt->fetch()){
        $pag = calculatePag($Id_Empleado, $campo, $oConni);
    }
    $stmt->close();
    echo json_encode(array("status" => $status, "mensaje" => $mensaje, "pag" => $pag));
}
if($accion === "borrar"){
    $stmt = $oConni->prepare("DELETE FROM EMPLEADOS WHERE ID_EMPLEADO = ?");
    $stmt->bind_param('s',$id);
    $stmt->execute();
    $status = "OK";
    $mensaje = "Registro borrado";
    if ($stmt->affected_rows==0) {
        $status = "KO";
        $mensaje = "Imposible borrar el registro";
    }
    $debug = $stmt->errno . " " . $stmt->error;
    $stmt->close();
    echo json_encode(array("status" => $status, "mensaje" => $mensaje,"debug" => $debug, "pag" => 1));
}
if($accion === "editar"){
    $stmt = $oConni->prepare("UPDATE EMPLEADOS SET NOMBRE = ? , APELLIDOS= ? WHERE ID_EMPLEADO = ? ");
    $stmt->bind_param('ssi', $nombre, $apellidos, $id);
    $stmt->execute();
    $debug = $stmt->errno . " " . $stmt->error;
    $status = "OK";
    $mensaje = "Registro Actualizado";
    if ($stmt->affect_rows==0) {
        $status = "KO";
        $mensaje = "imposible actualizar el registro";        
    }
    $pag = calculatePag($id, $campo, $oConni);
    
$stmt->close();
echo json_encode(array("status" => $status, "debug" => $debug, "pag" => $pag));
}
if($accion === "crearFormNuevo"){
    $html = formCreate();
    echo json_encode(array("status"=>"ok","html"=>$html));
}
if($accion === "crearFormEdit"){
    $html = formCreate();
    echo json_encode(array("status"=>"ok","html"=>$html));
}
function formCreate(){
    $html = '<form class="contForm" role="form" id="frmEmpleados">';
    $html .= '<div class="form-group"><label for="txtNombre">Nombre</label>';
    $html .= '<input type="text" class="form-control" id="txtNombre" name="txtNombre" required  value=""></div>';
    $html .= '<div class="form-group"><label for="txtApellidos">Apellidos</label>';
    $html .= '<input type="text" name="txtApellidos" class="form-control" id="txtApellidos" required  value=""></div>';
    $html .= '<button type="button" class="btn btn-warning" id="butEnviar"  onclick="doAccion()">Guardar</button>';
    $html .= '<button type="button" class="btn btn-warning" id="butCancelar" onclick="cancelarAccion()">Cancelar</button></form>';
    return $html;
}
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
function calculatePag($id,$campo,$oConni){
    $stmt = $oConni->prepare("SELECT ID_EMPLEADO, NOMBRE,APELLIDOS FROM EMPLEADOS ORDER BY $campo ");
    $cont = 0;
    if ($stmt->execute()) {
        $stmt->store_result();
        $stmt->bind_result($Id_Empleado, $NomEmpleado, $Ape_Empleado);
        while ($stmt->fetch()) {
            $cont++;
            if ($Id_Empleado == $id) {
                $posicion = ceil($cont / 15);
            }
        }
    }
    return $posicion;
}
