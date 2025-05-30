<?php
// Inicio la sesión para validar el rol del usuario
session_start();
// Solo dejo pasar a los usuarios con rol 1 (admin)
if (!isset($_SESSION['roles']) || $_SESSION['roles'] != '1') {
    http_response_code(403);
    echo json_encode(['error' => 'No autorizado']);
    exit();
}
// Devuelvo siempre JSON
header('Content-Type: application/json');
require_once 'c.php'; // Incluyo la conexión a la base de datos

// Esta función obtiene los nombres de los campos de la tabla departamentos
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

// Leo la acción que se va a realizar (agregar, editar, eliminar)
$action = isset($_POST['action']) ? $_POST['action'] : '';

switch ($action) {
    case 'agregar':
        // Agregar un nuevo departamento
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
        // Editar un departamento existente
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
        // Eliminar un departamento por id
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
        // Si la acción no es válida
        echo json_encode(['error' => 'Acción no válida']);
        break;
}
