<?php
// Incluyo la conexión a la base de datos (esto es para poder hacer consultas)
require_once '../../php/c.php';  // Asegúrate de que la conexión esté incluida correctamente

// Aquí obtengo el ID de departamento desde la URL si es que viene
$departamento_id = isset($_GET['departamento']) ? $_GET['departamento'] : null;

// Si sí me mandaron el ID del departamento por GET
if ($departamento_id) {
    // Consulto los servicios principales de ese departamento que estén activos
    $sql = "SELECT nombre FROM servicios_principales WHERE departamento_id = $1 AND estatus = 't'";
    $result = pg_query_params($conexion, $sql, array($departamento_id));  // Ejecuto la consulta con parámetros para evitar inyección SQL

    // Si la consulta fue exitosa, guardo los servicios en un arreglo
    if ($result) {
        $servicios = [];
        while ($row = pg_fetch_assoc($result)) {
            $servicios[] = $row['nombre'];  // Solo extraigo el nombre del servicio
        }
    } else {
        $servicios = [];  // Si no hay servicios o hubo error, dejo el arreglo vacío
    }
} else {
    $servicios = [];  // Si no hay departamento, no muestro nada
}
?>

<!DOCTYPE html>
<html lang="es" dir="ltr">
  <head>
    <link rel="icon" type="image/png" href="../Imagenes/favicon.ico">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citas ICEO</title>

    <!-- Enlace a Bootstrap desde el CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Fuente para iconos -->
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <link rel="stylesheet" href="style.css">
  </head>
  <body>
    <!-- Contenedor principal -->
    <div class="container-fluid">
      <!-- Barra superior con el logo -->
      <div class="d-flex justify-content-between align-items-center py-3">
        <!-- Logo del ICEO, si le das click te lleva a la página oficial -->
        <a href="https://www.oaxaca.gob.mx/iceo/" class="logo">
          <img src="../Imagenes/logo1.png" alt="logo" class="logo-img" style="height: 50px;">
        </a>
      </div>

      <!-- Título centrado de la página -->
      <header class="text-center mb-4">
        <h2>Registro de Citas ICEO</h2>
      </header>

      <!-- Formulario para seleccionar el servicio principal -->
      <div class="container mt-4">
        <form action="#">
          <!-- Aquí el usuario elige el servicio principal según el departamento -->
          <div class="mb-3">
            <label for="servicio" class="form-label">Hola legal</label>
            <select class="form-select" id="servicio" name="servicio" required>
              <option value="" selected disabled>Seleccione una opción</option>
              <?php
                // Aquí se imprimen los servicios principales que traje de la base de datos
                foreach ($servicios as $servicio) {
                    echo "<option value=\"" . htmlspecialchars($servicio) . "\">" . htmlspecialchars($servicio) . "</option>";
                }
              ?>
            </select>
          </div>

          <!-- Botón para continuar después de seleccionar el servicio -->
          <div class="mb-3 text-center">
            <button type="submit" class="btn btn-primary">Siguiente</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Incluir JS de Bootstrap desde CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
