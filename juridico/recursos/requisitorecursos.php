<?php
session_start();
require_once '../../php/c.php';

// Verificamos que la sesión 'secundario' esté definida
$requisitos_filtrados = [];

if (isset($_SESSION['secundario'])) {
    $servicio_secundario_id = $_SESSION['secundario'];

    // Consulta para obtener los requisitos relacionados y activos
    $sql = "
        SELECT r.nombre
        FROM requisitos r
        JOIN requisitos_servicios_secundarios rss
          ON r.id = rss.requisito_id
        WHERE rss.servicio_secundario_id = $1
          AND r.estatus = 't'
          AND rss.estatus = 't'
    ";

    $resultado = pg_query_params($conexion, $sql, array($servicio_secundario_id));

    if ($resultado && pg_num_rows($resultado) > 0) {
        while ($fila = pg_fetch_assoc($resultado)) {
            $requisitos_filtrados[] = $fila['nombre'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es" dir="ltr">
<head>
  <link rel="icon" type="image/x-icon" href="../../Imagenes/favicon.ico">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Citas ICEO</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="../../style.css">
</head>
<body>
  <div class="container-fluid">
    <!-- Barra superior -->
    <div class="d-flex justify-content-between align-items-center py-3">
      <a href="https://www.oaxaca.gob.mx/iceo/" class="logo">
        <img src="../../Imagenes/logo1.png" alt="logo" class="logo-img" style="height: 50px;">
      </a>
    </div>

    <!-- Título -->
    <header class="text-center mb-4">
      <h2>REQUERIMIENTOS PARA REGISTRAR LA CITA</h2>
    </header>

    <!-- Lista dinámica desde PostgreSQL -->
    <div class="container mt-4">
      <ol class="list-group text-start">
        <?php if (!empty($requisitos_filtrados)): ?>
          <?php foreach ($requisitos_filtrados as $index => $nombre): ?>
            <li class="list-group-item"><?= ($index + 1) . ". " . htmlspecialchars($nombre) ?></li>
          <?php endforeach; ?>
        <?php else: ?>
          <li class="list-group-item">No hay requisitos disponibles para el servicio seleccionado.</li>
        <?php endif; ?>
      </ol>
    </div>

    <!-- Botón de siguiente -->
    <div class="mb-3 text-center mt-4">
      <a href="../registrarcita.php" class="btn btn-primary">Siguiente</a>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
