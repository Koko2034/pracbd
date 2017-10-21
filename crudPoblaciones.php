<?php

require_once __DIR__ . '/cabecera15.php';


$data = json_decode($_POST['data'], true);
$accion = $_POST['accion'];
if($accion === "findPoblacion"){
    foreach($data as $key => $value){
         if($value!=""){
            is_numeric($value)?$query.=" AND ".$key."=".$value:$query.=" AND ".$key."='$value'";
         }
    }
    $query.="AND LUGARES BETWEEN 1 AND 15";
    $stmt = $oConni->prepare("SELECT PAIS,LUGAR,POBLACION,LAT,LON FROM LUGARES WHERE 1=1 ".$query.";");
    $stmt->execute();
    $stmt->store_result();
    $status="KO";
    $stmt->bind_result($pais,$lugar,$poblacion,$latitud,$longitud);
    $debug = $stmt->errno . " " . $stmt->error;
    /*$result = array();
    while($stmt->fetch()){
        $result[]=array(
            "pais" => $pais,
            "lugar" => $lugar,
            "poblacion" => $poblacion,
            "latitud" => $latitud,
            "longitud"=> $longitud
        );;
        $status="OK";
    };
*/
      echo json_encode(array("status"=>"hola","result"=>$query));
    
}

if($accion === "createPoblacion"){
    foreach($data as $key => $value){
        if(is_numeric($value)){
            $campos.=",".$key;
            $values.=",".$value;
        }else{
            $campos.=",".$key;
            $values.=",'".$value."'";
        }
    }
    $campos = "(ID".$campos.")";
    $values = "(null".$values.")";
    $stmt = $oConni->prepare("INSERT INTO LUGARES $campos VALUES $values");
        $stmt->execute();  
   $debug = $stmt->errno . " " . $stmt->error;
       $status = "OK";
    if($stmt->affect_rows==0){
        $status="KO"; 
    }
    echo json_encode(array("status"=>$status,"debug"=>$debug));
}

if($accion === "obtenerPaises"){
    $stmt = $oConni->prepare("SELECT PAIS FROM LUGARES group by PAIS");
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