<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../php/c.php';
require_once '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Validar sesión
if (!isset($_SESSION['datos_cita'])) {
    die('No se encontraron datos de la cita en la sesión.');
}

// Datos básicos
$nombre = $_SESSION['datos_cita']['nombre'] ?? 'Nombre no proporcionado';
$telefono = $_SESSION['datos_cita']['telefono'] ?? 'No proporcionado';
$correo = $_SESSION['datos_cita']['correo'] ?? 'No proporcionado';
$folio = $_SESSION['datos_cita']['folio'] ?? 'No proporcionado';
$fecha_cita = $_SESSION['datos_cita']['fecha_cita'] ?? 'Fecha no proporcionada';
$hora_cita = $_SESSION['datos_cita']['hora_cita'] ?? 'Hora no proporcionada';
$departamento = $_SESSION['datos_cita']['departamento'] ?? 'Departamento no proporcionado';

$secundario = $_SESSION['secundario'] ?? null;
$servicio = $_SESSION['servicio'] ?? null;
$principal = $_SESSION['principal'] ?? null;

// Eliminar bloques de depuración y dejar solo la lógica principal

// Obtener requisitos
$requisitos_filtrados = [];
if (is_numeric($servicio)) {
    $sql = "
        SELECT r.nombre
        FROM requisitos r
        JOIN requisitos_servicios_secundarios rss ON r.id = rss.requisito_id
        WHERE rss.servicio_secundario_id = $servicio AND r.estatus = 't' AND rss.estatus = 't'
    ";
    $resultado = pg_query($conexion, $sql);
    if ($resultado && pg_num_rows($resultado) > 0) {
        while ($fila = pg_fetch_assoc($resultado)) {
            $requisitos_filtrados[] = $fila['nombre'];
        }
    }
}

// DEBUG: Guardar los valores de las variables clave y los requisitos obtenidos
file_put_contents(__DIR__ . '/debug_requisitos.txt',
    'servicio=' . var_export($servicio, true) . "\n" .
    'secundario=' . var_export($secundario, true) . "\n" .
    'principal=' . var_export($principal, true) . "\n" .
    'SQL=' . (isset($sql) ? $sql : '') . "\n" .
    'requisitos_filtrados=' . print_r($requisitos_filtrados, true)
);

// Guardar valor de $_SESSION['servicio'] y $servicio para depuración
file_put_contents(__DIR__ . '/debug_servicio.txt', '$_SESSION[servicio]=' . var_export($_SESSION['servicio'], true) . ' $servicio=' . var_export($servicio, true));

// Obtener nombres de servicios
$nombre_servicio_principal = 'No proporcionado';
$nombre_servicio_secundario = 'No proporcionado';
$nombre_departamento = 'No proporcionado';

if ($principal) {
    $res = pg_query($conexion, "SELECT nombre FROM departamentos WHERE id = $principal");
    if ($res && pg_num_rows($res) > 0) {
        $nombre_departamento = pg_fetch_result($res, 0, 0);
    }
}

if ($secundario) {
    $res = pg_query($conexion, "SELECT nombre FROM servicios_principales WHERE id = $secundario");
    if ($res && pg_num_rows($res) > 0) {
        $nombre_servicio_principal = pg_fetch_result($res, 0, 0);
    }
}

if ($servicio) {
    $res = pg_query($conexion, "SELECT nombre FROM servicios_secundarios WHERE id = $servicio");
    if ($res && pg_num_rows($res) > 0) {
        $nombre_servicio_secundario = pg_fetch_result($res, 0, 0);
    }
}

// Logo
$logo_path = $_SERVER['DOCUMENT_ROOT'] . '/citasiceo/imagenes/logoiceo2017.png';
$logo_src = 'data:image/png;base64,' . base64_encode(file_get_contents($logo_path));

// Renderizar HTML
extract([
    'servicio' => $servicio,
    'secundario' => $secundario,
    'principal' => $principal,
    'requisitos_filtrados' => $requisitos_filtrados,
    'nombre' => $nombre,
    'telefono' => $telefono,
    'correo' => $correo,
    'folio' => $folio,
    'fecha_cita' => $fecha_cita,
    'hora_cita' => $hora_cita,
    'departamento' => $departamento,
    'nombre_servicio_principal' => $nombre_servicio_principal,
    'nombre_servicio_secundario' => $nombre_servicio_secundario,
    'logo_src' => $logo_src
]);
ob_start();
include 'acuse_template.php'; // Este archivo debe usar las variables ya definidas
$html = ob_get_clean();

// Crear PDF
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Descargar el PDF
$dompdf->stream('acuse_cita_' . preg_replace('/[^A-Za-z0-9_\-]/', '', $folio) . '.pdf', ['Attachment' => 1]);

// 🔴 Después de enviar el PDF, limpiar la sesión
unset($_SESSION['datos_cita']);
unset($_SESSION['idhora']);
unset($_SESSION['principal']);
unset($_SESSION['secundario']);
unset($_SESSION['servicio']);
?>
