<?php
   /* Requerimientos de Software - Prof. Alicia Salazar
    * Distribuidora SIA
    * Alexis Arguedas, Gabriela Garro, Yanil Gómez
    * -------------------------------------------------
    * session.php - Creado: 13/06/16
    * Control de la sesión actual (admin)
    */

   header('Content-type: text/html; charset=utf-8');
	
	// Connect to the db
	$dbhost = "localhost";
	$dbuser = "regular";
	$dbpass = "reque123";
	$dbname = "general";
	$dberror = "No se pudo conectar a la base de datos.";

	$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname) or die($dberror);

	mysqli_set_charset($conn, "UTF8");

	if ($conn == true) {
		session_start();
		if (isset($_SESSION['usernameID'])) { // El usuario sí inició sesión
			$usernameID = $_SESSION['usernameID'];
			$username = $_SESSION['username'];
			$userType = $_SESSION['userType'];

			// Obtener el id de Cliente, su nombre y la bodega a la que está asociado
			$queryClientes = mysqli_query($conn, "SELECT idCliente, nombreLocal, Bodega_idBodega as bodega FROM cliente, clienteXRuta, Ruta WHERE idCliente = Cliente_idCliente AND Ruta_idRuta = idRuta;");
			$clientes = mysqli_num_rows($queryClientes);
			$arrayClientes = array();
			while($row = mysqli_fetch_assoc($queryClientes)) {
				$arrayClientes[] = $row;
			}
			$_SESSION['arrayClientes'] = $arrayClientes;

			// Obtener el catálogo de productos
			$queryProductos = mysqli_query($conn, "SELECT idProducto, Producto.nombre as ProductoNombre, descripcion, precioCompra, precioVenta, impuesto, pesoPorCaja, Categoria.nombre as CategoriaNombre, Marca.nombre as MarcaNombre FROM Producto, Categoria, Marca WHERE Categoria_idCategoria=idCategoria AND Marca_idMarca = idMarca;");
			$productos = mysqli_num_rows($queryProductos);
			$arrayProductos = array();
			while ($row = mysqli_fetch_assoc($queryProductos)) {
				$arrayProductos[$row['idProducto']] = $row;
			}
			$_SESSION['arrayProductos'] = $arrayProductos;

			// Obtener el inventario de cada bodega
			$queryInventario = mysqli_query($conn, "SELECT * from Inventario;");
			$arrayInventario = array();
			while ($row = mysqli_fetch_assoc($queryInventario)) {
				if (0 < $row['cantidad']) {
					$arrayInventario[$row['Bodega_idBodega']][$row['Producto_idProducto']] = $row;
				}
			}
			$_SESSION['arrayInventario'] = $arrayInventario;
    	}
    	else {
    		echo "No inició sesión.";
    	}
	}
	else {
		echo "Can't connect to database.";
	}
?>