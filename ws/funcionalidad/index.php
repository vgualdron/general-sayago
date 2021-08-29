<?php
session_start();
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

require_once("../conexion.php");
require_once("../encrypted.php");
$conexion = new Conexion();

$frm = json_decode(file_get_contents('php://input'), true);

try {
  
  //  listar todos los posts o solo uno
  if ($_SERVER['REQUEST_METHOD'] == 'GET') {
      
      if (isset($_GET['idRol']) && $_GET['idAplicacion']) {
         // no se puede modificar por que es de los que se usa para asgnar funcionalidades a rol
        $sql = $conexion->prepare("SELECT distinct
                                    func.func_id as id,
                                    func.func_id as value,
                                    func.func_descripcion as descripcion,
                                    func.func_descripcion as text,
                                    func.func_tipo as tipo,
                                    func.func_url as url,
                                    func.func_icono as icono,
                                    func.func_orden as orden,
                                    func.apli_id as idaplicacion,
                                    apli.apli_descripcion as aplicacion,
                                    func.func_idpadre as idfuncionalidadpadre
                                    FROM pinchetas_general.funcionalidad func
                                    inner join pinchetas_general.rolfuncionalidad rofu on (rofu.func_id = func.func_id)
                                    inner join pinchetas_general.rol rol on (rol.rol_id = rofu.rol_id)
                                    inner join pinchetas_general.aplicacion apli on (apli.apli_id = func.apli_id)
                                    where rofu.rol_id = ?
                                    and func.apli_id = ?
                                    order by func.func_descripcion; ");
        $sql->bindValue(1, $_GET['idRol']);  
        $sql->bindValue(2, $_GET['idAplicacion']);  
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        header("HTTP/1.1 200 OK");
        echo json_encode( $sql->fetchAll() );
        exit();
  	  } else if (isset($_GET['idAplicacion'])) {
        // no se puede modificar por que es de los que se usa para asgnar funcionalidades a rol
        $sql = $conexion->prepare("SELECT distinct
                                    func.func_id as id,
                                    func.func_id as value,
                                    func.func_descripcion as descripcion,
                                    func.func_descripcion as text,
                                    func.func_tipo as tipo,
                                    func.func_url as url,
                                    func.func_icono as icono,
                                    func.func_orden as orden,
                                    func.apli_id as idaplicacion,
                                    func.func_idpadre as idfuncionalidadpadre,
                                    apli.apli_descripcion as aplicacion
                                    FROM pinchetas_general.funcionalidad func
                                    inner join pinchetas_general.aplicacion apli using(apli_id)
                                    where func.apli_id = ?
                                    order by apli.apli_descripcion,func.func_descripcion; ");
        $sql->bindValue(1, $_GET['idAplicacion']);  
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        header("HTTP/1.1 200 OK");
        echo json_encode( $sql->fetchAll() );
        exit();
  	  } else if (isset($_GET['id'])) {
        $sql = $conexion->prepare("SELECT distinct
                                    func.func_id as id,
                                    func.func_id as value,
                                    func.func_descripcion as descripcion,
                                    func.func_descripcion as text,
                                    func.func_tipo as tipo,
                                    func.func_url as url,
                                    func.func_icono as icono,
                                    func.func_orden as orden,
                                    func.apli_id as idaplicacion,
                                    func.func_idpadre as idfuncionalidadpadre,
                                    apli.apli_descripcion as aplicacion
                                    FROM pinchetas_general.funcionalidad func
                                    inner join pinchetas_general.aplicacion apli using(apli_id)
                                    where func.func_id = ?
                                    order by apli.apli_descripcion,func.func_descripcion; ");
                    							
        $sql->bindValue(1, $_GET['id']);                                
        $sql->execute();
        header("HTTP/1.1 200 OK");
        $result = $sql->fetch(PDO::FETCH_ASSOC);
        if ($result == false) {
          $data = (object) array();
          $data->mensaje = "No se encontraron registros.";
          header("HTTP/1.1 400 Bad Request");
          echo json_encode( $data );
          exit();
        } else {
          echo json_encode($result);
          exit();
        }
  	  } else {
        $sql = $conexion->prepare("SELECT distinct
                                    func.func_id as id,
                                    func.func_id as value,
                                    func.func_descripcion as descripcion,
                                    func.func_descripcion as text,
                                    func.func_tipo as tipo,
                                    func.func_url as url,
                                    func.func_icono as icono,
                                    func.func_orden as orden,
                                    func.apli_id as idaplicacion,
                                    func.func_idpadre as idfuncionalidadpadre,
                                    apli.apli_descripcion as aplicacion
                                    FROM pinchetas_general.funcionalidad func
                                    inner join pinchetas_general.aplicacion apli using(apli_id)
                                    order by apli.apli_descripcion,func.func_descripcion; ");
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        header("HTTP/1.1 200 OK");
        echo json_encode( $sql->fetchAll() );
        exit();
  	  }
  }
  // Crear un nuevo post
  else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $input = $_POST;
          
      $descripcion = $frm['descripcion'];
      $orden = $frm['orden'];
      $icono = $frm['icono'];
      $tipo = $frm['tipo'];
      $url = $frm['url'];
      $idPadre = $frm['idfuncionalidadpadre'];
      $idAplicacion = $frm['idAplicacion'];
      $registradopor = openCypher('decrypt', $frm['token']);
      $date = date("Y-m-d H:i:s");
      
      $sql = "INSERT INTO 
              pinchetas_general.funcionalidad (func_descripcion, func_tipo, func_url, func_icono, func_orden, apli_id, func_idpadre, func_registradopor, func_fechacambio)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?); ";
            
      $sql = $conexion->prepare($sql);
      $sql->bindValue(1, $descripcion);
      $sql->bindValue(2, $tipo);
      $sql->bindValue(3, $url);
      $sql->bindValue(4, $icono);
      $sql->bindValue(5, $orden);
      $sql->bindValue(6, $idAplicacion);
      $sql->bindValue(7, $idPadre);
      $sql->bindValue(8, $registradopor);
      $sql->bindValue(9, $date);
      $sql->execute();
      $postId = $conexion->lastInsertId();
 

    $input['id'] = $postId;
    $input['mensaje'] = "Registrado con éxito";
    header("HTTP/1.1 200 OK");
    echo json_encode($input);
    exit();
  	  
  }
  //Actualizar
  else if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
      $input = $_GET;
      
      $id = $frm['id'];
      $descripcion = $frm['descripcion'];
      $orden = $frm['orden'];
      $icono = $frm['icono'];
      $tipo = $frm['tipo'];
      $url = $frm['url'];
      $idPadre = $frm['idfuncionalidadpadre'];
      $idAplicacion = $frm['idAplicacion'];
      $registradopor = openCypher('decrypt', $frm['token']);
      $date = date("Y-m-d H:i:s");
      
      $sql = "UPDATE pinchetas_general.funcionalidad 
              SET func_descripcion = ?, func_tipo = ?, func_url = ?, func_icono = ?, func_orden = ?, apli_id = ?, func_idpadre = ?, func_registradopor = ?, func_fechacambio = ?
              WHERE func_id = ?; ";
            
      $sql = $conexion->prepare($sql);
      $sql->bindValue(1, $descripcion);
      $sql->bindValue(2, $tipo);
      $sql->bindValue(3, $url);
      $sql->bindValue(4, $icono);
      $sql->bindValue(5, $orden);
      $sql->bindValue(6, $idAplicacion);
      $sql->bindValue(7, $idPadre);
      $sql->bindValue(8, $registradopor);
      $sql->bindValue(9, $date);
      $sql->bindValue(10, $id);
      $result = $sql->execute();
      
      if($result) {
        $input['id'] = $result;
        $input['mensaje'] = "Actualizado con éxito";
        header("HTTP/1.1 200 OK");
        echo json_encode($input);
        exit();
  	  } else {
        $input['id'] = $result;
        $input['mensaje'] = "Error actualizando";
        header("HTTP/1.1 400 Bad Request");
        echo json_encode($input);
        exit();
  	  }
  	  
  }
  // Eliminar
  else if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
     $input = $_GET;
      $id = $input['id'];
      $registradopor = openCypher('decrypt', $input['token']);

      $date = date("Y-m-d H:i:s");
      
      $sql = "CALL procedimiento_eliminar_funcionalidad(?, ?); ";
            
      $sql = $conexion->prepare($sql);
      $sql->bindValue(1, $id);
      $sql->bindValue(2, $registradopor);
      $result = $sql->execute();
      if($result) {
        $output['id'] = $postId;
        $output['mensaje'] = "Eliminado con éxito";
        header("HTTP/1.1 200 OK");
        echo json_encode($output);
        exit();
  	  } else {
        $output['id'] = $postId;
        $output['mensaje'] = "Error eliminando";
        header("HTTP/1.1 400 Bad Request");
        echo json_encode($output);
        exit();
  	  }
  }

} catch (Exception $e) {
    echo 'Excepción capturada: ', $e->getMessage(), "\n";
}

//En caso de que ninguna de las opciones anteriores se haya ejecutado
// header("HTTP/1.1 400 Bad Request");

?>