<?php

require_once __DIR__ . '/cabecera15.php';


$data = json_decode($_POST['data'], true);
$accion = $_POST['accion'];
if($accion === "findPoblacion"){
    foreach($data as $key => $value){
        $query.=" AND ".$key=$value;
    }
    $stmt = $oConni->prepare("SELECT PAIS,LUGAR,POBLACION,LATITUD,LONGITUD FROM LUGARES WHERE 1=1 ".$query.";");
    $stmt->execute();
    $stmt->store_result();
    $status="KO";
    $stmt->bind_result($pais,$lugar,$poblacion,$latitud,$longitud);
    $debug = $stmt->errno . " " . $stmt->error;
    $cont=0;
    while($stmt->fetch()){
        $result =array(
            $cont=>array(
                "pais" => $pais,
                "lugar" => $lugar,
                "poblacion" => $poblacion,
                "latitud" => $latitud,
                "longitud"=> $longitud
            )
            );
        $cont++;
        $status="OK";
    };
    echo json_encode(array("status"=>$status,"result"=>$result));
}
if($accion === "createPoblacion"){
    foreach($data as $key => $value){
        $campos.=$key.",";
        $values.=$value.",";
    }
    $stmt = $oConni->prepare("INSERT INTO LUGARES (".substr($campos,0,strlen($campo)-1).") VALUES (". substr($values,0,strlen($values)-1).")");
        $stmt->execute();  
   $debug = $stmt->errno . " " . $stmt->error;
       $status = "OK";
    if($stmt->affect_rows==0){
        $status="KO"; 
    }
    echo json_encode(array("status"=>$status,"debug"=>$debug));
}
if($accion === "obtenerPaises"){
    $stmt = $oConni->prepare("SELECT PAIS FROM LUGARES");
    $stmt->execute();
    $stmt->store_result();
    $status="KO";
    $stmt->bind_result($pais);
    $debug = $stmt->errno . " " . $stmt->error;
    $paises=array();
    while($stmt->fetch()){
        array_push($paises,$pais);
        $status="OK";
    };
    echo json_encode(array("status"=>"ok","paises"=>$paises));
}
?>