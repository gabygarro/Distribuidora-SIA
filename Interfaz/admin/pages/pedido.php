<?php
   /* Requerimientos de Software - Prof. Alicia Salazar
    * Distribuidora SIA
    * Alexis Arguedas, Gabriela Garro, Yanil Gómez
    * -------------------------------------------------
    * tablas.php - Creado: 14/06/16
    * Tablas catálogo
    */

   include('session.php');
    if(!isset($_SESSION['usernameID'])) {
        header("Location: ../../index.php#notloggedin");
    }
    elseif ($_SESSION['userType'] != 1) { //if it's not admin
        header("Location: ../../index.php#notadmin");
    }

    //if the form was sent
    if(isset($_POST["pedido"])) {
        $cliente = $_POST["cliente"];
        $bodega = $_POST["bodega"];
        $idPedido = 0;

        $queryPedido = mysqli_query($conn, "INSERT INTO Pedido (Cliente_IdCliente, entregado, fecha) VALUES ('$cliente', 0, now());");

        $queryLastInsert = mysqli_query($conn, "SELECT LAST_INSERT_ID();");
        $numrows = mysqli_num_rows($queryLastInsert);
        if ($numrows != 0) {
            while ($row = mysqli_fetch_assoc($queryLastInsert)) {
                $idPedido = $row['LAST_INSERT_ID()'];
            }
            for ($i = 0; isset($_POST['producto' . $i]); $i++) {
                $currentProducto = $_POST['producto' . $i];
                $currentCantidad = $_POST['cantidad' . $i];
                $queryInventario = mysqli_query($conn, "UPDATE Inventario SET cantidad = cantidad - '$currentCantidad' WHERE Bodega_idBodega = '$bodega' AND Producto_idProducto = '$currentProducto' AND cantidad > 0;");

                $queryInsertProducto = mysqli_query($conn, "INSERT INTO ArticulosXPedido (Inventario_Bodega_idBodega, Inventario_Producto_idProducto, Pedido_idPedido, cantidad) VALUES ('$bodega', '$currentProducto', '$idPedido', '$currentCantidad');");
            }

        }
        else {
            echo "Last Insert devolvió 0 filas.";
        }

    }


?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SIA - Dashboard de Administrador</title>
    <link rel="icon" href="../../favicon.png" type="image/png">

    <!-- Bootstrap Core CSS -->
    <link href="../bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="../bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">

    <!-- Timeline CSS -->
    <link href="../dist/css/timeline.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- Morris Charts CSS -->
    <link href="../bower_components/morrisjs/morris.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="../bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>

    <script>
        // Clientes
        var arrayClientes = <?php echo json_encode($_SESSION['arrayClientes'], JSON_PRETTY_PRINT) ?>;
        //Productos e Inventario
        var arrayProductos = <?php echo json_encode($_SESSION['arrayProductos'], JSON_PRETTY_PRINT) ?>;
        var arrayInventario = <?php echo json_encode($_SESSION['arrayInventario'], JSON_PRETTY_PRINT) ?>; 
        var numPedidos = 0;

        function populateProductos(productoID, cantidadID, bodegaID) {
            var elementProducto = document.getElementById(productoID);
            var elementBodega = document.getElementById(bodegaID);
            elementProducto.length = 0;
            elementProducto.options[0] = new Option("Seleccione producto", "-1");
            elementProducto.selectedIndex = 0;
            for (var i = 0; i <= Object.keys(arrayInventario[elementBodega.value]).length; i++) {
                if (typeof arrayInventario[elementBodega.value][i.toString()] != 'undefined') {
                    var idProductoXBodega = arrayInventario[elementBodega.value][i.toString()]["Producto_idProducto"];
                    //console.log("idProductoXBodega = " + idProductoXBodega);
                    var optionNombre = arrayProductos[idProductoXBodega]["ProductoNombre"];
                    optionNombre += " (";
                    optionNombre += arrayProductos[idProductoXBodega]["descripcion"];
                    optionNombre += ")";
                    var optionID = arrayProductos[idProductoXBodega]["idProducto"];
                    elementProducto.options[elementProducto.length] = new Option(optionNombre, optionID);
                }
            }

            elementProducto.onchange = function(){
                var producto = elementProducto.value;
                var elementCantidad = document.getElementById(cantidadID);
                var cantidad = arrayInventario[elementBodega.value][producto]['cantidad'];
                //console.log(cantidad);
                elementCantidad.max = cantidad;
            };
        } 

        function anadirProducto(divID) {
            numPedidos++;
            var code = "<div class=\"row\">\n    <div class=\"col-md-8\">\n";
            code += "        <br><select name=\"producto";
            code += numPedidos + "\" id=\"producto";
            code += numPedidos + "\" class=\"form-control\"></select>\n";
            code += "    </div>\n    <div class=\"col-md-2\">";
            code += "        <br><input type=\"number\" min=\"0\" step=\"1\" name=\"cantidad";
            code += numPedidos + "\" id=\"cantidad";
            code += numPedidos + "\" class=\"form-control\">    </div>\n";
            code += "    <div class=\"col-md-2\">";
            code += "        <br><button class=\"btn btn-default\" onclick=\"return false;\"><i class=\"fa fa-times fa-fw\"></i></button>";
            code += "    </div>\n</div>";
            var div = document.getElementById(divID);
            div.insertAdjacentHTML('beforeend', code);
            populateProductos('producto' + numPedidos, 'cantidad' + numPedidos, 'bodega');
        }

        window.onload = function() {
            var elementClientes = document.getElementById('cliente');
            elementClientes.length = 0;
            elementClientes.options[0] = new Option('Seleccione el cliente', '-1');
            elementClientes.selectedIndex = 0;
            for (var i = 0; i < arrayClientes.length; i++) {
                elementClientes.options[elementClientes.length] = new Option(arrayClientes[i]["nombreLocal"], arrayClientes[i]["idCliente"]);
            }
            elementClientes.onchange = function() {
                var elementBodega = document.getElementById('bodega');
                elementBodega.value = "-1";
                for (var i = 0; i < arrayClientes.length; i++) {
                    if (elementClientes.options[elementClientes.selectedIndex].value == arrayClientes[i]["idCliente"]) {
                        elementBodega.value = arrayClientes[i]["bodega"];
                        //break;
                    }
                }
                //populateProductos
                populateProductos('producto0', 'cantidad0', 'bodega');
            }
        };
    </script>

