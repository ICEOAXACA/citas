<?php
require_once 'c.php';
header('Content-Type: application/json');

$sql = "SELECT * FROM dias_inhabiles ORDER BY fecha DESC";
$result = pg_query($conexion, $sql);
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
