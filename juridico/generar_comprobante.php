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

// Obtener requisitos
$requisitos_filtrados = [];
if (is_numeric($secundario)) {
    $sql = "
        SELECT r.nombre
        FROM requisitos r
        JOIN requisitos_servicios_secundarios rss ON r.id = rss.requisito_id
        WHERE rss.servicio_secundario_id = $1 AND r.estatus = 't' AND rss.estatus = 't'
    ";
    $resultado = pg_query_params($conexion, $sql, [$secundario]);
    if ($resultado && pg_num_rows($resultado) > 0) {
        while ($fila = pg_fetch_assoc($resultado)) {
            $requisitos_filtrados[] = $fila['nombre'];
        }
    }
}

// Obtener nombres de servicios
$nombre_servicio_principal = 'No proporcionado';
$nombre_servicio_secundario = 'No proporcionado';
$nombre_departamento = 'No proporcionado';

if ($principal) {
    $res = pg_query_params($conexion, "SELECT nombre FROM departamentos WHERE id = $1", [$principal]);
    if ($res && pg_num_rows($res) > 0) {
        $nombre_departamento = pg_fetch_result($res, 0, 'nombre');
    }
}

if ($secundario) {
    $res = pg_query_params($conexion, "SELECT nombre FROM servicios_principales WHERE id = $1", [$secundario]);
    if ($res && pg_num_rows($res) > 0) {
        $nombre_servicio_principal = pg_fetch_result($res, 0, 'nombre');
    }
}

if ($servicio) {
    $res = pg_query_params($conexion, "SELECT nombre FROM servicios_secundarios WHERE id = $1", [$servicio]);
    if ($res && pg_num_rows($res) > 0) {
        $nombre_servicio_secundario = pg_fetch_result($res, 0, 'nombre');
    }
}

// Logo
$logo_path = $_SERVER['DOCUMENT_ROOT'] . '/citasiceo/imagenes/logoiceo2017.png';
$logo_src = 'data:image/png;base64,' . base64_encode(file_get_contents($logo_path));

// Renderizar HTML
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
