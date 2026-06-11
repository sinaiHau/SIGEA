<?php
$direccionBaseDatos_host= "localhost";
$nombreBaseDatos = "SIGEA";
$usuarioBaseDatos = "root";
$contrasenaBaseDatos = "";

$conexion= new mysqli($direccionBaseDatos_host, $usuarioBaseDatos, $contrasenaBaseDatos, $nombreBaseDatos);

if($conexion->connect_error){
    die("Conexion fallida: " . $conexion->connect_error);
}



?>