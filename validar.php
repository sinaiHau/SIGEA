<?php
session_start();
include("includes/conexion.php");

$usuario = $_POST["nombre_usuario"];
$contrasena = $_POST["contrasena"];


$stmt = $conexion->prepare("SELECT NOMBRE_USUARIO, ROL FROM usuarios WHERE nombre_usuario = ? AND contrasena = ?");

// Unimos los datos 
$stmt->bind_param("ss", $usuario, $contrasena);


$stmt->execute();
$resultado = $stmt->get_result();

if ($fila = $resultado->fetch_assoc()) {
    // Si entró aquí, es que el usuario y contraseña son correctos
    $_SESSION['usuario'] = $fila['NOMBRE_USUARIO'];
    $_SESSION['rol'] = $fila['ROL'];
   

    // Mandamos a TODOS al mismo archivo
    header("Location: dashboard2.php");
    exit;
} else {
    // Si no coinciden, al login con error
    header("Location: index.php?error=1"); 
    exit;
}
?>
