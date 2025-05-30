<?php
// Este archivo destruye la sesión del usuario y lo regresa al login
session_start(); // Inicio la sesión para poder destruirla
session_unset(); // Limpio todas las variables de sesión
session_destroy(); // Destruyo la sesión completamente
header("location:../login.php"); // Redirijo al login después de cerrar sesión
?>