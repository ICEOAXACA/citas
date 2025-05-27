<?php
include 'c.php';
session_start();


/*se valida que ingresen valores reales para evitar un sql injection
$usuario =$conexion->real_escape_string(  $_POST['usuario']);
$pass =$conexion->real_escape_string(  $_POST['pass']);*/

$usuario = $_POST['usuario'];
$pass = $_POST['pass'];


//se hace la consulta de usuario y contraseña
$consulta = "SELECT * FROM  usuarios where usuario='$usuario' and contraseña='$pass'";
$datos = pg_query($conexion, $consulta) or die("Error de Consulta");

if ((pg_num_rows($datos) > 0)) {


    $user = pg_fetch_result($datos, 0, "usuario");
    $contra = pg_fetch_result($datos, 0, "contraseña");
    $rol = pg_fetch_result($datos, 0, "rol_id");
    $id = pg_fetch_result($datos, 0, "id"); // Obtener el id del usuario

    //se valida que la variable tenga datos
    if ($usuario == $user and $contra == $pass) {
        $_SESSION['roles'] = $rol;
        $_SESSION['id'] = $id; // Guardar el id en la sesión
        $_SESSION['username'] = $user; // Guardar el username en la sesión
        if ($rol == "1") {
            header("Location: ../admin/admin.php");
        } elseif ($rol == "2") {
            header("Location: ../supervisor/supervisor.php");
        } elseif ($rol == "3") {
            header("Location: ../revisor/revisores.php");
        } else {

            session_start();
            session_unset();
            session_destroy();

            echo '
   
            <script>
                alert("Intentalo Nuevamente 1");
                location.href = "login.php";
            </script>
        
            ';
        }

    } else {

        session_start();
        session_unset();
        session_destroy();
        echo '
   
    <script>
        alert("Intentalo Nuevamente 2");
        location.href = "../login.php";
    </script>

    ';
    }




} else { //si no tiene datos de login redireccionamos a que ingresen datos

    session_start();
    session_unset();
    session_destroy();


    echo '
   
    <script>
        alert("Intentalo Nuevamente 3");
        location.href = "../login.php";
    </script>
    
    ';


}
?>