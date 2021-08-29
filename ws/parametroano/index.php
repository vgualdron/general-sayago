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
                                    paan.paan_id as id,
                                    paan.paan_descripcion as descripcion,
                                    paan.paan_valor as valor,
                                    paan.paan_ano as ano
                                    FROM pinchetas_general.parametroano paan
                                    where paan.paan_id = ?
                                    order by paan.paan_descripcion; ");
                    							
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
                                    paan.paan_id as id,
                                    paan.paan_descripcion as descripcion,
                                    paan.paan_valor as valor,
                                    paan.paan_ano as ano
                                    FROM pinchetas_general.parametroano paan
                                    order by paan.paan_descripcion;");
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
      $valor = $frm['valor'];
      $ano = $frm['ano'];
      $url = $frm['url'];
      $registradopor = openCypher('decrypt', $frm['token']);
      $date = date("Y-m-d H:i:s");
      $cero = "1";
      $sql = "INSERT INTO 
              pinchetas_general.parametroano (paan_descripcion, paan_ano, paan_valor, paan_fechainicio, paan_fechafin, para_id, paan_registradopor, paan_fechacambio)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?); ";
            
      $sql = $conexion->prepare($sql);
      $sql->bindValue(1, $descripcion);
      $sql->bindValue(2, $ano);
      $sql->bindValue(3, $valor);
      $sql->bindValue(4, $date);
      $sql->bindValue(5, $date);
      $sql->bindValue(6, $cero);
      $sql->bindValue(7, $registradopor);
      $sql->bindValue(8, $date);
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
      $valor = $frm['valor'];
      $ano = $frm['ano'];
      $url = $frm['url'];
      $registradopor = openCypher('decrypt', $frm['token']);
      $date = date("Y-m-d H:i:s");
      
      $sql = "UPDATE pinchetas_general.parametroano 
              SET paan_descripcion = ?, paan_ano = ?, paan_valor = ?, paan_registradopor = ?, paan_fechacambio = ?
              WHERE paan_id = ?; ";
            
      $sql = $conexion->prepare($sql);
      $sql->bindValue(1, $descripcion);
      $sql->bindValue(2, $ano);
      $sql->bindValue(3, $valor);
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
      
      $sql = "CALL procedimiento_eliminar_parametroano(?, ?); ";
            
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
