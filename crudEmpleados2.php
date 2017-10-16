<?php

//Establezco ver todos los errores
ini_set('error_reporting', E_ALL ^ E_NOTICE);
ini_set('display_errors', 'on');
date_default_timezone_set('Europe/Madrid');

$accion = ($_POST['data']);
$aux = json_decode($accion,true);
$accion = $aux['accion'];
/*
if($accion==="Delete"){
    $id =$aux['id'];
    $info = "Desea borrar al usuario con Id : ".$id;
    echo modalInfo($info,$accion);
}
if($accion==="update"){
    $id =$aux['id'];
    $nombre = $aux['nombre'];
    $apellidos = $aux['apellidos'];
    $info = "Desea actualizar los datos del usuario cuya id es: ".$id .". Siendo ahora su nombre: ".$nombre." y apellidos: ".$apellidos;
    echo modalInfo($info,$accion);
}
if($accion==="Edit"){
    $id =$aux['id'];
    echo displayEdit($id);
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

function modalInfo($info,$accion){
    $html ='<div class="modal-dialog" role="document">';
    $html.='<div class="modal-content"><div class="modal-header">';
    $html.= '<h3 class="modal-title">Informacion del '.$accion.'</h3></div>';
    $html.= '<div class="modal-body"><p>'.$info.'</p></div>';
    $html.= '<div class="modal-footer">';
    if($accion!="comprobarDelete" & $accion!="comprobarEdit"){
        $html.= '<button type="button" class="btn btn-warning" onclick=confirmarAccion()>Confirmar</button>';
    }
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
    $html.='<input type="text" class="form-control" id="txtNombre" name="txtNombre" required onblur="controlText()" value="'.$NomEmpleado.'"></div>';
    $html.='<div class="form-group"><label for="txtApellidos">Apellidos</label>';
    $html.='<input type="text" name="txtApellidos" class="form-control" id="txtApellidos" required onblur="controlText()" value="'.$ApeEmpleado.'"></div>'; 
    if($id!=null){
        $html.='<button type="button" class="btn btn-warning" id="butEnviar" disabled onclick="doAccion(this.value)" value="confirm">Editar</button>';
    }else{
        $html.='<button type="button" class="btn btn-warning" id="butEnviar" disabled  onclick="doAccion(this.value)" value="Create">Guardar</button>';
    }
    $html.='<button type="button" class="btn btn-warning" id="butCancelar" onclick="cancelarAccion()">Cancelar</button></form>';
    return $html;
}

if($accion === "confirmUpdate"){
    $html="";
    $status;
    $id =$aux['id'];
    $nombre = $aux['nombre'];
    $apellidos = $aux['apellidos'];
    $campo = $aux['campo'];
    $orden = $aux['orden'];
    $campo = campo($campo,$orden);
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

if($accion==="confirmCreate"){
    $nombre = $aux['nombre'];
    $apellidos = $aux['apellidos'];
    $status="";
    $pag=1;
    $campo =$aux['campo'];
    $orden = $aux['orden'];
    $campo=campo( $campo,$orden);
    $id= $aux['id'];
    echo  createUser($nombre,$apellidos,$status,$pag,$campo,$id);
    
}
function campo($campo,$orden){
    if($campo==="N" && $orden==="A"){
        $campo ="NOMBRE ASC";
    }
    if($campo==="N" && $orden==="D"){
        $campo ="NOMBRE DESC";
    }
    if($campo==="I" && $orden==="A"){
        $campo="ID_EMPLEADO ASC";
    }
    if($campo==="I" && $orden==="D"){
        $campo="ID_EMPLEADO DESC";
    }
    if($campo==="A" && $orden==="A"){
        $campo ="APELLIDOS ASC";
    }
    if($campo==="A" && $orden ==="D"){
        $campo ="APELLIDOS DESC";
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
if($accion==="comprobarDelete"){
    $html;
    $status="KO";
    $id = $aux['id'];
    $oConni = new mysqli('213.32.71.33', 'root', 'andujar34', 'INMOLOSA');
    $oConni->set_charset('utf8');
    $stmt = $oConni->prepare("SELECT NOMBRE,APELLIDOS FROM EMPLEADOS WHERE ID_EMPLEADO = ?");
    $stmt->bind_param('i',$id);
    if($stmt->execute()){
        $stmt->store_result();
        $stmt->bind_result($NomEmpleado,$ApeEmpleado);
        while ($stmt->fetch()) {
            $html = "";
            $status="ok";
        }
        if($status!="ok"){
            $info = "No ha sido posible encontrar al usuario";
            $aux=json_decode(modalInfo($info,$accion),true);
            $html = $aux['html'];
        }
    }else{
        $html = $stmt->errno . " " . $stmt->error;
        $status = "KO";
        }
    $stmt->close();
   echo json_encode(array("status"=>$status,"html"=>$html,"pag"=>1));
}
if($accion==="comprobarEdit"){
    $html;
    $status="KO";
    $id = $aux['id'];
    $pag=1;
    $oConni = new mysqli('213.32.71.33', 'root', 'andujar34', 'INMOLOSA');
    $oConni->set_charset('utf8');
    $stmt = $oConni->prepare("SELECT NOMBRE,APELLIDOS FROM EMPLEADOS WHERE ID_EMPLEADO = ?");
    $stmt->bind_param('i',$id);
    if($stmt->execute()){
        $stmt->store_result();
        $stmt->bind_result($NomEmpleado,$ApeEmpleado);
        while ($stmt->fetch()) {
            $html = "";
            $status="ok";
            $pag=calculatePag($id,$campo);

        }
        if($status!="ok"){
            $info = "No ha sido posible encontrar al usuario";
            $aux=json_decode(modalInfo($info,$accion),true);
            $html = $aux['html'];
        }
    }else{
        $html = $stmt->errno . " " . $stmt->error;
        $status = "KO";
        }
    $stmt->close();
   echo json_encode(array("status"=>$status,"html"=>$html,"pag"=>$pag));
}*/
if($accion==="configuraAcceso"){
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
   // echo json_encode(array("horla"=>"sdljglsjdng","sdljndsl"=>"sdljghjlsd"));
}

/*
function createUser($nombre,$apellidos,$status,$pag,$campo,$id){
    $oConni = new mysqli('213.32.71.33', 'root', 'andujar34', 'INMOLOSA');
    $oConni->set_charset('utf8');
    $stmt = $oConni->prepare("INSERT INTO EMPLEADOS (NOMBRE,APELLIDOS) VALUES (?,?)");
    $stmt->bind_param('ss',$nombre,$apellidos);
    if($stmt->execute()){
        $stmt->store_result();
        $status="ok";
        $pag=calculatePag($id,$campo);
    }else{
        $html = $stmt->errno . " " . $stmt->error;
        $status = "KO";
        }
    json_encode(array("status"=>$status,"html"=>$html,"pag"=>$pag));
}
function deleteUser(){

}
function updateUser(){

}
function configuraAcceso(){
    
}
*/