<?php
session_start(); // Iniciar sesión
require_once '../../php/c.php'; // Conexión a la BD

$departamento_id = isset($_GET['departamento']) ? $_GET['departamento'] : null;

// Obtener servicios principales
if ($departamento_id) {
    $sql = "SELECT id, nombre FROM servicios_principales WHERE departamento_id = $1 AND estatus = 't'";
    $result = pg_query_params($conexion, $sql, array($departamento_id));

    if ($result) {
        $servicios = [];
        while ($row = pg_fetch_assoc($result)) {
            $servicios[] = ['id' => $row['id'], 'nombre' => $row['nombre']];
        }
    } else {
        $servicios = [];
    }
} else {
    $servicios = [];
}

// Obtener servicios secundarios
$servicios_secundarios = [];
if (isset($_GET['servicio_id']) && !empty($_GET['servicio_id'])) {
    $servicio_id = $_GET['servicio_id'];

    $sql_secundarios = "SELECT id, nombre FROM servicios_secundarios WHERE servicio_principal_id = $1 AND estatus = 't'";
    $result_secundarios = pg_query_params($conexion, $sql_secundarios, array($servicio_id));

    if ($result_secundarios) {
        while ($row = pg_fetch_assoc($result_secundarios)) {
            $servicios_secundarios[] = ['id' => $row['id'], 'nombre' => $row['nombre']];
        }
    }
}

// Guardar el servicio secundario seleccionado en la sesión
if (isset($_GET['servicio_secundario_id'])) {
    $_SESSION['servicio'] = $_GET['servicio_secundario_id'];

    // Redirigir a requisitorecursos.php
    header("Location: requisitorecursos.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es" dir="ltr">
<head>
  <link rel="icon" type="image/png" href="../../Imagenes/favicon.ico">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Citas ICEO</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js"></script>
  <link rel="stylesheet" href="style.css">

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
      padding: 3rem 2rem;
      max-width: 600px;
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
          <span class="fw-bold fs-4" style="color:#343a40;">Registro de Citas ICEO</span>
        </div>
        <div class="col-auto">
          <nav>
            <a href="../../login.php" class="nav-link">Iniciar sesión</a>
          </nav>
        </div>
      </div>
    </div>

    <!-- Contenedor del formulario -->
    <div class="container">
      <div class="form-container">
        <form method="get">
          <?php if (isset($_GET['servicio_id']) && !empty($servicios_secundarios)): ?>
            <div class="mb-3">
              <label for="servicio_secundario" class="form-label">Selecciona un recurso</label>
              <select class="form-select" id="servicio_secundario" name="servicio_secundario_id" required>
                <option value="" selected disabled>Seleccione una opción</option>
                <?php foreach ($servicios_secundarios as $servicio_secundario): ?>
                  <option value="<?= htmlspecialchars($servicio_secundario['id']) ?>">
                    <?= htmlspecialchars($servicio_secundario['nombre']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          <?php elseif (isset($_GET['servicio_id'])): ?>
            <p class="alert alert-warning">No hay servicios secundarios disponibles para el servicio seleccionado.</p>
          <?php endif; ?>

          <div class="mb-3 text-center">
            <button type="button" class="btn btn-secondary" onclick="window.history.back()">Regresar</button>

            <button type="submit" class="btn btn-primary">Siguiente</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
