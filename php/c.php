<?php
// Este archivo se encarga de la conexión a la base de datos PostgreSQL
header("Content-Type: text/html; charset=UTF-8");

// Aquí defino los datos de acceso a la base de datos
$user = "postgres";
$password = "siteoss";
$port = "5432";
$host = "10.173.155.46";
$bd = "citasnuevo";

// Armo la cadena de conexión
$conect = "host=$host port=$port dbname=$bd user=$user password=$password";
// Hago la conexión y la guardo en $conexion para usarla en todo el sistema
$conexion = pg_connect($conect);
if (!$conexion) {
    // Si falla la conexión, muestro el error y detengo todo
    die("Error de conexion: ".pg_last_error());
}
?>