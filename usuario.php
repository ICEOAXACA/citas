<?php
include('php/c.php');

session_start();


//Este codigo impide que alguien acceda sin logearse y lo redirecciona al inicio
if ((!isset($_SESSION['usuario']) || $_SESSION['usuario'] == "")) {
    session_destroy();
    echo '
   
            <script>
                alert("Intentalo Nuevamente");
                location.href = "login.php";
            </script>
        
            ';

    //Si el usuario se logeo entonces continua el codigo de usuario 
} else {

    $session = $_SESSION['usuario'];

    //se extrae el tipo de rol mediante una consulta guardada en la variable rol
    $datos = pg_query($conexion, "SELECT * FROM usuarios WHERE usuario='$session'");

    ($consulta = pg_fetch_array($datos));
    $rol = $consulta['id_rol'];


    if ($rol == "1") {
        include "admin/admin.php";
    } elseif ($rol == "2") {
        include "supervisor/supervisor.php";
    } elseif ($rol == "3") {
        include "revisor/revisores.php";
    } else {

        echo '
   
            <script>
                alert("Intentalo Nuevamente");
                location.href = "login.php";
            </script>
        
            ';
    }

}


?>

</body>

</html>