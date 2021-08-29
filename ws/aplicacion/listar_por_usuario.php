<?php
session_start();
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
require_once("../conexion.php");
require_once("../encrypted.php");
$conexion = new Conexion();

$frm = json_decode(file_get_contents('php://input'), true);
// echo "**".$frm;
$idusuario = openCypher('decrypt', $frm['token']);

$conexion ->query("SET NAMES 'utf8';");

$use = $conexion->prepare("SELECT distinct
							apli.* 
							FROM bd_general.aplicacion apli
							inner join bd_general.funcionalidad func on (func.apli_id = apli.apli_id)
							inner join bd_general.rolfuncionalidad rofu on (rofu.func_id = func.func_id)
							inner join bd_general.rol rol on (rofu.rol_id = rol.rol_id)
							inner join bd_general.usuariorol usro on (usro.rol_id = rol.rol_id)
							inner join bd_general.usuario usua on (usro.usua_id = usua.usua_id)
							where usua.pege_id = ?
							order by apli.apli_orden;"); 
							
$use->bindValue(1, $idusuario);
$use ->execute();
$count = $use->rowCount();
$row = $use->fetchAll();

$data = (object) array();
$array = array();

if ($count > 0) {
	foreach($row as $registro){
		$object = (object) array();
		$object->id =  $registro['apli_id'];
		$object->descripcion =  $registro['apli_descripcion'];
		$object->url =  $registro['apli_url'];
		$object->icono =  $registro['apli_icono'];
		$object->orden =  $registro['apli_orden'];
		array_push($array, $object);
	}
}

$data->aplicaciones =  $array;
$data->estado = $count > 0 ? "OK" : "ERROR";
print_r(json_encode($data));

?>