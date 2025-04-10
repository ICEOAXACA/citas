<?php
session_start(); // Iniciar la sesión

require_once '../../php/c.php';

// Obtener el ID de departamento desde la URL (si existe)
$departamento_id = isset($_GET['departamento']) ? $_GET['departamento'] : null;

// Consultar servicios principales
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

// Consultar servicios secundarios si se pasa el servicio principal
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

// Guardar servicio seleccionado en sesión y redirigir
if (isset($_GET['servicio_secundario_id'])) {
    $_SESSION['servicio'] = $_GET['servicio_secundario_id'];
    header("Location: requisitoservicios.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es" dir="ltr">
  <head>
    <link rel="icon" type="image/png" href="../Imagenes/favicon.ico">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citas ICEO</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <link rel="stylesheet" href="style.css">
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
        <h2>Registro de Citas ICEO</h2>
      </header>

      <div class="container mt-4">
        <form method="get">
          <!-- Servicios secundarios -->
          <?php if (isset($_GET['servicio_id']) && !empty($servicios_secundarios)): ?>
          <div class="mb-3">
            <label for="servicio_secundario" class="form-label">Selecciona un servicio</label>
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

          <!-- Botón -->
          <div class="mb-3 text-center">
            <button type="submit" class="btn btn-primary">Siguiente</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
