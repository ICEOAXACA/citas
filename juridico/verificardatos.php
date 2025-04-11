<?php
session_start();

// Recuperar los datos del formulario
$nombre = $_POST['nombre'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$correo = $_POST['correo'] ?? '';
$folio = $_POST['folio'] ?? '';
$fecha_cita = $_POST['fecha_cita'] ?? '';
$hora_cita = $_POST['hora_cita'] ?? '';

// Guardar los datos en sesión por si se necesitan después
$_SESSION['datos_cita'] = compact('nombre', 'telefono', 'correo', 'folio', 'fecha_cita', 'hora_cita');

// Recuperar sesiones
$principal = $_SESSION['principal'] ?? null;
$secundario = $_SESSION['secundario'] ?? null;
$servicio = $_SESSION['servicio'] ?? null;

// Conexión a PostgreSQL
require_once '../php/c.php'; // tu archivo de conexión con $conexion

// 1. Obtener nombre del departamento principal
$nombre_departamento = 'No encontrado';
if ($principal) {
    $query = "SELECT nombre FROM departamentos WHERE id = $1";
    $resultado = pg_query_params($conexion, $query, [$principal]);
    if ($resultado && pg_num_rows($resultado) > 0) {
        $fila = pg_fetch_assoc($resultado);
        $nombre_departamento = $fila['nombre'];
    }
}

// 2. Obtener nombre del servicio principal
$nombre_servicio_principal = 'No encontrado';
if ($secundario) {
    $query2 = "SELECT nombre FROM servicios_principales WHERE id = $1";
    $resultado2 = pg_query_params($conexion, $query2, [$secundario]);
    if ($resultado2 && pg_num_rows($resultado2) > 0) {
        $fila2 = pg_fetch_assoc($resultado2);
        $nombre_servicio_principal = $fila2['nombre'];
    }
}

// 3. Obtener nombre del servicio secundario
$nombre_servicio_secundario = 'No encontrado';
if ($servicio) {
    $query3 = "SELECT nombre FROM servicios_secundarios WHERE id = $1";
    $resultado3 = pg_query_params($conexion, $query3, [$servicio]);
    if ($resultado3 && pg_num_rows($resultado3) > 0) {
        $fila3 = pg_fetch_assoc($resultado3);
        $nombre_servicio_secundario = $fila3['nombre'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Verifica tu cita | ICEO</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="icon" type="image/png" href="../Imagenes/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container mt-4">
    <!-- Logo superior -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="https://www.oaxaca.gob.mx/iceo/">
            <img src="../Imagenes/logo1.png" alt="logo" style="height: 50px;">
        </a>
    </div>

    <!-- Encabezado -->
    <h2 class="text-center mb-4">Verifica tu información</h2>

    <!-- Datos del formulario -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Nombre completo:</label>
                <p class="form-control-plaintext"><?= htmlspecialchars($nombre) ?></p>
            </div>

            <div class="mb-3">
                <label class="form-label">Teléfono:</label>
                <p class="form-control-plaintext"><?= htmlspecialchars($telefono) ?: 'No proporcionado' ?></p>
            </div>

            <div class="mb-3">
                <label class="form-label">Correo electrónico:</label>
                <p class="form-control-plaintext"><?= htmlspecialchars($correo) ?: 'No proporcionado' ?></p>
            </div>

            <div class="mb-3">
                <label class="form-label">Folio Jurídico:</label>
                <p class="form-control-plaintext"><?= htmlspecialchars($folio) ?></p>
            </div>

            <div class="mb-3">
                <label class="form-label">Fecha de la cita:</label>
                <p class="form-control-plaintext"><?= htmlspecialchars($fecha_cita) ?></p>
            </div>

            <div class="mb-3">
                <label class="form-label">Hora de la cita:</label>
                <p class="form-control-plaintext"><?= htmlspecialchars($hora_cita) ?></p>
            </div>

            <div class="mb-3">
                <label class="form-label">Departamento principal:</label>
                <p class="form-control-plaintext"><?= htmlspecialchars($nombre_departamento) ?></p>
            </div>

            <div class="mb-3">
                <label class="form-label">Servicio principal:</label>
                <p class="form-control-plaintext"><?= htmlspecialchars($nombre_servicio_principal) ?></p>
            </div>

            <div class="mb-3">
                <label class="form-label">Servicio secundario:</label>
                <p class="form-control-plaintext"><?= htmlspecialchars($nombre_servicio_secundario) ?></p>
            </div>

            <!-- Botones -->
            <div class="d-flex justify-content-between mt-4">
                <form action="guardar_datos.php" method="POST">
                    <?php foreach ($_SESSION['datos_cita'] as $key => $value): ?>
                        <input type="hidden" name="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($value) ?>">
                    <?php endforeach; ?>
                    <button type="submit" class="btn btn-primary">Confirmar y Guardar</button>
                </form>
            </div>
        </div>
    </div>


<!-- JS de Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
