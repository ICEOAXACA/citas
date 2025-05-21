<?php
session_start();
require_once '../../php/c.php';

$requisitos_filtrados = [];

if (isset($_SESSION['secundario'])) {
  $servicio_secundario_id = $_SESSION['secundario'];

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
      margin: 0;
      min-height: 100vh;
    }

    .top-bar {
      background-color: rgba(255, 255, 255, 0.95);
      border-bottom: 2px solid #861f41;
      padding: 1rem;
    }

    .form-container {
      background-color: rgba(255, 255, 255, 0.95);
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
      padding: 3rem 2rem;
      max-width: 700px;
      margin: 2rem auto;
    }

    .btn-primary {
      background-color: #861f41;
      border-color: #861f41;
    }

    .btn-primary:hover {
      background-color: #6e1b37;
      border-color: #6e1b37;
    }

    .btn-success {
      background-color: #28a745;
      border-color: #28a745;
    }

    .btn-success:hover {
      background-color: #218838;
      border-color: #1e7e34;
    }

    h2 {
      color: #343a40;
    }

    .logo-img {
      height: 50px;
    }
  </style>
</head>

<body>
  <div class="container-fluid">
    <!-- Barra superior -->
    <div class="top-bar">
      <div class="row align-items-center justify-content-center">
        <div class="col-auto">
          <a href="https://www.oaxaca.gob.mx/iceo/">
            <img src="../../Imagenes/logo1.png" alt="logo" class="logo-img">
          </a>
        </div>
        <div class="col text-center">
          <span class="fw-bold fs-4">Registro de Citas ICEO</span>
        </div>
      </div>
    </div>

    <!-- Contenedor del contenido -->
    <div class="container">
      <div class="form-container">
        <!-- Título -->
        <header class="text-center mb-4">
          <h2>REQUERIMIENTOS PARA PRESENTARSE A LA CITA</h2>
        </header>

        <!-- Lista dinámica desde PostgreSQL -->
        <ol class="list-group text-start">
          <?php if (!empty($requisitos_filtrados)): ?>
            <?php foreach ($requisitos_filtrados as $index => $nombre): ?>
              <li class="list-group-item"><?= ($index + 1) . ". " . htmlspecialchars($nombre) ?></li>
            <?php endforeach; ?>
          <?php else: ?>
            <li class="list-group-item">No hay requisitos disponibles para el servicio seleccionado.</li>
          <?php endif; ?>
        </ol>

        <!-- Botón para descargar el PDF -->
        <div class="text-center mt-4">
          <a id="btnDescargarPDF" href="SERVICIOS-JURIDICOS.pdf" download class="btn btn-success">
            Descargar formato de Servicios Jurídicos
            <i class="fas fa-file-download ms-2"></i>
          </a>
        </div>

        <!-- Botón de siguiente -->
        <div class="text-center mt-4">
            <button type="button" class="btn btn-secondary" onclick="window.history.back()">Regresar</button>

          <a id="btnSiguiente" href="../registrarcita.php" class="btn btn-primary disabled" tabindex="-1" aria-disabled="true">
            Siguiente
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Script para habilitar el botón Siguiente -->
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const btnDescargar = document.getElementById("btnDescargarPDF");
      const btnSiguiente = document.getElementById("btnSiguiente");

      btnDescargar.addEventListener("click", function () {
        btnSiguiente.classList.remove("disabled");
        btnSiguiente.removeAttribute("aria-disabled");
        btnSiguiente.setAttribute("tabindex", "0");
      });
    });
  </script>
</body>
</html>
