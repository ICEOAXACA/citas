<?php
session_start();
require_once 'php/c.php'; // Conexión a la base de datos

// Consultar departamentos
$sql = "SELECT id, nombre FROM departamentos WHERE estatus = 't'";
$result = pg_query($conexion, $sql);

$departamentos = [];
if ($result) {
    while ($row = pg_fetch_assoc($result)) {
        $departamentos[] = $row;
    }
}

// Verificar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['departamento'])) {
    $departamento_id = $_POST['departamento'];
    $_SESSION['principal'] = $departamento_id;

    // Redirigir según el departamento seleccionado
    if ($departamento_id == 1) {
        header("Location: juridico/citasjuridico.php?departamento=" . urlencode($departamento_id));
        exit();
    } else {
        header("Location: siguiente_pagina.php"); // Cambia esto al siguiente paso normal
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es" dir="ltr">
<head>
  <link rel="icon" type="image/png" href="./Imagenes/favicon.ico">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro de citas ICEO</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js"></script>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <!-- Contenedor principal -->
  <div class="container-fluid">
    <!-- Barra superior -->
    <div class="d-flex justify-content-between align-items-center py-3">
      <a href="https://www.oaxaca.gob.mx/iceo/" class="logo">
        <img src="Imagenes/logo1.png" alt="logo" class="logo-img" style="height: 50px;">
      </a>
      <nav>
        <a href="login.php" class="nav-link">Iniciar sesión</a>
      </nav>
    </div>

    <!-- Título centrado -->
    <header class="text-center mb-4">
      <h2>Registro de Citas ICEO</h2>
    </header>

    <!-- Formulario -->
    <div class="container mt-4">
      <form action="#" method="POST">
        <!-- Departamento -->
        <div class="mb-3">
          <label for="departamento" class="form-label">Departamento</label>
          <select class="form-select" id="departamento" name="departamento" required>
            <option value="" selected disabled>Seleccione un departamento</option>
            <?php
              foreach ($departamentos as $departamento) {
                  echo "<option value=\"" . htmlspecialchars($departamento['id']) . "\">" . htmlspecialchars($departamento['nombre']) . "</option>";
              }
            ?>
          </select>
        </div>

        <!-- Botón Siguiente -->
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
