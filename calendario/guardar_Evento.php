<?php
header('Content-Type: application/json');
include 'conexion.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$titulo        = $_POST['nombre_evento'];
$departamento = $_POST['id_departamento'];
$evento        = $_POST['id_tipo_evento'];
$tema          = $_POST['id_tema'];
$periodo       = $_POST['id_periodo'];
$institucion   = $_POST['id_institucion'];
$turno         = $_POST['id_turno'];
$objetivo      = $_POST['objetivo'];
$inicio        = $_POST['inicio'];
$fin           = $_POST['fin'];

$sql = "INSERT INTO eventos
(nombre_evento, id_departamento, id_tipo_evento, id_tema, id_periodo, id_institucion, id_turno, objetivo, inicio, fin)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conexion->prepare($sql);

$stmt->bind_param(
    "siiiiissss",
    $titulo,
    $departamento,
    $evento,
    $tema,
    $periodo,
    $institucion,
    $turno,
    $objetivo,
    $inicio,
    $fin
);

$stmt->execute();

echo json_encode([
    "status" => "ok",
    "id_evento" => $conexion->insert_id
]);
