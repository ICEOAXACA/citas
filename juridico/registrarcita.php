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
            <label for="servicio" class="form-label">Hola registrar cita</label>
            <select class="form-select" id="servicio" name="servicio" required>
              <option value="" selected disabled>Seleccione una opción</option>
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
