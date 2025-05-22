<?php
session_start();
require_once '../php/c.php';
require_once '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Guardar idhora si viene desde POST
if (isset($_POST['idhora'])) {
    $_SESSION['idhora'] = $_POST['idhora'];
}

// Guardar datos del ciudadano si vienen desde POST
if (
    isset($_POST['nombre'], $_POST['telefono'], $_POST['correo'], $_POST['folio'], $_POST['fecha_cita'], $_POST['hora_cita']) &&
    (!isset($_SESSION['datos_cita']) || empty($_SESSION['datos_cita']))
) {
    $_SESSION['datos_cita'] = [
        'nombre' => $_POST['nombre'],
        'telefono' => $_POST['telefono'],
        'correo' => $_POST['correo'],
        'folio' => $_POST['folio'],
        'fecha_cita' => $_POST['fecha_cita'],
        'hora_cita' => $_POST['hora_cita']
    ];
}

$datos_cita = $_SESSION['datos_cita'] ?? [];

$nombre = $datos_cita['nombre'] ?? '';
$telefono = $datos_cita['telefono'] ?? '';
$correo = $datos_cita['correo'] ?? '';
$folio = $datos_cita['folio'] ?? '';
$fecha_cita = $datos_cita['fecha_cita'] ?? '';
$hora_cita = $datos_cita['hora_cita'] ?? '';

$principal = $_SESSION['principal'] ?? null;
$secundario = $_SESSION['secundario'] ?? null;
$servicio = $_SESSION['servicio'] ?? null;
$idhora = $_SESSION['idhora'] ?? null;

$nombre_departamento = $nombre_servicio_principal = $nombre_servicio_secundario = 'No encontrado';

// Obtener nombres desde la base de datos
if ($principal) {
    $res = pg_query_params($conexion, "SELECT nombre FROM departamentos WHERE id = $1", [$principal]);
    if ($res && pg_num_rows($res) > 0)
        $nombre_departamento = pg_fetch_result($res, 0, 'nombre');
}
if ($secundario) {
    $res = pg_query_params($conexion, "SELECT nombre FROM servicios_principales WHERE id = $1", [$secundario]);
    if ($res && pg_num_rows($res) > 0)
        $nombre_servicio_principal = pg_fetch_result($res, 0, 'nombre');
}
if ($servicio) {
    $res = pg_query_params($conexion, "SELECT nombre FROM servicios_secundarios WHERE id = $1", [$servicio]);
    if ($res && pg_num_rows($res) > 0)
        $nombre_servicio_secundario = pg_fetch_result($res, 0, 'nombre');
}

