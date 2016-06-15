<?php
   /* Requerimientos de Software - Prof. Alicia Salazar
    * Distribuidora SIA
    * Alexis Arguedas, Gabriela Garro, Yanil Gómez
    * -------------------------------------------------
    * session.php - Creado: 13/06/16
    * Control de la sesión actual (admin)
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

			$queryClientes = mysqli_query($conn, "SELECT idCliente, nombreLocal, Bodega_idBodega as bodega FROM cliente, clienteXRuta, Ruta WHERE idCliente = Cliente_idCliente AND Ruta_idRuta = idRuta;");
			$clientes = mysqli_num_rows($queryClientes);

			$arrayClientes = array();
			while($row = mysqli_fetch_assoc($queryClientes)) {
				$arrayClientes[] = $row;
			}
			$_SESSION['arrayClientes'] = $arrayClientes;

    	}
    	else {
    		echo "No inició sesión.";
    	}
	}
	else {
		echo "Can't connect to database.";
	}
?>