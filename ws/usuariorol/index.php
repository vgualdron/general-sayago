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
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      
      $idusuario = $frm['idusuario'];
      $roles = $frm['roles'];
        
      $registradopor = openCypher('decrypt', $frm['token']);
      $date = date("Y-m-d H:i:s");
        
      $sql = "DELETE FROM pinchetas_general.usuariorol
              WHERE usua_id = ?; ";
            
      $sql = $conexion->prepare($sql);
      $sql->bindValue(1, $idusuario);
      $result = $sql->execute();
      if($result) {
          foreach ($roles as $clave => $rol) {
              $sql2 = "INSERT INTO 
                  pinchetas_general.usuariorol (usua_id, rol_id, usro_registradopor, usro_fechacambio)
                  VALUES (?, ?, ?, ?); ";

              $sql2 = $conexion->prepare($sql2);
              $sql2->bindValue(1, $idusuario);
              $sql2->bindValue(2, $rol["id"]);
              $sql2->bindValue(3, $registradopor);
              $sql2->bindValue(4, $date);
              $result2 = $sql2->execute();
              $postId = $conexion->lastInsertId();
          }
          $input['id'] = $postId;
          $input['mensaje'] = "Cambios gurdados con éxito";
          header("HTTP/1.1 200 OK");
          echo json_encode($input);
          exit();
          
  	  } else {
        $input['id'] = $postId;
        $input['mensaje'] = "Error eliminando";
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