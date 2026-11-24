<?php
$servidor = 'localhost';
$baseDatos = 'reportes_escolares';
$usuario = 'root';
$contrasena = '';
$puerto = 3306;

try {
    $conexion = new PDO(
        "mysql:host=$servidor;port=$puerto;dbname=$baseDatos;charset=utf8mb4",
        $usuario,
        $contrasena
    );
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $conexion->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    http_response_code(500);
    exit('No se pudo conectar a la base de datos. Revisa MySQL y la configuración de conexión.');
}

function obtenerReportes() {
    global $conexion;
    $consulta = $conexion->query('SELECT id, nombre, tipo_usuario, area, problema, descripcion, estado, fecha FROM reportes ORDER BY orden ASC');
    return $consulta->fetchAll();
}

function registrarReporte($reporte) {
    global $conexion;
    try {
        $consulta = $conexion->prepare('INSERT INTO reportes (id, nombre, tipo_usuario, area, problema, descripcion, estado, fecha) VALUES (:id, :nombre, :tipo_usuario, :area, :problema, :descripcion, :estado, :fecha)');
        return $consulta->execute($reporte);
    } catch (PDOException $e) {
        return false;
    }
}

function actualizarReporte($id, $estado) {
    global $conexion;
    try {
        $consulta = $conexion->prepare('UPDATE reportes SET estado = ? WHERE id = ?');
        return $consulta->execute([$estado, $id]);
    } catch (PDOException $e) {
        return false;
    }
}

function eliminarReporte($id) {
    global $conexion;
    try {
        $consulta = $conexion->prepare('DELETE FROM reportes WHERE id = ?');
        return $consulta->execute([$id]);
    } catch (PDOException $e) {
        return false;
    }
}
