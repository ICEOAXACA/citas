<?php
// Inicio la sesión para manejar los datos del usuario
session_start();
// Incluyo la conexión a la base de datos
require_once '../php/c.php';

// Obtengo el ID de departamento desde la URL (si existe y es válido)
$departamento_id = isset($_GET['departamento']) ? filter_var($_GET['departamento'], FILTER_VALIDATE_INT) : null;

$servicios = [];  // Aquí voy a guardar los servicios principales del departamento

if ($departamento_id) {
    // Consulto los servicios principales activos para el departamento seleccionado
    $sql = "SELECT id, nombre FROM servicios_principales WHERE departamento_id = $1 AND estatus = 't'";
    $result = pg_query_params($conexion, $sql, array($departamento_id));

    // Si la consulta falla, lo registro en el log y muestro error
    if (!$result) {
        error_log('Error en la consulta: ' . pg_last_error($conexion));
        die('Hubo un problema al obtener los servicios, por favor intente nuevamente más tarde.');
    }

    // Guardo los servicios en el arreglo para mostrarlos en el select
    while ($row = pg_fetch_assoc($result)) {
        $servicios[] = [
            'id' => $row['id'],
            'nombre' => $row['nombre']
        ];
    }
}

// Si el usuario selecciona un servicio, lo guardo en la sesión y redirijo según el tipo
if (isset($_GET['servicio'])) {
    $servicio_id = $_GET['servicio'];
    $_SESSION['secundario'] = $servicio_id;

    if ($servicio_id == 1) {
        header("Location: servicios/servicios.php?servicio_id=" . urlencode($servicio_id));
        exit();
    } elseif ($servicio_id == 2) {
        header("Location: recursos/recursos.php?servicio_id=" . urlencode($servicio_id));
        exit();
    } else {
        echo "Servicio no válido.";
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

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <link rel="stylesheet" href="style.css">

    <style>
        body {
            background: url('../Imagenes/Fondo.jpeg') no-repeat center center fixed;
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
                        <img src="../Imagenes/logo1.png" alt="logo" class="logo-img" style="height: 50px;">
                    </a>
                </div>
                <div class="col text-center" style="min-width: 300px;">
                    <span class="fw-bold fs-4" style="color:#343a40;">Registro de Citas ICEO</span>
                </div>
                <div class="col-auto">
                    <nav>
                        <a href="../login.php" class="nav-link">Iniciar sesión</a>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Formulario -->
        <div class="container">
            <div class="form-container">
                <form action="" method="get">
                    <div class="mb-3">
                        <label for="servicio" class="form-label">Seleccione el área correspondiente a su trámite</label>
                        <select class="form-select" id="servicio" name="servicio" required aria-label="Seleccione un servicio">
                            <option value="" selected disabled>Seleccione una opción</option>
                            <?php
                            foreach ($servicios as $servicio) {
                                echo "<option value=\"" . htmlspecialchars($servicio['id']) . "\">" . htmlspecialchars($servicio['nombre']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>

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
