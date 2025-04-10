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
        <?php
          // Incluir la conexión PostgreSQL
          require_once '../../php/c.php';

          $sql = "SELECT nombre FROM requisitos";
          $resultado = pg_query($conexion, $sql);

          if ($resultado && pg_num_rows($resultado) > 0) {
            $contador = 1;
            while ($fila = pg_fetch_assoc($resultado)) {
              echo "<li class='list-group-item'>{$contador}. " . htmlspecialchars($fila['nombre']) . "</li>";
              $contador++;
            }
          } else {
            echo "<li class='list-group-item'>No hay requisitos disponibles.</li>";
          }

          pg_close($conexion);
        ?>
      </ol>
    </div>

    <!-- Botón de siguiente -->
    <div class="mb-3 text-center mt-4">
      <a href="siguiente_paso.php" class="btn btn-primary">Siguiente</a>
    </div>
  </div>

  <?php
    // Mostrar sesiones al final
    session_start();
  ?>
  <div class="container mt-5 mb-5">
    <h5 class="text-center">Sesiones activas</h5>
    <ul class="list-group">
      <li class="list-group-item"><strong>Departamento (principal):</strong> <?= isset($_SESSION['principal']) ? htmlspecialchars($_SESSION['principal']) : 'No definido' ?></li>
      <li class="list-group-item"><strong>Servicio principal (secundario):</strong> <?= isset($_SESSION['secundario']) ? htmlspecialchars($_SESSION['secundario']) : 'No definido' ?></li>
      <li class="list-group-item"><strong>Recurso:</strong> <?= isset($_SESSION['servicio']) ? htmlspecialchars($_SESSION['servicio']) : 'No definido' ?></li>
    </ul>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
