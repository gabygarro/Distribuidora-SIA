<?php
   /* Requerimientos de Software - Prof. Alicia Salazar
	* Distribuidora SIA
	* Alexis Arguedas, Gabriela Garro, Yanil Gómez
	* -------------------------------------------------
	* login.php - Created: 10/06/16
	* Validación de inicio de sesión
	*/
	session_start(); //Start session
	$loginerror = ""; // Variable to store error message

	if (empty($_POST['usuario']) || empty($_POST['contrasena'])) {
		$loginerror = "Usuario o contraseña vacíos.";
		$_SESSION['loginerror'] = $loginerror;
		header("Location: index.php#invalidData");
	}
	else {
		$dbhost = "localhost";
		$dbuser = "regular";
		$dbpass = "reque123";
		$dbname = "general";
		$dberror = "No se pudo conectar a la base de datos.";

		$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname) or die($dberror);

		if ($conn == true) {
			echo "Connected!";

			$user = $_POST['usuario'];
			$pass = $_POST['contrasena'];

			$query = mysqli_query($conn, "SELECT * FROM usuario WHERE username='$user'");
			$numrows = mysqli_num_rows($query);

			if ($numrows!=0) {
				while ($row = mysqli_fetch_assoc($query)){
				    if ($row['username'] == $user && $row['password'] = $pass) {
				    	if ($row['tipo'] == 1) //si es admin
				    	header("Location: admin/index.php");
				    }
				}
			    die("Usuario o contraseña incorrectos.");
			}
			else
				echo "Usuario no existe";
			} 
			else die("No sé cuál es el problema");
		}

		//header("Location: index.php#somethingHappened");
?>