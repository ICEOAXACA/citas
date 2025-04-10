<?php
// Iniciar la sesión
session_start();

// Incluye la conexión a la base de datos
require_once '../php/c.php';  // Asegúrate de que la conexión esté incluida correctamente

// Obtener el ID de departamento desde la URL (si existe y es un número válido)
$departamento_id = isset($_GET['departamento']) ? filter_var($_GET['departamento'], FILTER_VALIDATE_INT) : null;

$servicios = [];  // Inicializar el array de servicios vacío

if ($departamento_id) {
    // Consultar los servicios principales filtrando por departamento_id y estatus = 't'
    $sql = "SELECT id, nombre FROM servicios_principales WHERE departamento_id = $1 AND estatus = 't'";
    $result = pg_query_params($conexion, $sql, array($departamento_id));  // Ejecutar la consulta con parámetros

    // Verificar si la consulta fue exitosa
    if (!$result) {
        error_log('Error en la consulta: ' . pg_last_error($conexion));
        die('Hubo un problema al obtener los servicios, por favor intente nuevamente más tarde.');
    }

    // Verificar si se obtuvieron resultados
    while ($row = pg_fetch_assoc($result)) {
        $servicios[] = [
            'id' => $row['id'],  // Suponiendo que el ID está en la columna 'id'
            'nombre' => $row['nombre']
        ];
    }
}

// Verificar si el formulario fue enviado y si el servicio es válido
if (isset($_GET['servicio'])) {
    $servicio_id = $_GET['servicio'];

    // Guardar el ID del servicio en la sesión llamada 'secundario'
    $_SESSION['secundario'] = $servicio_id;

    // Redirigir dependiendo del ID del servicio y pasar el ID por la URL
    if ($servicio_id == 1) {
        header("Location: servicios/servicios.php?servicio_id=" . urlencode($servicio_id));  // Redirigir a servicios.php con el ID en la URL
        exit();
    } elseif ($servicio_id == 2) {
        header("Location: recursos/recursos.php?servicio_id=" . urlencode($servicio_id));  // Redirigir a legal.php con el ID en la URL
        exit();
    } else {
        echo "Servicio no válido.";  // Opcional: manejar servicios no válidos
    }
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
            <form action="" method="get">
                <!-- Servicios principales -->
                <div class="mb-3">
                    <label for="servicio" class="form-label">Selecciona el área del servicio</label>
                    <select class="form-select" id="servicio" name="servicio" required aria-label="Seleccione un servicio">
                        <option value="" selected disabled>Seleccione una opción</option>
                        <?php
                        // Mostrar los servicios que coinciden con el departamento_id y estatus = 't'
                        foreach ($servicios as $servicio) {
                            echo "<option value=\"" . htmlspecialchars($servicio['id']) . "\">" . htmlspecialchars($servicio['nombre']) . "</option>";
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
