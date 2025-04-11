<?php
require_once '../php/c.php'; // ← Conexión a PostgreSQL

// Obtener la fecha de la petición
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : '';

// Si la fecha no está vacía, realizar la consulta para obtener las horas ocupadas
if ($fecha) {
    // Consulta para obtener las horas ocupadas
    $sql = "SELECT hora_id FROM historial_citas WHERE fecha = TO_DATE('$fecha', 'DD-MM-YYYY')";
    $resultado = pg_query($conexion, $sql);

    // Array para almacenar las horas ocupadas
    $horas_ocupadas = [];

    if ($resultado && pg_num_rows($resultado) > 0) {
        while ($fila = pg_fetch_assoc($resultado)) {
            $horas_ocupadas[] = $fila['hora_id'];
        }
    }

    // Devolver las horas ocupadas en formato JSON
    echo json_encode($horas_ocupadas);
}
?>
