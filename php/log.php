<?php
// Incluyo la conexión a la base de datos
include 'c.php';
// Inicio la sesión para manejar el login
session_start();

// Evito que el login se quede en caché para que siempre pida usuario y contraseña
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

// Si ya hay sesión activa, redirijo según el rol
if (isset($_SESSION['roles'])) {
    if ($_SESSION['roles'] == '1') {
        header('Location: ../admin/admin.php');
        exit();
    } elseif ($_SESSION['roles'] == '2') {
        header('Location: ../supervisor/supervisor.php');
        exit();
    } elseif ($_SESSION['roles'] == '3') {
        header('Location: ../revisor/revisores.php');
        exit();
    }
}

/*
// Validación para evitar SQL injection (comentado porque se usa pg_query, no mysqli)
$usuario =$conexion->real_escape_string(  $_POST['usuario']);
$pass =$conexion->real_escape_string(  $_POST['pass']);
*/

// Obtengo los datos del formulario
$usuario = $_POST['usuario'];
$pass = $_POST['pass'];

//se hace la consulta de usuario y contraseña
$consulta = "SELECT * FROM  usuarios where usuario='$usuario' and contraseña='$pass'";
$datos = pg_query($conexion, $consulta) or die("Error de Consulta");

if ((pg_num_rows($datos) > 0)) {
    // Si hay resultados, obtengo los datos del usuario
    $user = pg_fetch_result($datos, 0, "usuario");
    $contra = pg_fetch_result($datos, 0, "contraseña");
    $rol = pg_fetch_result($datos, 0, "rol_id");
    $id = pg_fetch_result($datos, 0, "id"); // id del usuario

    // Valido que los datos coincidan
    if ($usuario == $user and $contra == $pass) {
        $_SESSION['roles'] = $rol;
        $_SESSION['id'] = $id; // Guardo el id en la sesión
        $_SESSION['username'] = $user; // Guardo el username en la sesión
        if ($rol == "1") {
            header("Location: ../admin/admin.php");
        } elseif ($rol == "2") {
            header("Location: ../supervisor/supervisor.php");
        } elseif ($rol == "3") {
            header("Location: ../revisor/revisores.php");
        } else {
            // Si el rol no es válido, cierro sesión y muestro alerta
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
        // Si el usuario o contraseña no coinciden, cierro sesión y muestro alerta
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
} else {
    // Si no hay datos de login, cierro sesión y muestro alerta
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