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
      if (isset($_GET['idUsuario'])) {
        $sql = $conexion->prepare("SELECT distinct
                                    rol.rol_id as id,
                                    rol.rol_descripcion as descripcion,
                                    rol.rol_orden as orden,
                                    rol.rol_visible as visible
                                    FROM pinchetas_general.rol rol
                                    inner join pinchetas_general.usuariorol usro on (usro.rol_id = rol.rol_id)
                                    inner join pinchetas_general.usuario usua on (usua.usua_id = usro.usua_id)
                                    where rol.rol_visible = 'SI'
                                    and usua.usua_id = ?
                                    order by rol.rol_orden;");
        $sql->bindValue(1, $_GET['idUsuario']); 
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        header("HTTP/1.1 200 OK");
        echo json_encode( $sql->fetchAll()  );
        exit();
  	  } else if (isset($_GET['id'])) {
        $sql = $conexion->prepare("SELECT distinct
                                    rol.rol_id as id,
                                    rol.rol_descripcion as descripcion,
                                    rol.rol_orden as orden,
                                    rol.rol_visible as visible
                                    FROM pinchetas_general.rol rol
                                    where rol.rol_id = ?
                                    order by rol.rol_orden; ");
                    							
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
                                    rol.rol_id as id,
                                    rol.rol_descripcion as descripcion,
                                    rol.rol_orden as orden,
                                    rol.rol_visible as visible
                                    FROM pinchetas_general.rol rol
                                    where rol.rol_visible = 'SI'
                                    order by rol.rol_orden;");
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
      $orden = $frm['orden'];
      $visible = $frm['visible'];
      $registradopor = openCypher('decrypt', $frm['token']);
      $date = date("Y-m-d H:i:s");
      
      $sql = "INSERT INTO 
              pinchetas_general.rol (rol_descripcion, rol_orden, rol_visible, rol_registradopor, rol_fechacambio)
              VALUES (?, ?, ?, ?, ?); ";
            
      $sql = $conexion->prepare($sql);
      $sql->bindValue(1, $descripcion);
      $sql->bindValue(2, $orden);
      $sql->bindValue(3, $visible);
      $sql->bindValue(4, $registradopor);
      $sql->bindValue(5, $date);
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
      $visible = $frm['visible'];
      $registradopor = openCypher('decrypt', $frm['token']);
      $tiposVehiculo = $frm['tiposVehiculo'];
      $date = date("Y-m-d H:i:s");
      
      $sql = "UPDATE pinchetas_general.rol  
              SET rol_descripcion = ?, rol_orden = ?, rol_visible = ?, rol_registradopor = ?, rol_fechacambio = ?
              WHERE rol_id = ?; ";
            
      $sql = $conexion->prepare($sql);
      $sql->bindValue(1, $descripcion);
      $sql->bindValue(2, $orden);
      $sql->bindValue(3, $visible);
      $sql->bindValue(4, $registradopor);
      $sql->bindValue(5, $date);
      $sql->bindValue(6, $id);
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
      
      $sql = "CALL procedimiento_eliminar_rol(?, ?); ";
            
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