<?php

//Establezco ver todos los errores
ini_set('error_reporting', E_ALL ^ E_NOTICE);
ini_set('display_errors', 'on');
date_default_timezone_set('Europe/Madrid');

$accion = ($_POST['data']);
$aux = json_decode($accion,true);
$accion = $aux['accion'];

if($accion==="Delete"){
    $id =$aux['id'];
    $info = "Desea borrar al usuario con Id : ".$id;
    echo modalInfo($info,$accion);
}
function modalInfo($info,$accion){
    $html ='<div class="modal-dialog" role="document">';
    $html.='<div class="modal-content"><div class="modal-header">';
    $html.= '<h3 class="modal-title">Informacion del '.$accion.'</h3></div>';
    $html.= '<div class="modal-body"><p>'.$info.'</p></div>';
    $html.= '<div class="modal-footer">';
    $html.= '<button type="button" class="btn btn-warning" onclick=confirmarAccion()>Confirmar</button>';
    $html.= '<button type="button" class="btn btn-warning" onclick=cancelarAccion()>Atrás</button></div></div></div>';
    return json_encode(array("status"=>"ok","html"=>$html));
}
if($accion==="confirmDelete"){
    $id =$aux['id'];
    $oConni = new mysqli('213.32.71.33', 'root', 'andujar34', 'INMOLOSA');
    $oConni->set_charset('utf8');
    $stmt = $oConni->prepare("DELETE FROM EMPLEADOS WHERE ID_EMPLEADO = ?");
    $stmt->bind_param('s',$id);
    $status=null;
    $html="";
    if($stmt->execute()){
        $stmt->store_result();
        $status = "OK";
    }else{
        $html = $stmt->errno . " " . $stmt->error;
        $status = "KO";
    }
    $stmt->close();
    echo json_encode(array("status"=>$status,"html"=>$html));
}
if($accion==="Edit"){
    $id =$aux['id'];
    echo displayEdit($id);
}
function displayEdit($id){
    $html;
    $status;
    $oConni = new mysqli('213.32.71.33', 'root', 'andujar34', 'INMOLOSA');
    $oConni->set_charset('utf8');
    $stmt = $oConni->prepare("SELECT NOMBRE,APELLIDOS FROM EMPLEADOS WHERE ID_EMPLEADO = ?");
    $stmt->bind_param('i',$id);
    if($stmt->execute()){
        $stmt->store_result();
        $stmt->bind_result($NomEmpleado,$ApeEmpleado);
        while ($stmt->fetch()) {
            $html = formCreate($id,$NomEmpleado,$ApeEmpleado);
        }
       $status="ok";
    }else{
        $html = $stmt->errno . " " . $stmt->error;
        $status = "KO";
        }
    $stmt->close();
    return json_encode(array("status"=>$status,"html"=>$html));
}
function formCreate($id,$NomEmpleado,$ApeEmpleado){
    $html='<form class="contForm" role="form" id="frmEmpleados">';
    if($id!=null){
        $html.='<div class="form-group"><label for="txtId">Id</label>';
        $html.='<input type="text" class="form-control" id="txtID" name="txtId" disabled value="'.$id.'"></div>';
    }
    $html.='<div class="form-group"><label for="txtNombre">Nombre</label>';
    $html.='<input type="text" class="form-control" id="txtNombre" name="txtNombre" value="'.$NomEmpleado.'"></div>';
    $html.='<div class="form-group"><label for="txtApellidos">Apellidos</label>';
    $html.='<input type="text" name="txtApellidos" class="form-control" id="txtApellidos" value="'.$ApeEmpleado.'"></div>'; 
    if($id!=null){
        $html.='<button type="button" class="btn btn-warning" id="butEnviar" onclick="doAccion(this.value)" value="confirm">Editar</button>';
    }else{
        $html.='<button type="button" class="btn btn-warning" id="butEnviar" onclick="doAccion(this.value)" value="Create">Guardar</button>';
    }
    $html.='<button type="button" class="btn btn-warning" id="butCancelar" onclick="cancelarAccion()">Cancelar</button></form>';
    return $html;
}
if($accion==="update"){
    $id =$aux['id'];
    $nombre = $aux['nombre'];
    $apellidos = $aux['apellidos'];
    $info = "Desea actualizar los datos del usuario cuya id es: ".$id .". Siendo ahora su nombre: ".$nombre." y apellidos: ".$apellidos;
    echo modalInfo($info,$accion);
}
if($accion === "confirmUpdate"){
    $html="";
    $status;
    $id =$aux['id'];
    $nombre = $aux['nombre'];
    $apellidos = $aux['apellidos'];
    $campo = $aux['campo'];
    $campo = campo($campo);
    $pag;
    $oConni = new mysqli('213.32.71.33', 'root', 'andujar34', 'INMOLOSA');
    $oConni->set_charset('utf8');
    $stmt = $oConni->prepare("UPDATE EMPLEADOS SET NOMBRE = ? , 
                                APELLIDOS= ? 
                                WHERE ID_EMPLEADO = ? ");
    $stmt->bind_param('ssi',$nombre,$apellidos,$id);
    if($stmt->execute()){
        $stmt->store_result();    
        $status ="ok";
        $pag = calculatePag($id,$campo);
    }else{
        $html = $stmt->errno . " " . $stmt->error;
        $status = "KO";
        }
    echo json_encode(array("status"=>"ko","html"=>$html,"pag"=>$pag));
}
if($accion === "Create"){
    $id=null;
    $html = formCreate($id,"","");
    echo json_encode(array("status"=>"ok","html"=>$html));
}
if($accion==="createUser"){
    $nombre = $aux['nombre'];
    $apellidos = $aux['apellidos'];
    $info = "Desea introducir un nuevo usuario que tendra como nombre: ".$nombre. " y apellidos: ".$apellidos;
    echo modalInfo($info,$accion);
}
if($accion==="confirmCreate"){
    $nombre = $aux['nombre'];
    $apellidos = $aux['apellidos'];
    $status="";
    $oConni = new mysqli('213.32.71.33', 'root', 'andujar34', 'INMOLOSA');
    $oConni->set_charset('utf8');
    $stmt = $oConni->prepare("INSERT INTO EMPLEADOS (NOMBRE,APELLIDOS) VALUES (?,?)");
    $stmt->bind_param('ss',$nombre,$apellidos);
    if($stmt->execute()){
        $stmt->store_result();
        $status="ok";
    }else{
        $html = $stmt->errno . " " . $stmt->error;
        $status = "KO";
        }
    echo json_encode(array("status"=>$status,"html"=>$html,"pag"=>0));
}
function campo($campo){
    if($campo==="N"){
        $campo ="NOMBRE";
    }
    if($campo==="I"){
        $campo="ID_EMPLEADO";
    }
    if($campo==="A"){
        $campo ="APELLIDOS";
    }
    return $campo;
}
function calculatePag($id,$campo){
    $oConni = new mysqli('213.32.71.33', 'root', 'andujar34', 'INMOLOSA');
    $oConni->set_charset('utf8');
    $paginaAct="";
    $stmt = $oConni->prepare("SELECT pos/15 FROM (SELECT @rownum:=@rownum+1 'pos',ID_EMPLEADO, NOMBRE FROM EMPLEADOS e ,(SELECT @rownum:=0) r ORDER BY ? )x WHERE ID_EMPLEADO= ?;");
    $stmt->bind_param('ss',$campo,$id);
    if($stmt->execute()){
        $stmt->bind_result($posicion);
        while ($stmt->fetch()) {
            $paginaAct = ceil($posicion);
        }
    }else{
        $pos = $stmt->errno . " " . $stmt->error;
        $status = "KO";
        }
   return $paginaAct;
}
/*
if($accion==='Create'){
    echo create(); 
}
if($accion==='Read'){
    $id =$aux['id'];
    echo read($id);
}
if($accion==='Delete'){
    $id = $aux['id'];
    echo Delete($accion,$id);
}
function Delete($accion,$id){
    $oConni = new mysqli('213.32.71.33', 'root', 'andujar34', 'INMOLOSA');
    $oConni->set_charset('utf8');
    $stmt = $oConni->prepare("DELETE FROM EMPLEADOS WHERE ID_EMPLEADO = ?");
    $stmt->bind_param('s',$id);
    $status=null;
    if($stmt->execute()){
        $stmt->store_result();
        $html=modalInfo("El usuario ha sido borrado",$accion);
        $status = "OK";
    }else{
        $debug = $stmt->errno . " " . $stmt->error;
        $html =modalInfo($debug,$accion);
        $status = "KO";
    }
    $stmt->close();
    return json_encode(array("status"=>$status,"html"=>$html));
}
if($accion === 'update'){
    $id=$aux['id'];
    $nombre=$aux['nombre'];
    $apellidos=$aux['apellidos'];
    echo update($id,$nombre,$apellidos);
}
if($accion==='createform'){
    $html='<form role="form" id="frmEmpleados">';
    $html.='<div class="form-group"><label for="txtNombre">Nombre</label>';
    $html.='<input type="text" class="form-control" id="txtNombre" name="txtNombre" value="'.$NomEmpleado.'"></div>';
    $html.='<div class="form-group"><label for="txtApellidos">Apellidos</label>';
    $html.='<input type="text" name="txtApellidos" class="form-control" id="txtApellidos" value="'.$ApeEmpleado.'"></div>';   
    $html.='<button type="button" class="btn btn-primary" id="butEnviar" onclick="doAccion(this.value)" value="create">Crear</button>';
    $html.='<button type="button" class="btn btn-primary" id="butCancelar" onclick="cancelarAccion()">Cancelar</button></form>';

    echo json_encode(array("status"=>"ok","html"=>$html));
}
if($accion ==='confirm'){
    $html = modalinfo("confirm","Confirmacion de los cargos");
    echo json_encode(array("status"=>"ok","html"=>$html));
}

function read($id){
    
}

function create(){
    $oConni = new mysqli('213.32.71.33', 'root', 'andujar34', 'INMOLOSA');
    $oConni->set_charset('utf8');
    $nombre = $_POST['txtNombre'];
    $apellidos = $_POST['txtApellidos'];
    $stmt = $oConni->prepare("INSERT INTO EMPLEADOS (NOMBRE,APELLIDOS) VALUES (?,?)");
    $stmt->bind_param('ss',$nombre,$apellidos);
    if($stmt->execute()){
        $stmt->store_result();
        echo json_encode(array("status"=>"ok","html"=>""));
    }else{
        $debug = $stmt->errno . " " . $stmt->error;
        echo json_encode(array("status"=>"ok","html"=>$debug));
        }
}
function update($id,$nombre,$apellidos){
    $html="";
    $status;
    $oConni = new mysqli('213.32.71.33', 'root', 'andujar34', 'INMOLOSA');
    $oConni->set_charset('utf8');
    $stmt = $oConni->prepare("UPDATE EMPLEADOS SET NOMBRE = ? , 
                                APELLIDOS= ? 
                                WHERE ID_EMPLEADO = ? ");
    $stmt->bind_param('ssi',$nombre,$apellidos,$id);
    if($stmt->execute()){
        $stmt->store_result();    
        $status ="ok";
    }else{
        $html = $stmt->errno . " " . $stmt->error;
        $status = "KO";
        }
    return json_encode(array("status"=>"ko","html"=>$html));
}
*/