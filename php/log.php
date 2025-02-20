<?php
include('c.php');

/*se valida que ingresen valores reales para evitar un sql injection
$usuario =$conexion->real_escape_string(  $_POST['usuario']);
$pass =$conexion->real_escape_string(  $_POST['pass']);*/

$usuario = $_POST['usuario'];
$pass = $_POST['pass'];

//se ace la consulta de usuario y contraseña
$consulta = "SELECT * FROM  usuarios where usuario='$usuario' and contraseña='$pass'";

$fila = pg_query($conexion, $consulta);

echo $usuario;
//se valida que la variable tenga datos
if ($fila > 0) {
    echo '
   
    <script>
        alert("Intentalo Nuevamente");
        location.href = "../login.php";
    </script>

    ';

} else { //si no tiene datos de login redireccionamos a que ingresen datos

    session_start();

    if ($_SESSION['usuario'] = $usuario and $_SESSION['contraseña'] = $pass) {
        header("location:../usuario.php");
    } else {
        echo '
   
    <script>
        alert("Intentalo Nuevamente");
        location.href = "../login.php";
    </script>

    ';
    }



}
?>