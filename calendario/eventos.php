<?php
header('Content-Type: application/json');
include 'conexion.php';


if (isset($_GET['id'])) {

    $id = intval($_GET['id']);

    $sql = "SELECT * FROM eventos WHERE id_evento = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    echo json_encode($resultado->fetch_assoc());
    exit;
}

$result = $conexion->query("SELECT id_evento, nombre_evento, inicio, fin FROM eventos");

$eventos = [];

while ($row = $result->fetch_assoc()) {
    $eventos[] = [
        "id"    => $row['id_evento'],
        "title" => $row['nombre_evento'],
        "start" => $row['inicio'],
        "end"   => $row['fin']
    ];
}

echo json_encode($eventos);