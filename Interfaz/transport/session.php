<?php
   /* Requerimientos de Software - Prof. Alicia Salazar
    * Distribuidora SIA
    * Alexis Arguedas, Gabriela Garro, Yanil Gómez
    * -------------------------------------------------
    * session.php - Creado: 14/06/16
    * Control de la sesión actual (transporte)
    */
	
	// Connect to the db
	$dbhost = "localhost";
	$dbuser = "regular";
	$dbpass = "reque123";
	$dbname = "general";
	$dberror = "No se pudo conectar a la base de datos.";

	$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname) or die($dberror);

	if ($conn == true) {
		session_start();
		if (isset($_SESSION['usernameID'])) { // El usuario sí inició sesión
			$usernameID = $_SESSION['usernameID'];
			$username = $_SESSION['username'];
			$userType = $_SESSION['userType'];
    	}

    	//Obtener locación de la bodega
    	$queryBodega = mysqli_query($conn, "SELECT latitud, longitud from Bodega, Ruta WHERE Empleado_Usuario_idUsuario = '$usernameID' AND idBodega=Bodega_idBodega;");
    	$_SESSION['latitud'] = "";
    	$_SESSION['longitud'] = "";
    	while($row = mysqli_fetch_assoc($queryBodega)) {
    		$_SESSION['latitud'] = $row['latitud'];
    		$_SESSION['longitud'] = $row['longitud'];
    	}

    	//Obtener los pedidos de la ruta del chofer actual
		$queryPedidos = mysqli_query($conn, "SELECT idPedido, Pedido.Cliente_idCliente, nombreLocal, fecha FROM Pedido, Cliente, ClienteXRuta, Ruta WHERE Pedido.Cliente_idCliente=idCliente AND ClienteXRuta.Cliente_idCliente = idCliente AND Ruta_idRuta = idRuta AND Empleado_Usuario_idUsuario = '$usernameID' AND fecha IS NOT NULL AND entregado=0 ORDER BY fecha;");
		$arrayPedidos = array();
		while ($row = mysqli_fetch_assoc($queryPedidos)) {
			$arrayPedidos[] = [$row['idPedido'], $row['Cliente_idCliente'], $row['nombreLocal'], $row['fecha'], "<i class=\"fa fa-location-arrow\"></i> Ver", "<i class=\"fa fa-share fa-fw\"></i> Ver", "<i class=\"fa fa-check-circle fa-fw\"></i>"];
		}
		$_SESSION['arrayPedidos'] = $arrayPedidos;
	}
	else {
		echo "Can't connect to database.";
	}
?>