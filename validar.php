<?php
session_start();
include("includes/conexion.php");

// 1. Aquí recibes lo que viene del index ( name="contrasena" )
$usuario = $_POST["nombre_usuario"];
$password = $_POST["contrasena"]; 

// 2. Aquí usas el nombre real de la columna de tu BD ( password )
$stmt = $conexion->prepare("SELECT nombre_usuario, rol_usuario FROM usuarios WHERE nombre_usuario = ? AND password = ?");

// 3. Unimos los datos (esto se queda igual)
$stmt->bind_param("ss", $usuario, $password);

$stmt->execute();
$resultado = $stmt->get_result();

if ($fila = $resultado->fetch_assoc()) {
    $_SESSION['usuario'] = $fila['nombre_usuario'];
    $_SESSION['rol'] = $fila['rol_usuario'];
   
    header("Location: dashboard2.php");
    exit;
} else {
    header("Location: index.php?error=1"); 
    exit;
}
?>