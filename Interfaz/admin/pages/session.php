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
			$queryClientes = mysqli_query($conn, "SELECT idCliente, nombreLocal, nombreEncargado, correo, Cliente.direccion as direccion, Cliente.latitud as latitud, Cliente.longitud as longitud, horaApertura, minutoApertura, horaCierre, minutoCierre, Bodega_idBodega as bodega, Bodega.nombre as bodegaNombre, idRuta FROM cliente, clienteXRuta, Ruta, Bodega WHERE idCliente = Cliente_idCliente AND Ruta_idRuta = idRuta AND idBodega = Bodega_idBodega;");
			$clientes = mysqli_num_rows($queryClientes);
			$arrayClientes = array();
			$arrayClientes2 = array();
			while($row = mysqli_fetch_assoc($queryClientes)) {
				$arrayClientes[] = $row;
				$arrayClientes2[] = [$row['idCliente'], $row['nombreLocal'], $row['nombreEncargado'], $row['correo'], $row['direccion'], "<a href=\"http:\/\/maps.google.com\/maps?q=" . $row['latitud'] . "," . $row['longitud'] . "\" target=\"_blank\"><i class=\"fa fa-location-arrow\"></i> Ver</a>", $row['horaApertura'] . ":" . $row['minutoApertura'], $row['horaCierre'] . ":" . $row['minutoCierre'], $row['bodegaNombre'], $row['idRuta']];
			}
			$_SESSION['arrayClientes'] = $arrayClientes;
			$_SESSION['arrayClientes2'] = $arrayClientes2;

			// Obtener el catálogo de productos
			$queryProductos = mysqli_query($conn, "SELECT idProducto, Producto.nombre as ProductoNombre, descripcion, precioCompra, precioVenta, impuesto, pesoPorCaja, Categoria.nombre as CategoriaNombre, Marca.nombre as MarcaNombre, Proveedor.nombre as proveedor FROM Producto, Categoria, Marca, Proveedor WHERE Categoria_idCategoria=idCategoria AND Marca_idMarca = idMarca AND Proveedor_idProveedor = idProveedor;");
			$productos = mysqli_num_rows($queryProductos);
			$arrayProductos = array();
			$arrayProductos2 = array();
			while ($row = mysqli_fetch_assoc($queryProductos)) {
				$arrayProductos[$row['idProducto']] = $row;
				$arrayProductos2[] = [$row['idProducto'], $row['ProductoNombre'], $row['descripcion'], "₡" . $row['precioCompra'], "₡" . $row['precioVenta'], $row['impuesto'] . "%", $row['pesoPorCaja'] . "kg", $row['CategoriaNombre'], $row['MarcaNombre'], $row['proveedor']];
			}
			$_SESSION['arrayProductos'] = $arrayProductos;
			$_SESSION['arrayProductos2'] = $arrayProductos2;

			// Obtener el inventario de cada bodega
			$queryInventario = mysqli_query($conn, "SELECT * from Inventario;");
			$arrayInventario = array();
			while ($row = mysqli_fetch_assoc($queryInventario)) {
				if (0 < $row['cantidad']) {
					$arrayInventario[$row['Bodega_idBodega']][$row['Producto_idProducto']] = $row;
				}
			}
			$_SESSION['arrayInventario'] = $arrayInventario;

			//Obtener todos los pedidos
			$queryPedidos = mysqli_query($conn, "SELECT idPedido, Cliente_idCliente, nombreLocal, fecha FROM Pedido, Cliente WHERE Cliente_idCliente=idCliente AND fecha IS NOT NULL AND entregado=0 ORDER BY fecha;");
			$arrayPedidos = array();
			while ($row = mysqli_fetch_assoc($queryPedidos)) {
				$arrayPedidos[] = [$row['idPedido'], $row['Cliente_idCliente'], $row['nombreLocal'], $row['fecha']];
			}
			$_SESSION['arrayPedidos'] = $arrayPedidos;

			//Obtener las rutas
			$queryRutas = mysqli_query($conn, "SELECT idRuta, Canton.nombre AS canton, Provincia.nombre AS provincia, Bodega.nombre AS bodega, Camion_idCamion AS camion, CONCAT (Empleado.nombre, ' ', Empleado.apellidos) AS empleado, lunes, martes, miercoles, jueves, viernes, sabado FROM Ruta, Canton, Provincia, Bodega, Empleado WHERE Canton_idCanton1 = idCanton AND Provincia_idProvincia = idProvincia AND Bodega_idBodega = idBodega AND Empleado_Usuario_idUsuario = Usuario_idUsuario;");
			$arrayRutas = array();
			while ($row = mysqli_fetch_assoc($queryRutas)) {
				// Cambiar el valor de cada día de la semana
				$dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
				for ($i = 0; $i < 6; $i++) {
					if ($row[$dias[$i]] == 0) {
						$row[$dias[$i]] = "No";
					}
					else {
						$row[$dias[$i]] = "Sí";
					}
				}			
				$arrayRutas[] = [$row['idRuta'], $row['canton'], $row['provincia'], $row['bodega'], $row['camion'], $row['empleado'], $row['lunes'], $row['martes'], $row['miercoles'], $row['jueves'], $row['viernes'], $row['sabado'], "<a href=\"#editarRutaModal\" data-toggle=\"modal\"><i class=\"fa fa-pencil-square-o\"></i> Editar</a>"];
			}
			$_SESSION['arrayRutas'] = $arrayRutas;

			//Obtener los camiones
			$queryCamiones = mysqli_query($conn, "SELECT idCamion, anho, RTV, marca, modelo, combustible, latitud, longitud, pesoMaximo FROM CAMION WHERE fueraDeServicio = 0;");
			$arrayCamiones = array();
			while ($row = mysqli_fetch_assoc($queryCamiones)) {
				if ($row['RTV'] == 0) {
					$row['RTV'] = "Sí";
				}
				else {
					$row['RTV'] = "Sí";
				}
				$arrayCamiones[] = [$row['idCamion'], $row['anho'], $row['RTV'], $row['marca'], $row['modelo'], $row['combustible'], $row['pesoMaximo'] . "kg", "<a href=\"http:\/\/maps.google.com\/maps?q=" . $row['latitud'] . "," . $row['longitud'] . "\" target=\"_blank\"><i class=\"fa fa-location-arrow\"></i> Ver</a>"];
				//
			}
			$_SESSION['arrayCamiones'] = $arrayCamiones;

			// Obtener bodegas
			$queryBodegas = mysqli_query($conn, "SELECT * FROM Bodega;");
			$arrayBodegas = array();
			while ($row = mysqli_fetch_assoc($queryBodegas)) {
				$arrayBodegas[] = [$row['idBodega'], $row['nombre'], $row['direccion'], "<a href=\"http:\/\/maps.google.com\/maps?q=" . $row['latitud'] . "," . $row['longitud'] . "\" target=\"_blank\"><i class=\"fa fa-location-arrow\"></i> Ver</a>"];
			}
			$_SESSION['arrayBodegas'] = $arrayBodegas;

			//Obtener empleados
			$queryEmpleados = mysqli_query($conn, "SELECT * FROM Empleado;");
			$arrayEmpleados = array();
			while ($row = mysqli_fetch_assoc($queryEmpleados)) {
				if ($row['idTipoEmpleado'] == 2) {
					$row['idTipoEmpleado'] = 'TR';
				}
				$arrayEmpleados[] = [$row['idTipoEmpleado'] . "-" . $row['Usuario_idUsuario'], $row['cedula'], $row['nombre'], $row['apellidos']];
			}
			$_SESSION['arrayEmpleados'] = $arrayEmpleados;

			//Obtener marcas
			$queryMarcas = mysqli_query($conn, "SELECT * FROM Marca;");
			$arrayMarcas = array();
			while ($row = mysqli_fetch_assoc($queryMarcas)) {
				$arrayMarcas[] = [$row['idMarca'], $row['nombre']];
			}
			$_SESSION['arrayMarcas'] = $arrayMarcas;

			//Obtener categorias
			$queryCategorias = mysqli_query($conn, "SELECT * FROM Categoria;");
			$arrayCategorias = array();
			while ($row = mysqli_fetch_assoc($queryCategorias)) {
				$arrayCategorias[] = [$row['idCategoria'], $row['nombre']];
			}
			$_SESSION['arrayCategorias'] = $arrayCategorias;

			//Obtener proveedores
			$queryProveedores = mysqli_query($conn, "SELECT * FROM Proveedor");
			$arrayProveedores = array();
			while($row = mysqli_fetch_assoc($queryProveedores)) {
				$arrayProveedores[] = [$row['idProveedor'], $row['nombre'], $row['direccion'], $row['telefono'], $row['tiempoDesalmacenaje'] . "min", "<i class=\"fa fa-share fa-fw\"></i> Ver productos", "<i class=\"fa fa-pencil-square-o\"></i> Editar</a>"];
			}
			$_SESSION['arrayProveedores'] = $arrayProveedores;

			//Obtener bitácora
			$queryBitacora = mysqli_query($conn, "SELECT * FROM Bitacora");
			$arrayBitacora = array();
			while($row = mysqli_fetch_assoc($queryBitacora)) {
				$arrayBitacora[] = [$row['Usuario_idUsuario'], $row['evento'], $row['fecha']];
			}
			$_SESSION['arrayBitacora'] = $arrayBitacora;
    	}
    	else {
    		echo "No inició sesión.";
    	}
	}
	else {
		echo "Can't connect to database.";
	}
?>