<?php
// Incluyo la conexión a la base de datos y devuelvo JSON
require_once 'c.php';
header('Content-Type: application/json');

// Obtengo la fecha desde GET o uso la fecha de hoy por defecto
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');
$fecha = substr($fecha, 0, 10); // Por seguridad, solo tomo los primeros 10 caracteres

// Solo muestro registros del departamento jurídico (id=1) y la fecha seleccionada
$sql = "SELECT * FROM historial_citas WHERE departamento_id = 1 AND fecha = $1 ORDER BY id DESC";
$result = pg_query_params($conexion, $sql, [$fecha]);
$rows = [];
if ($result) {
    // Recorro los resultados y los guardo en un arreglo para devolverlos como JSON
    while ($row = pg_fetch_assoc($result)) {
        $rows[] = $row;
    }
}
// Devuelvo el resultado en formato JSON
// 'success' indica si la consulta fue exitosa y 'data' trae el historial de citas
// Esto lo uso para mostrar el historial de citas del jurídico en el dashboard

echo json_encode([
    'success' => true,
    'data' => $rows
]);
