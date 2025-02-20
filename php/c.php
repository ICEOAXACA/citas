<?php
header("Content-Type: text/html; charset=UTF-8");

$user = "postgres";
$password = " ";
$port = "5432";
$host = "localhost";
$bd = "iceocitas";

$conect = "host=$host port=$port dbname=$bd user=$user password=$password";
$conexion=pg_pconnect($conect) or die ("Error de conexion: ".pg_last_error());
    
?>