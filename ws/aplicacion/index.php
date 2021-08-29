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
      if (isset($_GET['id'])) {
        $sql = $conexion->prepare("SELECT distinct
                                    apli.apli_id as id,
                                    apli.apli_descripcion as descripcion,
                                    apli.apli_icono as icono,
                                    apli.apli_orden as orden,
                                    apli.apli_url as url
                                    FROM pinchetas_general.aplicacion apli
                                    where apli.apli_id = ?
                                    order by apli.apli_orden; ");
                    							
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
                                    apli.apli_id as id,
                                    apli.apli_id as value,
                                    apli.apli_descripcion as descripcion,
                                    apli.apli_descripcion as text,
                                    apli.apli_icono as icono,
                                    apli.apli_orden as orden,
                                    apli.apli_url as url
                                    FROM pinchetas_general.aplicacion apli
                                    order by apli.apli_orden;");
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        header("HTTP/1.1 200 OK");
        echo json_encode( $sql->fetchAll()  );
        exit();
  	  }
  }
  // Crear un nuevo post
  else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $input = $_POST;
          
      $descripcion = $frm['descripcion'];
      $icono = $frm['icono'];
      $orden = $frm['orden'];
      $url = $frm['url'];
      $registradopor = openCypher('decrypt', $frm['token']);
      $date = date("Y-m-d H:i:s");
      
      $sql = "INSERT INTO 
              pinchetas_general.aplicacion (apli_descripcion, apli_icono, apli_orden, apli_url, apli_registradopor, apli_fechacambio)
              VALUES (?, ?, ?, ?, ?, ?); ";
            
      $sql = $conexion->prepare($sql);
      $sql->bindValue(1, $descripcion);
      $sql->bindValue(2, $icono);
      $sql->bindValue(3, $orden);
      $sql->bindValue(4, $url);
      $sql->bindValue(5, $registradopor);
      $sql->bindValue(6, $date);
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
      $icono = $frm['icono'];
      $orden = $frm['orden'];
      $url = $frm['url'];
      $registradopor = openCypher('decrypt', $frm['token']);
      $date = date("Y-m-d H:i:s");
      
      $sql = "UPDATE pinchetas_general.aplicacion 
              SET apli_descripcion = ?, apli_orden = ?, apli_url = ?, apli_icono = ?, apli_registradopor = ?, apli_fechacambio = ?
              WHERE apli_id = ?; ";
            
      $sql = $conexion->prepare($sql);
      $sql->bindValue(1, $descripcion);
      $sql->bindValue(2, $orden);
      $sql->bindValue(3, $url);
      $sql->bindValue(4, $icono);
      $sql->bindValue(5, $registradopor);
      $sql->bindValue(6, $date);
      $sql->bindValue(7, $id);
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
      
      $sql = "CALL procedimiento_eliminar_aplicacion(?, ?); ";
            
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