<?php
$direccionBaseDatos= "localhost";
$nombreBD = "Alumnos";
$usuarioBD = "root";
$passBD = "";

$conexion_alumnos= new mysqli($direccionBaseDatos, $usuarioBD, $passBD, $nombreBD);

if($conexion_alumnos->connect_error){
    die("Conexion fallida: " . $conexion_alumnos->connect_error);
}



?>