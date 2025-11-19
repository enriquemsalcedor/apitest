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
    case "listCategories":
      listCategories();
      break;
    case "listPlaces":
      listPlaces();
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
    case "rent":
      rent();
      break;
    case "showProfile":
      showProfile();
      break;
    case "updateProfile":
      updateProfile();
      break;
    case "updatePassword":
      updatePassword();
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
                      "nombre" => $row['nombre'],
                      "apellido" => $row['apellido'],
                      "id" => $row['id'],
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
      $categoria  = (!empty($_REQUEST['categoria']) ? $_REQUEST['categoria'] : '');
      $lugar  = (!empty($_REQUEST['lugar']) ? $_REQUEST['lugar'] : '');
      $recordmin  = (!empty($_REQUEST['recordmin']) ? $_REQUEST['recordmin'] : '');
      $recordtop = (!empty($_REQUEST['recordtop']) ? $_REQUEST['recordtop'] : '');

      $query = "SELECT DISTINCT l.*, c.nombre as categoria, u.nombre as lugar, COUNT(a.idLocacion) as record 
                FROM locaciones l
                INNER JOIN lugares u ON l.idLugar = u.id
                INNER JOIN categorias c ON l.idCategoria = c.id 
                LEFT JOIN alquiler a ON a.idLocacion = l.id 
                WHERE 1=1";

      if ($categoria != ''){
        $query .= " AND l.idCategoria = $categoria";
      }
      if ($lugar != ''){
        $query .= " AND l.idLugar = $lugar";
      }

      $query .= " GROUP by a.idLocacion, l.id";

      if ($recordmin != '' && $recordtop != ''){
        $query .= " HAVING COUNT(a.idLocacion) BETWEEN $recordmin AND $recordtop ";
      }

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

      $query = "SELECT DISTINCT l.*, c.nombre as categoria, u.nombre as lugar, COUNT(a.idLocacion) as record 
                FROM locaciones l
                INNER JOIN lugares u ON l.idLugar = u.id
                INNER JOIN categorias c ON l.idCategoria = c.id 
                LEFT JOIN alquiler a ON a.idLocacion = l.id  
                WHERE l.id = $id GROUP BY a.idLocacion, l.id";
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
        'categoria' =>	$row['categoria'],
        'lugar' =>	$row['lugar'],
        'record' =>	$row['record'],
      );
      
      echo json_encode($resultado);

    }

    function listCategories(){
      global $mysqli;

      $query = "SELECT * FROM categorias ORDER BY nombre ASC";
      if(!$result = $mysqli->query($query)){
        die($mysqli->error);  
      }
      $recordsTotal = $result->num_rows;
      $resultado = array();
      while($row = $result->fetch_assoc()){
        $resultado[] = array(
          'id' 			   =>	$row['id'],
          'nombre' 	   =>	$row['nombre']
        );

      }
      $response = array(			
        "total" => intval($recordsTotal),
        "data"  => $resultado
      );
      echo json_encode($response);
    }

    function listPlaces(){
      global $mysqli;

      $query = "SELECT * FROM lugares ORDER BY nombre ASC";
      if(!$result = $mysqli->query($query)){
        die($mysqli->error);  
      }
      $recordsTotal = $result->num_rows;
      $resultado = array();
      while($row = $result->fetch_assoc()){
        $resultado[] = array(
          'id' 			   =>	$row['id'],
          'nombre' 	   =>	$row['nombre']
        );

      }
      $response = array(			
        "total" => intval($recordsTotal),
        "data"  => $resultado
      );
      echo json_encode($response);
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

    function rent(){
      global $mysqli;
      $usuario     = (!empty($_REQUEST['usuario']) ? $_REQUEST['usuario'] : '');
      $locacion     = (!empty($_REQUEST['idlocacion']) ? $_REQUEST['idlocacion'] : '');
      $fecha     = (!empty($_REQUEST['fecha']) ? $_REQUEST['fecha'] : '');

      $sql = "SELECT * FROM alquiler WHERE idLocacion = $locacion AND fecha = '$fecha'";
      $result = $mysqli->query($sql);
      $row = $result->fetch_assoc();

      if ($result->num_rows==0){
        $query 	= '	INSERT INTO	alquiler (idUsuario, idLocacion, fecha ) VALUES ( "'.$usuario.'", "'.$locacion.'", "'.$fecha.'") ';
        $result1 = $mysqli->query($query);
        if($result1){
          $response = array(			
            "message" => 'Felicidades reservacion exitosa.!!',  
            "code"    => 'ok'
          );   
        }
      }else{
        $response = array(			
          "message" => 'La localidad ya esta reservada para la fecha.!!',  
          "code"    => 'failed'
        );   
      }

      echo json_encode($response);

    }

    function showProfile(){
      global $mysqli;
      $id     = (!empty($_REQUEST['id']) ? $_REQUEST['id'] : '');

      $query = "SELECT * FROM usuarios WHERE id = $id";
      if(!$result = $mysqli->query($query)){
        die($mysqli->error);  
      }
      $row = $result->fetch_assoc();
      $resultado = array();
      $resultado[] = array(
        'id' 			  =>	$row['id'],
        'nombre' 	  =>	$row['nombre'],
        'apellido'	  =>	$row['apellido'],
        'cedula' 	  =>	$row['cedula'],
        'email' 	=>	$row['email'],
        'fecha_nac' 	=>	$row['fecha_nac']
      );
      
      echo json_encode($resultado);

    }

    function updateProfile(){
      global $mysqli;
      $nombre     = (!empty($_REQUEST['nombre']) ? $_REQUEST['nombre'] : '');
      $apellido   = (!empty($_REQUEST['apellido']) ? $_REQUEST['apellido'] : '');
      $cedula     = (!empty($_REQUEST['cedula']) ? $_REQUEST['cedula'] : '');
      $fecha_nac  = (!empty($_REQUEST['fecha_nac']) ? $_REQUEST['fecha_nac'] : '');
	    $id  = (!empty($_REQUEST['id']) ? $_REQUEST['id'] : '');

      $query 	= "	UPDATE usuarios SET
            nombre = '".$nombre."', 
            apellido = '".$apellido."', 
            cedula = '".$cedula."',
            fecha_nac = '".$fecha_nac."'
            WHERE id = $id ";

          $result1 = $mysqli->query($query);

          if($result1 == true){
            $response = array(			
                "message" => 'Perfil actualizado.!!', 
                "code" => 'ok' 
            );       
          }else{
            $response = array(			
                "message" => 'Ha ocurrido un error.!',
                "code" => 'error'
            );
          }  
     
      echo json_encode($response);
    
    }

    function updatePassword(){
      global $mysqli;
      $clave  = (!empty($_REQUEST['clave']) ? $_REQUEST['clave'] : '');
      $id     = (!empty($_REQUEST['id']) ? $_REQUEST['id'] : '');

      $query 	= "UPDATE usuarios SET clave = '".$clave."' WHERE id = $id ";
          $result = $mysqli->query($query);
          if($result){
            $response = array(			
                "message" => 'Contraseña actualizada.!!', 
                "code" => 'ok' 
            ); 
          }else{
            $response = array(			
                "message" => 'Ha ocurrido un error.!!',  
                "code" => 'error' 
            ); 
          }    

      echo json_encode($response);
    }
    //********* 
	
?>