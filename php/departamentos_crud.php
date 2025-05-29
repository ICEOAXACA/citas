<?php
session_start();
if (!isset($_SESSION['roles']) || $_SESSION['roles'] != '1') {
    http_response_code(403);
    echo json_encode(['error' => 'No autorizado']);
    exit();
}
header('Content-Type: application/json');
require_once 'c.php';

function get_campos_departamentos($conexion) {
    $campos = [];
    $sql = "SELECT * FROM departamentos LIMIT 1";
    $result = pg_query($conexion, $sql);
    if ($result) {
        $num_fields = pg_num_fields($result);
        for ($i = 0; $i < $num_fields; $i++) {
            $fieldName = pg_field_name($result, $i);
            $campos[] = $fieldName;
        }
    }
    return $campos;
}

$action = isset($_POST['action']) ? $_POST['action'] : '';

switch ($action) {
    case 'agregar':
        $campos = get_campos_departamentos($conexion);
        $insert_campos = array_filter($campos, fn($c) => $c !== 'id');
        $values = [];
        $placeholders = [];
        foreach ($insert_campos as $idx => $campo) {
            $values[] = $_POST[$campo] ?? '';
            $placeholders[] = '$' . ($idx + 1);
        }
        $sql = "INSERT INTO departamentos (" . implode(",", $insert_campos) . ") VALUES (" . implode(",", $placeholders) . ") RETURNING *";
        $result = pg_query_params($conexion, $sql, $values);
        if ($result && $row = pg_fetch_assoc($result)) {
            echo json_encode(['success' => true, 'departamento' => $row]);
        } else {
            echo json_encode(['error' => 'Error al agregar']);
        }
        break;
    case 'editar':
        $campos = get_campos_departamentos($conexion);
        $update_campos = array_filter($campos, fn($c) => $c !== 'id');
        $id = intval($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['error' => 'ID inválido']);
            exit();
        }
        $set = [];
        $values = [];
        $idx = 1;
        foreach ($update_campos as $campo) {
            $set[] = "$campo = \\$$idx";
            $values[] = $_POST[$campo] ?? '';
            $idx++;
        }
        $values[] = $id;
        $sql = "UPDATE departamentos SET " . implode(", ", $set) . " WHERE id = \\$$idx RETURNING *";
        $result = pg_query_params($conexion, $sql, $values);
        if ($result && $row = pg_fetch_assoc($result)) {
            echo json_encode(['success' => true, 'departamento' => $row]);
        } else {
            echo json_encode(['error' => 'Error al editar']);
        }
        break;
    case 'eliminar':
        $id = intval($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['error' => 'ID inválido']);
            exit();
        }
        $sql = "DELETE FROM departamentos WHERE id = $1";
        $result = pg_query_params($conexion, $sql, [$id]);
        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => 'Error al eliminar']);
        }
        break;
    default:
        echo json_encode(['error' => 'Acción no válida']);
        break;
}
