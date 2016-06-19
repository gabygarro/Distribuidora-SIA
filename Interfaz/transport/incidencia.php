<?php
   /* Requerimientos de Software - Prof. Alicia Salazar
    * Distribuidora SIA
    * Alexis Arguedas, Gabriela Garro, Yanil Gómez
    * -------------------------------------------------
    * index.php - Creado: 14/06/16
    * Interfaz principal de transporte.
    */

    include('session.php');
    if(!isset($_SESSION['usernameID'])) {
        header("Location: ../index.php#notloggedin");
    }
    if ($_SESSION['userType'] != 2) { //if it's not transport
        header("Location: ../index.php#nottransport");
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

    <title>Distribuidora SIA - Transporte</title>
    <link rel="icon" href="../favicon.png" type="image/png">

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="css/simple-sidebar.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="../admin/bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.css" rel="stylesheet">

    <!-- DataTables Responsive CSS -->
    <link href="../admin/bower_components/datatables-responsive/css/responsive.dataTables.scss" rel="stylesheet">
    
    <!-- Datatables JS -->
    <script src="https://code.jquery.com/jquery-1.12.3.js"></script>
    <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <script>
        var arrayPedidos = <?php echo json_encode($_SESSION['arrayPedidos'], JSON_PRETTY_PRINT) ?>; 

        $(document).ready(function() {
            $('#pedidos').DataTable( {
                data: arrayPedidos,
                columns: [
                    { title: "ID. Pedido" },
                    { title: "ID. Cliente" },
                    { title: "Cliente" },
                    { title: "Fecha de Pedido" },
                    { title: "Ubicación" },
                    { title: "Artículos" },
                    { title: "Marcar como entregado" }
                ]
            } );
        } );
    </script>

</head>

<body>

    <div id="wrapper">

        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav">
                <li class="sidebar-brand">
                    <a href="#">
                        <img class="img-responsive" src="../img/profile.png" alt="">
                        Transporte
                    </a>
                </li>
                <li>
                    <a href="index.php">Ruta</a>
                </li>
                <li>
                    <a href="#"><b>Reporte de incidencia</b></a>
                </li>
                <li>
                    <a href="../index.php">Cerrar sesión</a>
                </li>
            </ul>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <a href="#menu-toggle" class="btn btn-default" id="menu-toggle"><i class="fa fa-bars"></i></a>
                        <h1>Reporte de incidencia</h1>
                        <p>Arrastre el marcador rojo para indicar el lugar de la incidencia: </p>
                    </div>
                </div>
                <section id="contact" class="map">
                    <!--
                    <iframe width="100%" height="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q=Twitter,+Inc.,+Market+Street,+San+Francisco,+CA&amp;aq=0&amp;oq=twitter&amp;sll=28.659344,-81.187888&amp;sspn=0.128789,0.264187&amp;ie=UTF8&amp;hq=Twitter,+Inc.,+Market+Street,+San+Francisco,+CA&amp;t=m&amp;z=15&amp;iwloc=A&amp;output=embed"></iframe>
                    -->
                    <iframe width="100%" height="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?q=<?php echo $_SESSION['latitud']?>,<?php echo $_SESSION['longitud']?>&amp;output=embed"></iframe>

                    <br />
                    <small>
                        <!--
                        <a href="https://maps.google.com/maps?f=q&amp;source=embed&amp;hl=en&amp;geocode=&amp;q=Twitter,+Inc.,+Market+Street,+San+Francisco,+CA&amp;aq=0&amp;oq=twitter&amp;sll=28.659344,-81.187888&amp;sspn=0.128789,0.264187&amp;ie=UTF8&amp;hq=Twitter,+Inc.,+Market+Street,+San+Francisco,+CA&amp;t=m&amp;z=15&amp;iwloc=A"></a>
                        -->
                        <a href="https://maps.google.com/maps?q=<?php echo $_SESSION['latitud']?>,<?php echo $_SESSION['longitud']?>&amp;output=embed"></a>
                    </small>
                    </iframe>
                </section>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <br>
                        <form role="form" name="incidencia" action="incidencia.php" method="POST">
                            <label>Tipo</label>
                            <select id="tipo" name ="tipo" class="form-control"></select>
                            <br>
                            <label>Descripción</label> 
                            <textarea maxlength="300" class="form-control"></textarea>
                            <br>
                            <button name="incidencia" class="btn btn-primary" type="submit">Enviar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- /#page-content-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="../admin/bower_components/metisMenu/dist/metisMenu.min.js"></script>

    <!-- DataTables JavaScript -->
    <script src="../admin/bower_components/datatables/media/js/jquery.dataTables.min.js"></script>
    <script src="../admin/bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.min.js"></script>

    <!-- Menu Toggle Script -->
    <script>
    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });
    </script>

</body>

</html>
