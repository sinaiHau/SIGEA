<?php
header('Content-Type: application/json');
include 'conexion.php';

$id_evento = $_POST['id_evento'];

$sql = "DELETE FROM eventos WHERE id_evento = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_evento);
$stmt->execute();

echo json_encode(["status" => "ok"]);