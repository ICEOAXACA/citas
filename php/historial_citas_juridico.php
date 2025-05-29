<?php
require_once 'c.php';
header('Content-Type: application/json');

$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');
$fecha = substr($fecha, 0, 10); // Seguridad

// Solo mostrar registros con departamento_id = 1 y la fecha seleccionada
$sql = "SELECT * FROM historial_citas WHERE departamento_id = 1 AND fecha = $1 ORDER BY id DESC";
$result = pg_query_params($conexion, $sql, [$fecha]);
$rows = [];
if ($result) {
    while ($row = pg_fetch_assoc($result)) {
        $rows[] = $row;
    }
}
echo json_encode([
    'success' => true,
    'data' => $rows
]);
