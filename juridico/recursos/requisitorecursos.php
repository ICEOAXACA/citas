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

  <style>
    body {
      background: url('../../Imagenes/Fondo.jpeg') no-repeat center center fixed;
      background-size: cover;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      min-height: 100vh;
      margin: 0;
    }

    .container, .container-fluid {
      background: transparent !important;
      box-shadow: none !important;
      border: none !important;
    }

    .form-container {
      background-color: rgba(255, 255, 255, 0.95);
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
      padding: 2rem;
      max-width: 700px;
      margin: 3rem auto;
    }

    .nav-link {
      font-weight: bold;
      color: #fff;
      text-decoration: none;
      background-color: #861f41;
      padding: 0.3rem 0.7rem;
      border-radius: 5px;
    }

    .nav-link:hover {
      background-color: #6e1b37;
      color: #fff;
    }

    .top-bar {
      background-color: rgba(255, 255, 255, 0.9);
      border-bottom: 2px solid #861f41;
      padding: 1rem;
    }

    h2 {
      color: #343a40;
    }
  </style>
</head>

<body>
  <div class="container-fluid">
    <!-- Barra superior -->
    <div class="top-bar">
      <div class="row align-items-center justify-content-center">
        <div class="col-auto">
          <a href="https://www.oaxaca.gob.mx/iceo/" class="logo">
            <img src="../../Imagenes/logo1.png" alt="logo" class="logo-img" style="height: 50px;">
          </a>
        </div>
        <div class="col text-center" style="min-width: 300px;">
          <span class="fw-bold fs-4" style="color:#343a40;">Requerimientos para la cita</span>
        </div>
        <div class="col-auto">
          <nav>
            <a href="../../login.php" class="nav-link">Iniciar sesión</a>
          </nav>
        </div>
      </div>
    </div>

    <!-- Contenedor principal -->
    <div class="form-container">
      <h4 class="text-center mb-4">REQUERIMIENTOS PARA PRESENTARSE A LA CITA</h4>
      
      <!-- Lista dinámica -->
      <ol class="list-group text-start mb-4">
        <?php if (!empty($requisitos_filtrados)): ?>
          <?php foreach ($requisitos_filtrados as $index => $nombre): ?>
            <li class="list-group-item"><?= ($index + 1) . ". " . htmlspecialchars($nombre) ?></li>
          <?php endforeach; ?>
        <?php else: ?>
          <li class="list-group-item">No hay requisitos disponibles para el servicio seleccionado.</li>
        <?php endif; ?>
      </ol>

      <!-- Botón de siguiente -->
      <div class="text-center">
            <button type="button" class="btn btn-secondary" onclick="window.history.back()">Regresar</button>

        <a href="../registrarcita.php" class="btn btn-primary">Siguiente</a>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
