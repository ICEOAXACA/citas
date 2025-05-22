<?php
header("Content-Type: text/html; charset=UTF-8");

$user = "postgres";
$password = "siteoss";
$port = "5432";
$host = "10.173.155.46";
$bd = "citasnuevo";

$conect = "host=$host port=$port dbname=$bd user=$user password=$password";
$conexion = pg_connect($conect);
if (!$conexion) {
    die("Error de conexion: ".pg_last_error());
}
?>