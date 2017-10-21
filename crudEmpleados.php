<?php
//Establezco ver todos los errores
ini_set('error_reporting', E_ALL ^ E_NOTICE);
ini_set('display_errors', 'on');
date_default_timezone_set('Europe/Madrid');
$accion = ($_POST['data']);
$aux = json_decode($accion,true);
$accion = $aux['accion'];

//$oConni = new mysqli('localhost', 'alumno', 's231234', 'INMOLOSA');
$oConni = new mysqli('213.32.71.33', 'root', 'andujar34', 'INMOLOSA');
$oConni->set_charset('utf8');

$id =$aux['id'];

if($accion==="comprobarDelete"){
  
    $status="KO";
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
   echo json_encode(array("status"=>$status,"html"=>$html));
}
if($accion==="confirmDelete"){
   
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
    echo json_encode(array("status"=>$status,"html"=>$html,"pag"=>1));
}
if($accion==="Delete"){
    $info = "Desea borrar al usuario con Id : ".$id;
    echo modalInfo($info,$accion);
}

if($accion==="comprobarEdit"){
    $status="KO";
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
            $aux = json_decode(modalInfo($info,$accion),true);
            $html = $aux['html'];
            
        }
    }else{
        $html = $stmt->errno . " " . $stmt->error;
        $status = "KO";
        }
    $stmt->close();
   echo json_encode(array("status"=>$status,"html"=>$html));
}
if($accion==="Edit"){
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
    echo json_encode(array("status"=>$status,"html"=>$html));
}
//$accion="confirmUpdate";
if($accion === "confirmUpdate"){
    $nombre = $aux['nombre'];
    $apellidos = $aux['apellidos'];
    $campo = $aux['campo'];
    $orden = $aux['orden'];
    $campo = campo($campo,$orden);
    $pag;
    $stmt = $oConni->prepare("UPDATE EMPLEADOS SET NOMBRE = ? , 
                                APELLIDOS= ? 
                                WHERE ID_EMPLEADO = ? ");
    $stmt->bind_param('ssi',$nombre,$apellidos,$id);
    if($stmt->execute()){
        $stmt->store_result();    
        $status ="ok";
        $pag = calculatePag($id,$campo,$oConni);
    }else{
        $html = $stmt->errno . " " . $stmt->error;
        $status = "KO";
        }
    $stmt->close();
    echo json_encode(array("status"=>$status,"html"=>$html,"pag"=>$pag));
    
}
if($accion==="update"){

    $nombre = $aux['nombre'];
    $apellidos = $aux['apellidos'];
    $info = "Desea actualizar los datos del usuario cuya id es: ".$id .". Siendo ahora su nombre: ".$nombre." y apellidos: ".$apellidos;
    echo modalInfo($info,$accion);
}
if($accion === "Create"){
    $id=null;
    $html = formCreate($id,"","");
    echo json_encode(array("status"=>"ok","html"=>$html));
}
if($accion==="confirmCreate"){
    $nombre = $aux['nombre'];
    $apellidos = $aux['apellidos'];
    $campo =$aux['campo'];
    $orden = $aux['orden'];
    $campo=campo($campo,$orden);
    $stmt = $oConni->prepare("INSERT INTO EMPLEADOS (NOMBRE,APELLIDOS) VALUES (?,?)");
    $stmt->bind_param('ss',$nombre,$apellidos);
    if($stmt->execute()){
        $stmt->store_result();
        $status="ok";
        $stmt = $oConni->prepare("SELECT MAX(ID_EMPLEADO) FROM EMPLEADOS");
        if($stmt->execute()){
            $stmt->store_result();
            $stmt->bind_result($Id_Empleado);
            while ($stmt->fetch()) {
               $pag=calculatePag($Id_Empleado,$campo,$oConni);
            }
        }

    }else{
        $html = $stmt->errno . " " . $stmt->error;
        $status = "KO";
        }
        $stmt->close();
    echo json_encode(array("status"=>$status,"html"=>$html,"pag"=>$pag));
   
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
function calculatePag($id,$campo,$oConni){
    $stmt = $oConni->prepare("SELECT ID_EMPLEADO, NOMBRE,APELLIDOS FROM EMPLEADOS ORDER BY $campo ");
    $posicion=0;
    $cont=0;
    if($stmt->execute()){
        $stmt->bind_result($Id_Empleado,$NomEmpleado,$Ape_Empleado);
        while ($stmt->fetch()) {
            $cont++;
            if($Id_Empleado==$id){
                $posicion = ceil($cont/15);
            }
        }
    }
    return $posicion;
}
/*function calculatePag($id,$campo){
    //$oConni = new mysqli('localhost', 'alumno', 's231234', 'INMOLOSA');
    $oConni = new mysqli('213.32.71.33', 'root', 'andujar34', 'INMOLOSA');
    $oConni->set_charset('utf8');
    $paginaAct;
    $numRegPag=15;
    $stmt = $oConni->prepare("SELECT pos/$numRegPag from (SELECT @rownum:=@rownum+1 'pos',ID_EMPLEADO, NOMBRE,APELLIDOS FROM EMPLEADOS e ,(SELECT @rownum:=0) r ORDER BY ?  )x WHERE ID_EMPLEADO=?;");
    $stmt->bind_param('ss',$campo,$id);
    if($stmt->execute()){
        $stmt->bind_result($posicion);
        while ($stmt->fetch()) {
            $paginaAct = ceil($posicion);
        }
    }
   return $paginaAct;
}*/
