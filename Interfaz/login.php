<?php
   /* Requerimientos de Software - Prof. Alicia Salazar
	* Distribuidora SIA
	* Alexis Arguedas, Gabriela Garro, Yanil Gómez
	* -------------------------------------------------
	* login.php - Creado: 10/06/16
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
			echo "Connected!\n";

			$user = $_POST['usuario'];
			$pass = $_POST['contrasena'];

			$query = mysqli_query($conn, "SELECT * FROM usuario WHERE username='$user'");
			$numrows = mysqli_num_rows($query);

			if ($numrows!=0) {
				while ($row = mysqli_fetch_assoc($query)){
				    if ($row['username'] == $user && $row['password'] = $pass) {

				    	//Store the user type
				    	$_SESSION['userType'] = $row['tipo'];

				    	//Store the userID
				    	$_SESSION['usernameID'] = $row['idUsuario'];

				    	//Store the user's name
				    	$_SESSION['username'] = $row['username'];

				    	//User type check
				    	if ($row['tipo'] == 1) //si es admin
				    		header("Location: admin/index.php");
				    	elseif ($row['tipo'] == 2) {
				    		header("Location: transport/index.php");
				    	}

				    	/*poner más ifs para cada tipo de usuario*/

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