</head>

<body>

    <div id="wrapper">

        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php">Distribuidora SIA</a>
            </div>
            <!-- /.navbar-header -->

            <ul class="nav navbar-top-links navbar-right">
                </li>
                <!-- /.dropdown -->
                <!-- Dropdown de entregas que están sucediendo en este momento -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-tasks fa-fw"></i>  <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-tasks">
                        <li>
                            <a href="#">
                                <div>
                                    <p>
                                        <strong>Task 1</strong>
                                        <span class="pull-right text-muted">40% Complete</span>
                                    </p>
                                    <div class="progress progress-striped active">
                                        <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%">
                                            <span class="sr-only">40% Complete (success)</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <p>
                                        <strong>Task 2</strong>
                                        <span class="pull-right text-muted">20% Complete</span>
                                    </p>
                                    <div class="progress progress-striped active">
                                        <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: 20%">
                                            <span class="sr-only">20% Complete</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <p>
                                        <strong>Task 3</strong>
                                        <span class="pull-right text-muted">60% Complete</span>
                                    </p>
                                    <div class="progress progress-striped active">
                                        <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%">
                                            <span class="sr-only">60% Complete (warning)</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <p>
                                        <strong>Task 4</strong>
                                        <span class="pull-right text-muted">80% Complete</span>
                                    </p>
                                    <div class="progress progress-striped active">
                                        <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100" style="width: 80%">
                                            <span class="sr-only">80% Complete (danger)</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a class="text-center" href="#">
                                <strong>Ver todas las entregas</strong>
                                <i class="fa fa-angle-right"></i>
                            </a>
                        </li>
                    </ul>
                    <!-- /.dropdown-tasks -->
                </li>
                <!-- /.dropdown -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-bell fa-fw"></i>  <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-alerts">
                        <li>
                            <a href="#">
                                <div>
                                    <i class="fa fa-comment fa-fw"></i> New Comment
                                    <span class="pull-right text-muted small">4 minutes ago</span>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <i class="fa fa-twitter fa-fw"></i> 3 New Followers
                                    <span class="pull-right text-muted small">12 minutes ago</span>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <i class="fa fa-envelope fa-fw"></i> Message Sent
                                    <span class="pull-right text-muted small">4 minutes ago</span>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <i class="fa fa-tasks fa-fw"></i> New Task
                                    <span class="pull-right text-muted small">4 minutes ago</span>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#">
                                <div>
                                    <i class="fa fa-upload fa-fw"></i> Server Rebooted
                                    <span class="pull-right text-muted small">4 minutes ago</span>
                                </div>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a class="text-center" href="#">
                                <strong>See All Alerts</strong>
                                <i class="fa fa-angle-right"></i>
                            </a>
                        </li>
                    </ul>
                    <!-- /.dropdown-alerts -->
                </li>
                <!-- /.dropdown -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="#"><i class="fa fa-user fa-fw"></i> Perfil</a>
                        </li>
                        <li><a href="#"><i class="fa fa-gear fa-fw"></i> Configuración</a>
                        </li>
                        <li class="divider"></li>
                        <li><a href="../../index.php"><i class="fa fa-sign-out fa-fw"></i> Cerrar sesión</a>
                        </li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
            </ul>
            <!-- /.navbar-top-links -->

            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li class="sidebar-search">
                            <div class="input-group custom-search-form">
                                <input type="text" class="form-control" placeholder="Search...">
                                <span class="input-group-btn">
                                <button class="btn btn-default" type="button">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                            </div>
                            <!-- /input-group -->
                        </li>
                        <li>
                            <a href="index.php"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-shopping-cart fa-fw"></i> Pedidos<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="pedido.php">Nuevo pedido</a>
                                </li>
                                <li>
                                    <a href="pedidos.php">Ver pedidos</a>
                                </li>
                            </ul>
                             <!--/.nav-second-level -->
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-share-alt fa-fw"></i> Rutas<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="#">Nueva ruta</a>
                                </li>
                                <li>
                                    <a href="rutas.php">Ver rutas</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-building fa-fw"></i> Bodegas<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="#">Nueva bodega</a>
                                </li>
                                <li>
                                    <a href="bodegas.php">Ver bodegas</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-users fa-fw"></i> Clientes<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="#">Nuevo cliente</a>
                                </li>
                                <li>
                                    <a href="clientes.php">Ver clientes</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-user fa-fw"></i> Empleados<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="#">Nuevo empleado</a>
                                </li>
                                <li>
                                    <a href="empleados.php">Ver empleados</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-truck fa-fw"></i> Camiones<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="#">Nuevo camión</a>
                                </li>
                                <li>
                                    <a href="camiones.php">Ver camiones</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-suitcase fa-fw"></i> Productos<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="#">Nuevo producto</a>
                                </li>
                                <li>
                                    <a href="productos.php">Catálogo de productos</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-table fa-fw"></i> Tablas catálogo<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="#">Nueva marca</a>
                                </li>
                                <li>
                                    <a href="#">Nueva categoría</a>
                                </li>
                                <li>
                                    <a href="tablas.php">Ver tablas catálogo</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>

        <!-- ______________________________________ page wrapper ______________________________________________ -->
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Nuevo pedido</h1>
                </div>
                <!-- /.col-lg-12 -->
                <div class="panel panel-default">
                    <div class="panel-body">
                        <form role="form" name="pedido" action="pedido.php" method="POST">
                            <label>Cliente</label>
                            <select id="cliente" name ="cliente" class="form-control"></select>
                            <input id="bodega" name="bodega" type="hidden" value="-1"></input>
                            <br>
                            <div class="row">
                                <div class="col-md-8">
                                    <label>Productos</label>
                                    <select name="producto0" id="producto0" class="form-control"></select>
                                </div>
                                <div class="col-md-2">
                                    <label>Cantidad</label>
                                    <input type="number" min="1" step="1" name="cantidad0" id="cantidad0" class="form-control">
                                </div>
                            </div>
                            <div id="masproductos"></div>
                            <br>
                            <button class="btn btn-default" id="otroProducto" onclick="anadirProducto('masproductos');return false;">Añadir producto</button>
                            <button name="pedido" class="btn btn-primary" type="submit">Hacer pedido</button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- /.row -->

        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- jQuery -->
    <script src="../bower_components/jquery/dist/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="../bower_components/metisMenu/dist/metisMenu.min.js"></script>

    <!-- Morris Charts JavaScript -->
    <script src="../bower_components/raphael/raphael-min.js"></script>
    <script src="../bower_components/morrisjs/morris.min.js"></script>
    <script src="../js/morris-data.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="../dist/js/sb-admin-2.js"></script>

</body>

</html>
