<?php
// Incluye la conexión a la base de datos
require_once '../php/c.php';  // Asegúrate de que la conexión esté incluida correctamente

// Obtener el ID de departamento desde la URL (si existe)
$departamento_id = isset($_GET['departamento']) ? $_GET['departamento'] : null;

// Verificar si se pasó el ID del departamento por GET
if ($departamento_id) {
    // Consultar los servicios_principales filtrando por departamento_id y estatus = 't'
    $sql = "SELECT nombre FROM servicios_principales WHERE departamento_id = $1 AND estatus = 't'";
    $result = pg_query_params($conexion, $sql, array($departamento_id));  // Ejecutar la consulta con parámetros para evitar inyección SQL

    // Verificar si se obtuvieron resultados
    if ($result) {
        // Guardar los servicios en un array
        $servicios = [];
        while ($row = pg_fetch_assoc($result)) {
            $servicios[] = $row['nombre'];  // Solo extraemos la columna 'nombre'
        }
    } else {
        $servicios = [];  // En caso de que no haya servicios para el departamento proporcionado o estatus != 't'
    }
} else {
    $servicios = [];  // Si no se proporcionó un departamento, no mostrar nada
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
      <!-- Barra superior -->
      <div class="d-flex justify-content-between align-items-center py-3">
        <a href="https://www.oaxaca.gob.mx/iceo/" class="logo">
          <img src="../Imagenes/logo1.png" alt="logo" class="logo-img" style="height: 50px;">
        </a>
      </div>

      <!-- Título centrado -->
      <header class="text-center mb-4">
        <h2>Registro de Citas ICEO</h2>
      </header>

      <!-- Formulario -->
      <div class="container mt-4">
        <form action="#">
          <!-- Servicios principales -->
          <div class="mb-3">
            <label for="servicio" class="form-label">Seleccione un Servicio</label>
            <select class="form-select" id="servicio" name="servicio" required>
              <option value="" selected disabled>Seleccione una opción</option>
              <?php
                // Mostrar los servicios que coinciden con el departamento_id y estatus = 't'
                foreach ($servicios as $servicio) {
                    echo "<option value=\"" . htmlspecialchars($servicio) . "\">" . htmlspecialchars($servicio) . "</option>";
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

    <!-- Incluir JS de Bootstrap desde CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
