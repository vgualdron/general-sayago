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
$urlaplicacion = $frm['url'];
// $idaplicacion = "2";

$conexion ->query("SET NAMES 'utf8';");

$use = $conexion->prepare("SELECT distinct
			func.* 
			FROM pinchetas_general.aplicacion apli
			inner join pinchetas_general.funcionalidad func on (func.apli_id = apli.apli_id)
			inner join pinchetas_general.rolfuncionalidad rofu on (rofu.func_id = func.func_id)
			inner join pinchetas_general.rol rol on (rofu.rol_id = rol.rol_id)
			inner join pinchetas_general.usuariorol usro on (usro.rol_id = rol.rol_id)
			inner join pinchetas_general.usuario usua on (usro.usua_id = usua.usua_id)
			where usua.pege_id = ?
			and apli.apli_url like '%".$urlaplicacion."%'
			and func.func_idpadre is null
			order by func.func_idpadre,func.func_orden;"); 
							
$use->bindValue(1, $idusuario);
// $use->bindValue(2, $idaplicacion);
$use ->execute();
$count = $use->rowCount();
$row = $use->fetchAll();

$data = (object) array();
$array = array();

if ($count > 0) {
	foreach($row as $registro){
		
		
		if ($registro['func_tipo'] == 'ETIQUETA') {
			$object = (object) array();
			$object->title = true;
			$object->name =  $registro['func_descripcion'];
			$object->class = "";
			$object2 = (object) array();
			$object2->element = "";
			$object2->atributes = (object) array();
			$object->wrapper = $object2;
			
			array_push($array, $object);
		} else if ($registro['func_tipo'] == 'FUNCIONALIDAD') {
			$object = (object) array();
			$object->name =  $registro['func_descripcion'];
			$object->url = $registro['func_url'];
			$object->icon = $registro['func_icono'];
			
			array_push($array, $object);
		} else if ($registro['func_tipo'] == 'ICONO') {
			$object = (object) array();
			$object->name =  $registro['func_descripcion'];
			$object->url = $registro['func_url'];
			$object->icon = $registro['func_icono'];
			
			$children = array();
			
			$use2 = $conexion->prepare("SELECT distinct
							func.* 
							FROM pinchetas_general.aplicacion apli
							inner join pinchetas_general.funcionalidad func on (func.apli_id = apli.apli_id)
							inner join pinchetas_general.rolfuncionalidad rofu on (rofu.func_id = func.func_id)
							inner join pinchetas_general.rol rol on (rofu.rol_id = rol.rol_id)
							inner join pinchetas_general.usuariorol usro on (usro.rol_id = rol.rol_id)
							inner join pinchetas_general.usuario usua on (usro.usua_id = usua.usua_id)
							where usua.pege_id = ?
							and apli.apli_id = ?
							and func.func_idpadre = ?
							order by func.func_idpadre,func.func_orden;"); 
							
			$use2->bindValue(1, $idusuario);
			$use2->bindValue(2, $idaplicacion);
			$use2->bindValue(3, $registro['func_id']);
		
			$use2 ->execute();
			$count2 = $use2->rowCount();
			$row2 = $use2->fetchAll();
			
			if ($count2 > 0) {
				foreach($row2 as $registro2){
					$objChildren = (object) array();
			
					$objChildren->name =  $registro2['func_descripcion'];
					$objChildren->url = $registro2['func_url'];
					$objChildren->icon = $registro2['func_icono'];
					
					array_push($children, $objChildren);
				}
			}
			
			$object->children = $children;
			
			array_push($array, $object);
		}
		
		
	}
}

$data->funcionalidades =  $array;
$data->estado = $count > 0 ? "OK" : "ERROR";
print_r(json_encode($data));

?>