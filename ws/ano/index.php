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
                    							ano.ano_id as id,
                    							ano.ano_descripcion as descripcion
                    							FROM bd_washinhouse.ano ano
                    							where ano.ano_id = ?
                    							order by ano.ano_descripcion; ");
                    							
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
  	  }
      else {
        $sql = $conexion->prepare("SELECT distinct
                    							ano.ano_id as value,
                    							ano.ano_descripcion as text
                    							FROM bd_washinhouse.ano ano
                    							order by ano.ano_descripcion desc; ");
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
      $registradopor = openCypher('decrypt', $frm['token']);
      $date = date("Y-m-d H:i:s");
      
      $sql = "INSERT INTO 
              bd_washinhouse.ano (ano_id, ano_descripcion, ano_registradopor, ano_fechacambio)
              VALUES (?, ?, ?, ?); ";
            
      $sql = $conexion->prepare($sql);
      $sql->bindValue(1, $descripcion);
      $sql->bindValue(2, $descripcion);
      $sql->bindValue(3, $registradopor);
      $sql->bindValue(4, $date);
      $sql->execute();
      $postId = $conexion->lastInsertId();
      
      if($postId) {
        $input['id'] = $postId;
        $input['mensaje'] = "Registrado con éxito";
        header("HTTP/1.1 200 OK");
        echo json_encode($input);
        exit();
  	  } else {
        $input['id'] = $postId;
        $input['mensaje'] = "Error registrando marca, ya existe el registro";
        header("HTTP/1.1 400 Bad Request");
        echo json_encode($input);
        exit();
  	  }
  }
  //Actualizar
  else if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
      $input = $_GET;
      
      $id = $frm['id'];
      $descripcion = $frm['descripcion'];
      $registradopor = openCypher('decrypt', $frm['token']);
      $date = date("Y-m-d H:i:s");
      
      $sql = "UPDATE bd_washinhouse.ano  
              SET ano_id = ?, ano_descripcion = ?, ano_registradopor = ?, ano_fechacambio = ?
              WHERE ano_id = ?; ";
            
      $sql = $conexion->prepare($sql);
      $sql->bindValue(1, $descripcion);
      $sql->bindValue(2, $descripcion);
      $sql->bindValue(3, $registradopor);
      $sql->bindValue(4, $date);
      $sql->bindValue(5, $id);
      $result = $sql->execute();
      if($result) {
        $input['id'] = $postId;
        $input['mensaje'] = "Registrado con éxito";
        header("HTTP/1.1 200 OK");
        echo json_encode($input);
        exit();
  	  } else {
        $input['id'] = $postId;
        $input['mensaje'] = "Error modificando marca";
        header("HTTP/1.1 400 Bad Request");
        echo json_encode($input);
        exit();
  	  }
  }
  // Eliminar
  else if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
      $input = $_GET;
  
      $id = $input['id'];
      $descripcion = $frm['descripcion'];
      $registradopor = openCypher('decrypt', $frm['token']);
      $date = date("Y-m-d H:i:s");
             
      $sql = "DELETE FROM bd_washinhouse.ano 
              WHERE ano_id = ?; ";
            
      $sql = $conexion->prepare($sql);
      $sql->bindValue(1, $id);
      $result = $sql->execute();
      if($result) {
        $input['id'] = $postId;
        $input['mensaje'] = "Eliminado con éxito";
        header("HTTP/1.1 200 OK");
        echo json_encode($input);
        exit();
  	  } else {
        $input['id'] = $postId;
        $input['mensaje'] = "Error eliminando marca";
        header("HTTP/1.1 400 Bad Request");
        echo json_encode($input);
        exit();
  	  }
  }

} catch (Exception $e) {
    echo 'Excepción capturada: ', $e->getMessage(), "\n";
}

//En caso de que ninguna de las opciones anteriores se haya ejecutado
// header("HTTP/1.1 400 Bad Request");

?>