<?php
include 'c.php';

/*se valida que ingresen valores reales para evitar un sql injection
$usuario =$conexion->real_escape_string(  $_POST['usuario']);
$pass =$conexion->real_escape_string(  $_POST['pass']);*/

$usuario = $_POST['usuario'];
$pass = $_POST['pass'];


//se hace la consulta de usuario y contraseña
$consulta = "SELECT * FROM  usuarios where usuario='$usuario' and contraseña='$pass'";
$datos = pg_query($conexion, $consulta) or die("Error de Consulta");

if((pg_num_rows($datos)> 0)){
    

$user = pg_fetch_result($datos,0,"usuario");
$contra = pg_fetch_result($datos,0,"contraseña");


//se valida que la variable tenga datos
if ($usuario == $user and $contra == $pass) {

    session_start();

        echo $user;
        echo $contra;

}else{
    echo '
   
    <script>
        alert("Intentalo Nuevamente");
        location.href = "../login.php";
    </script>

    ';
}


        

} else { //si no tiene datos de login redireccionamos a que ingresen datos




    echo '
   
    <script>
        alert("Intentalo Nuevamente");
        location.href = "../login.php";
    </script>

    ';


}
?>