// Procesar el registro de cita
if (isset($_POST['registrar_cita'])) {
    if (!$idhora || !$nombre || (!$telefono && !$correo) || !$fecha_cita) {
        $mensaje_error = "❌ Debes proporcionar al menos un número de teléfono o un correo electrónico.";
    } else {
        $fecha_obj = DateTime::createFromFormat('Y-m-d', $fecha_cita);
        $hoy = new DateTime();
        $hoy->setTime(0, 0, 0);

        if (!$fecha_obj) {
            $mensaje_error = "❌ Fecha inválida. Intenta seleccionar otra.";
        } elseif ($fecha_obj < $hoy) {
            $mensaje_error = "❌ No puedes registrar una cita en una fecha pasada.";
        } else {
            $fecha_formateada = $fecha_obj->format('Y-m-d');

            $params = [
                $nombre,
                $telefono,
                $correo,
                $fecha_formateada,
                $idhora,
                $folio,
                $principal,
                $secundario,
                $servicio
            ];

            $query = "INSERT INTO historial_citas (
                nombre_contribuyente, telefono, correo, fecha, hora_id, folio,
                departamento_id, servicio_principal_id, servicio_secundario_id
            ) VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9)";

            $resultado_insert = pg_query_params($conexion, $query, $params);

            if ($resultado_insert && pg_affected_rows($resultado_insert) > 0) {
                // Preparar datos del PDF
                $logo_path = $_SERVER['DOCUMENT_ROOT'] . '/citasiceo/imagenes/logoiceo2017.png';
                $_SESSION['acuse_pdf'] = [
                    'nombre' => $nombre,
                    'telefono' => $telefono,
                    'correo' => $correo,
                    'folio' => $folio,
                    'fecha_cita' => $fecha_cita,
                    'hora_cita' => $hora_cita,
                    'departamento' => $nombre_departamento,
                    'nombre_servicio_principal' => $nombre_servicio_principal,
                    'nombre_servicio_secundario' => $nombre_servicio_secundario,
                    'logo' => $logo_path,
                    // Agregar los IDs para consulta de requisitos
                    'servicio' => $servicio,
                    'secundario' => $secundario,
                    'principal' => $principal
                ];

                // Limpiar sesión (datos ya pasaron a sesión temporal)
                unset($_SESSION['datos_cita']);
                unset($_SESSION['idhora']);
                unset($_SESSION['principal']);
                unset($_SESSION['secundario']);
                unset($_SESSION['servicio']);

                header("Location: acuse_generado.php");
                exit;
            } else {
                $mensaje_error = "❌ Error al registrar la cita: " . pg_last_error($conexion);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Acuse de Cita | ICEO</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('../Imagenes/Fondo.jpeg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', sans-serif;
        }

        .acuse-container {
            background-color: rgba(255, 255, 255, 0.96);
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.3);
            max-width: 800px;
            margin: 40px auto;
        }

        .acuse-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .acuse-header img {
            height: 60px;
        }

        .acuse-title {
            font-size: 1.8rem;
            font-weight: bold;
            margin-top: 10px;
            text-transform: uppercase;
        }

        .seccion {
            font-weight: bold;
            font-size: 1.1rem;
            color: #2c3e50;
            margin-bottom: 15px;
            border-bottom: 2px solid #ccc;
            padding-bottom: 5px;
        }

        .dato-label {
            font-weight: 600;
            color: #444;
        }

        .dato {
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px dashed #ccc;
        }

        .folio {
            font-size: 1.2rem;
            font-weight: bold;
            color: #2c3e50;
        }

        .btn-imprimir {
            margin-top: 30px;
            text-align: center;
        }

        .nota {
            font-size: 0.9rem;
            color: #666;
            margin-top: 20px;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }

        @media print {
            .btn-imprimir {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="acuse-container">
        <div class="acuse-header">
            <img src="../Imagenes/logo1.png" alt="Logo">
            <div class="acuse-title">Acuse de Cita</div>
            <div class="text-muted">Instituto Catastral del Estado de Oaxaca</div>
        </div>

        <?php if (!empty($mensaje_error)): ?>
            <div class="alert alert-danger"><?= $mensaje_error ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-6">
                <div class="seccion">Datos del ciudadano</div>
                <div><span class="dato-label">Nombre completo:</span>
                    <div class="dato"><?= htmlspecialchars($nombre) ?></div>
                </div>
                <div><span class="dato-label">Teléfono:</span>
                    <div class="dato"><?= htmlspecialchars($telefono ?: 'No proporcionado') ?></div>
                </div>
                <div><span class="dato-label">Correo electrónico:</span>
                    <div class="dato"><?= htmlspecialchars($correo ?: 'No proporcionado') ?></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="seccion">Detalles de la cita</div>
                <div><span class="dato-label">Folio jurídico:</span>
                    <div class="dato folio"><?= htmlspecialchars($folio) ?></div>
                </div>
                <div><span class="dato-label">Fecha:</span>
                    <div class="dato"><?= htmlspecialchars($fecha_cita) ?></div>
                </div>
                <div><span class="dato-label">Hora:</span>
                    <div class="dato"><?= htmlspecialchars($hora_cita) ?></div>
                </div>
            </div>
        </div>

        <div class="seccion mt-4">Servicios solicitados</div>
        <div><span class="dato-label">Departamento:</span>
            <div class="dato"><?= htmlspecialchars($nombre_departamento) ?></div>
        </div>
        <div><span class="dato-label">Área del Servicio:</span>
            <div class="dato"><?= htmlspecialchars($nombre_servicio_principal) ?></div>
        </div>
        <div><span class="dato-label">Servicio seleccionado:</span>
            <div class="dato"><?= htmlspecialchars($nombre_servicio_secundario) ?></div>
        </div>

        <div class="d-flex justify-content-center align-items-center gap-3 mt-4 btn-imprimir">
            <button type="button" class="btn btn-secondary" onclick="window.history.back()">Regresar</button>
            <form method="post" class="m-0">
                <button type="submit" name="registrar_cita" class="btn btn-success">Registrar cita</button>
            </form>
        </div>

        <div class="nota">
            Registre su cita y guarde su acuse. Le recomendamos presentarse con al menos 10 minutos de anticipación.
        </div>
    </div>
</body>
</html>