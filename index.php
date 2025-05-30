<?php
// Inicio la sesión para poder guardar datos del usuario si es necesario
session_start();
// Incluyo el archivo de conexión a la base de datos (aquí es donde se conecta todo el sistema con la BD)
require_once 'php/c.php'; // Conexión a la base de datos

// Hago la consulta para traer los departamentos activos (estatus = 't')
$sql = "SELECT id, nombre FROM departamentos WHERE estatus = 't'";
$result = pg_query($conexion, $sql);

// Aquí guardo los departamentos en un arreglo para usarlos en el select del formulario
$departamentos = [];
if ($result) {
    while ($row = pg_fetch_assoc($result)) {
        $departamentos[] = $row;
    }
}

// Si el usuario ya seleccionó un departamento y envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['departamento'])) {
    $departamento_id = $_POST['departamento'];
    // Guardo el departamento seleccionado en la sesión para usarlo después
    $_SESSION['principal'] = $departamento_id;

    // Si el departamento es el Jurídico (id = 1), lo mando a la página de citas jurídicas
    if ($departamento_id == 1) {
        header("Location: juridico/citasjuridico.php?departamento=" . urlencode($departamento_id));
        exit();
    } else {
        // Si es otro departamento, lo mando a la siguiente página (aquí hay que poner la página correcta)
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

  <style>
    body {
      background: url('Imagenes/Fondo.jpeg') no-repeat center center fixed;
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

    .form-select,
    .form-label {
      font-size: 1.1rem;
    }

    .btn-primary {
      font-size: 0.9rem;
      padding: 0.5rem 1.2rem;
    }

    @media (max-width: 767.98px) {
      .btn-responsive {
        width: 100%;
      }
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
  <!-- Contenedor principal -->
  <div class="container-fluid">
    <!-- Barra superior con el logo, título y botón de login -->
    <div class="top-bar">
      <div class="row align-items-center justify-content-center">
        <div class="col-auto">
          <!-- Logo del ICEO, si le das click te lleva a la página oficial -->
          <a href="https://www.oaxaca.gob.mx/iceo/" class="logo">
            <img src="Imagenes/logo1.png" alt="logo" class="logo-img" style="height: 50px;">
          </a>
        </div>
        <div class="col text-center" style="min-width: 300px;">
          <!-- Título principal de la página -->
          <span class="fw-bold fs-4" style="color:#343a40;">Registro de Citas ICEO</span>
        </div>
        <div class="col-auto">
          <!-- Botón para ir al login de usuarios internos -->
          <nav>
            <a href="login.php" class="nav-link">Iniciar sesión</a>
          </nav>
        </div>
      </div>
    </div>

    <!-- Formulario para seleccionar el departamento -->
    <div class="container">
      <div class="form-container">
        <form action="#" method="POST">
          <!-- Aquí el usuario elige el departamento al que quiere sacar cita -->
          <div class="mb-3">
            <label for="departamento" class="form-label">Selecciona un departamento</label>
            <select class="form-select" id="departamento" name="departamento" required>
              <option value="" selected disabled>Seleccione un departamento</option>
              <?php
                // Aquí se imprimen los departamentos que traje de la base de datos
                foreach ($departamentos as $departamento) {
                    echo "<option value=\"" . htmlspecialchars($departamento['id']) . "\">" . htmlspecialchars($departamento['nombre']) . "</option>";
                }
              ?>
            </select>
          </div>

          <!-- Botón para continuar después de seleccionar el departamento -->
          <div class="mb-3 text-center">
            <button type="submit" class="btn btn-primary btn-sm btn-responsive">Siguiente</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
