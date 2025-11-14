<?php

	header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
	header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
	header("Allow: GET, POST, OPTIONS, PUT, DELETE");
    
    include("../conexion.php");

	$oper = '';
	if (isset($_REQUEST['oper'])) {
		$oper = $_REQUEST['oper'];
	}
	
	switch($oper){
		case "login": 
			login();
      break;
    case "userRegister":
      userRegister();
      break;
    case "listLocations":
      listLocations();
      break;
    case "getLocation":
      getLocation();
      break;
    case "addFavorites":
      addFavorites();
      break;
    case "listFavorites":
      listFavorites();
      break;
    case "deleteFavorites":
      deleteFavorites();
      break;
		default:
      echo "{failure:true}";
      break;
	}	
	

    function login(){
      global $mysqli;
      $usuario   = (!empty($_REQUEST['usuario']) ? $_REQUEST['usuario'] : '');
      $clave     = (!empty($_REQUEST['clave']) ? $_REQUEST['clave'] : '');

      $sql = "SELECT * FROM usuarios WHERE usuario = '".$usuario."' AND clave='".$clave."'" ;
      $result = $mysqli->query($sql);

      $row = $result->fetch_assoc();
      if ($row != null){	
              if($row['status'] == 'Activo'){
                  $response = array(			
                      "message" => 'Bienvenido.!!',
                      "status" => $row['status']
                  );
              }else{
                  $response = array(			
                  "message" => 'Usuario inactivo.!',
                  "status" => $row['status']
                  );
              }          
          }else{
              $response = array(			
              "message" => 'Usuario no existe.!',
              "status" => ''
              );
          }      
      echo json_encode($response);
    
    }

    function userRegister(){
      global $mysqli;
      $email      = (!empty($_REQUEST['email']) ? $_REQUEST['email'] : '');
      $clave      = (!empty($_REQUEST['clave']) ? $_REQUEST['clave'] : '');
      $nombre     = (!empty($_REQUEST['nombre']) ? $_REQUEST['nombre'] : '');
      $apellido   = (!empty($_REQUEST['apellido']) ? $_REQUEST['apellido'] : '');
      $cedula     = (!empty($_REQUEST['cedula']) ? $_REQUEST['cedula'] : '');
      $fecha_nac  = (!empty($_REQUEST['fecha_nac']) ? $_REQUEST['fecha_nac'] : '');

      $sql = "SELECT * FROM usuarios WHERE usuario = '".$email."'" ;
      $result = $mysqli->query($sql);

		  if ($result->num_rows==0){	
          $query 	= '	INSERT INTO	usuarios (usuario, email, clave, status, nombre, apellido, cedula, fecha_nac) 
                                VALUES ( "'.$email.'", "'.$email.'", "'.$clave.'", "Activo", "'.$nombre.'", "'.$apellido.'", "'.$cedula.'", "'.$fecha_nac.'") ';
          $result1 = $mysqli->query($query);

          if($result1 == true){
            $response = array(			
                "message" => 'Usuario registrado.!!', 
                "code" => 'ok' 
            );       
          }else{
            $response = array(			
                "message" => 'Ha ocurrido un error.!',
                "code" => 'error'
            );
          }      
      }else{
          $response = array(			
          "message" => 'Usuario ya existe.!',
          "code" => 'failed'
          );
      }      
     
      echo json_encode($response);
    
    }

    function listLocations(){
      global $mysqli;

      $query = "SELECT l.*, c.nombre as categoria, u.nombre as lugar FROM locaciones l
                INNER JOIN lugares u ON l.idLugar = u.id
                INNER JOIN categorias c ON l.idCategoria = c.id";
      if(!$result = $mysqli->query($query)){
        die($mysqli->error);  
      }
      $recordsTotal = $result->num_rows;
      $resultado = array();
      while($row = $result->fetch_assoc()){
        $resultado[] = array(
          'id' 			   =>	$row['id'],
          'nombre' 	   =>	$row['nombre'],
          'precio'	   =>	$row['precio'],
          'imagen' 	   =>	$row['imagen'],
          'lugar' 	   =>	$row['lugar'],
          'categoria'  =>	$row['categoria'],
          'status' 	   =>	$row['status'],
        );

      }
      $response = array(			
        "total" => intval($recordsTotal),
        "data"  => $resultado
      );
      echo json_encode($response);

    }

    function getLocation(){
      global $mysqli;
      $id     = (!empty($_REQUEST['id']) ? $_REQUEST['id'] : '');

      $query = "SELECT * FROM locaciones WHERE id = $id";
      if(!$result = $mysqli->query($query)){
        die($mysqli->error);  
      }
      $row = $result->fetch_assoc();
      $resultado = array();
      $resultado[] = array(
        'id' 			  =>	$row['id'],
        'nombre' 	  =>	$row['nombre'],
        'precio'	  =>	$row['precio'],
        'imagen' 	  =>	$row['imagen'],
        'imagen1' 	=>	$row['imagen1'],
        'imagen2' 	=>	$row['imagen2'],
        'imagen3' 	=>	$row['imagen3'],
        'status' 	  =>	$row['status'],
      );
      
      echo json_encode($resultado);

    }

    function addFavorites(){
      global $mysqli;
      $id     = (!empty($_REQUEST['id']) ? $_REQUEST['id'] : '');
      $usuario = (!empty($_REQUEST['usuario']) ? $_REQUEST['usuario'] : '');

      $sql = "SELECT * FROM favoritos WHERE idUsuario = '".$usuario."' AND idLocacion = '".$id."'" ;
      $result = $mysqli->query($sql);

		  if ($result->num_rows==0){	
          $query 	= '	INSERT INTO	favoritos (idUsuario, idLocacion) VALUES ( "'.$usuario.'", "'.$id.'") ';
          $result1 = $mysqli->query($query);

          if($result1 == true){
            $response = array(			
                "message" => 'Agregado a favoritos.!!',  
            );       
          }else{
            $response = array(			
                "message" => 'Ha ocurrido un error.!',
            );
          }      
      }else{
          $response = array(			
          "message" => 'Favorito ya existe.!',
          );
      }      
     
      echo json_encode($response);
    
    }

    function deleteFavorites(){
      global $mysqli;
      $id     = (!empty($_REQUEST['id']) ? $_REQUEST['id'] : '');
      $usuario = (!empty($_REQUEST['usuario']) ? $_REQUEST['usuario'] : '');

      $sql = "DELETE FROM favoritos WHERE idUsuario = '".$usuario."' AND idLocacion = '".$id."'" ;
      $result = $mysqli->query($sql);

		  if ($result){	      
        $response = array(			
            "message" => 'Favorito eliminado.!!',  
        );        
      }else{
          $response = array(			
          "message" => 'Ha ocurrido un error.!',
          );
      }      
      echo json_encode($response);
    
    }

    function listFavorites(){
      global $mysqli;
      $usuario     = (!empty($_REQUEST['usuario']) ? $_REQUEST['usuario'] : '');

      $query = "SELECT f.id, l.nombre, l.precio, l.imagen, l.status  FROM favoritos f 
                LEFT  JOIN locaciones l ON l.id = f.idLocacion
                WHERE f.idUsuario = $usuario";
      
      if(!$result = $mysqli->query($query)){
        die($mysqli->error);  
      }
      while($row = $result->fetch_assoc()){
        $resultado[] = array(
          'id' 			=>	$row['id'],
          'nombre' 	=>	$row['nombre'],
          'precio'	=>	$row['precio'],
          'imagen' 	=>	$row['imagen'],
          'status' 	=>	$row['status'],
        );

      }      
      echo json_encode($resultado);

    }
    //********* */

	
?>