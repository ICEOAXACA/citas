<?php
// Incluye la conexión a la base de datos
require_once 'php/c.php';  // Asegúrate de que la conexión esté incluida correctamente

// Consultar los departamentos con estatus 't' desde la base de datos
$sql = "SELECT id, nombre FROM departamentos WHERE estatus = 't'";
$result = pg_query($conexion, $sql);  // Ejecutar la consulta

// Verificar si se obtuvieron resultados
if ($result) {
    // Guardar los departamentos en un array
    $departamentos = [];
    while ($row = pg_fetch_assoc($result)) {
        $departamentos[] = $row;
    }
} else {
    $departamentos = [];  // En caso de que no haya departamentos con estatus 't'
}
?>

<!DOCTYPE html>
<html lang="es" dir="ltr">
  <head>
    <link rel="icon" type="image/png" href="./Imagenes/favicon.ico">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de citas ICEO</title>

    <!-- Enlace a Bootstrap desde el CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Fuente para iconos -->
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <link rel="stylesheet" href="style.css">

    <!-- Script para redirección cuando se presiona el botón Siguiente -->
    <script>
      function redirectToJurídico(event) {
        var departamento = document.getElementById("departamento").value; // Obtener el ID del departamento seleccionado
        if (departamento === "1") { // Si el ID del departamento es "1" (que corresponde a "Juridico")
          event.preventDefault(); // Evitar el envío del formulario
          var idDepartamento = document.getElementById("departamento").value;
          window.location.href = "juridico/citasjuridico.php?departamento=" + encodeURIComponent(idDepartamento); // Redirigir a citasjuridico.php con el ID del departamento en GET
        } else {
          // Si no es "Juridico" (ID != 1), enviar el formulario normalmente
          document.querySelector("form").submit();
        }
      }
    </script>
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
        <form action="#" method="POST" onsubmit="redirectToJurídico(event)">
          <!-- Departamento -->
          <div class="mb-3">
            <label for="departamento" class="form-label">Departamento</label>
            <select class="form-select" id="departamento" name="departamento" required>
              <option value="" selected disabled>Seleccione un departamento</option>
              <?php
                // Mostrar los departamentos de la base de datos con estatus 't'
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

    <!-- Incluir JS de Bootstrap desde CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
