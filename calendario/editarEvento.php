<?php
header('Content-Type: application/json');
include 'conexion.php';

$id_evento     = $_POST['id_evento'];
$nombre_evento = $_POST['nombre_evento'];
$id_departamento = $_POST['id_departamento'];
$id_tipo_evento  = $_POST['id_tipo_evento'];
$id_tema         = $_POST['id_tema'];
$id_periodo      = $_POST['id_periodo'];
$id_institucion  = $_POST['id_institucion'];
$id_turno        = $_POST['id_turno'];
$objetivo        = $_POST['objetivo'];
$inicio          = $_POST['inicio'];
$fin             = $_POST['fin'];

$sql = "UPDATE eventos SET
    nombre_evento = ?,
    id_departamento = ?,
    id_tipo_evento = ?,
    id_tema = ?,
    id_periodo = ?,
    id_institucion = ?,
    id_turno = ?,
    objetivo = ?,
    inicio = ?,
    fin = ?
WHERE id_evento = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param(
    "siiiiissssi",
    $nombre_evento,
    $id_departamento,
    $id_tipo_evento,
    $id_tema,
    $id_periodo,
    $id_institucion,
    $id_turno,
    $objetivo,
    $inicio,
    $fin,
    $id_evento
);

$stmt->execute();

echo json_encode(["status" => "ok"]);