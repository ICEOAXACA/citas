<?php //este php evita que alguien ingrese sin login
session_start();
if(!isset($_SESSION['roles']) || $_SESSION ['roles'] != '2'){
    header("location:../php/log.php");
    session_destroy();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Usuario</title>
</head>
<body>
    <a href="../php/cerrar.php" class="btn btn-primary"> Cerrar Sesion </a>

    <h1 class ="text-center"> Bienvenido</h1>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Usuario</title>
</head>
<body>
    <h1 class ="text-center"> Contenido de supervisores</h1>
</body>
</html>
