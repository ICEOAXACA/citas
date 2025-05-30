<?php
// Incluyo la conexión a la base de datos y devuelvo JSON
require_once 'c.php';
header('Content-Type: application/json');

// Hago la consulta para traer todos los días inhábiles ordenados por fecha descendente
$sql = "SELECT * FROM dias_inhabiles ORDER BY fecha DESC";
$result = pg_query($conexion, $sql);
$rows = [];
if ($result) {
    // Recorro los resultados y los guardo en un arreglo para devolverlos como JSON
    while ($row = pg_fetch_assoc($result)) {
        $rows[] = $row;
    }
}
// Devuelvo el resultado en formato JSON
// 'success' indica si la consulta fue exitosa y 'data' trae los días inhábiles
// Esto lo uso en el dashboard para mostrar los días que no se puede agendar
// citas en el sistema

echo json_encode([
    'success' => true,
    'data' => $rows
]);
