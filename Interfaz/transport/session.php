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
	}
	else {
		echo "Can't connect to database.";
	}
?>