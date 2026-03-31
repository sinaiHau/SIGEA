<?php
session_start();
// Llamamos a la bd alumnos
include("includes/conexion_importarAlumno.php");

// Navbar
include("includes/navbar.php");

$mensaje = "";

// Acción para "limpiar" el semestre anterior
if (isset($_POST['cerrar_ciclo'])) {
    $sql_reset = "UPDATE alumnos SET estatus = 'Inactivo'";
    if ($conexion_alumnos->query($sql_reset)) {
        $mensaje = "Ciclo escolar cerrado. Todos los alumnos pasaron a 'Inactivo'.";
    }
}

if (isset($_POST['importar'])) {
    if ($_FILES['archivo_csv']['size'] > 0) {
        $archivo = $_FILES['archivo_csv']['tmp_name'];
        $gestor = fopen($archivo, "r");
        fgetcsv($gestor, 1000, ","); // Brincamos encabezados

        $contador = 0;
        while (($datos = fgetcsv($gestor, 1000, ",")) !== FALSE) {
            $nc   = $conexion_alumnos->real_escape_string($datos[0]);
            $nom  = $conexion_alumnos->real_escape_string($datos[1]);
            $grp  = $conexion_alumnos->real_escape_string($datos[2]);
            $sem  = $conexion_alumnos->real_escape_string($datos[3]);

            $sql = "REPLACE INTO alumnos (numero_control, nombre, grupo, semestre, estatus) 
                    VALUES ('$nc', '$nom', '$grp', '$sem', 'Activo')";

            if ($conexion_alumnos->query($sql)) {
                $contador++;
            }
        }
        fclose($gestor);
        $mensaje = " Se actualizaron $contador alumnos en el sistema SIGEA.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/importar_alumnos.css?v=<?php echo time(); ?>">
    <title>Importar Alumnos | SIGEA</title>
</head>
<body>
    <div class="container-admin">
    
        <div class="card alert-warning">
            <h4>Zona de Control Semestral</h4>
            <div style="padding: 15px 25px; background-color: #fff9e6; border-left: 4px solid #856404; margin: 10px 25px;">
                <p style="margin: 0; font-size: 14px; color: #856404;">
                    <strong>¡Atención!</strong> Al cerrar el ciclo, todos los alumnos actuales pasarán a <b>Inactivo</b>. 
                    Esto limpiará las listas actuales para que puedas subir los nuevos grupos.
                </p>
            </div>

            <form action="" method="POST" onsubmit="return confirmarCierre();">
                <button type="submit" name="cerrar_ciclo" class="btn-danger">
                    Cerrar Ciclo Escolar Actual
                </button>
            </form>
        </div>

        <div class="card">
            <h2>Actualización de Alumnos</h2>
            <p style="padding-left: 25px; color: #666;">Sube el archivo <b>.csv</b> oficial para reactivar y actualizar la lista de alumnos inscritos.</p>
            
            <?php if ($mensaje) echo "<div style='margin: 0 25px 20px; padding: 10px; background: #e7f3ff; color: #0d6efd; border-radius: 5px; font-weight: bold;'>$mensaje</div>"; ?>

            <form action="" method="POST" enctype="multipart/form-data">
                <input type="file" name="archivo_csv" accept=".csv" required>
                <button type="submit" name="importar" class="btn-primary">
                    Cargar a la Base de Datos
                </button>
            </form>
        </div>

    </div>

    <script>
    function confirmarCierre() {
        return confirm("¿Estás segura de que quieres cerrar el ciclo actual?\n\nEsta acción marcará a todos los alumnos como 'Inactivos'. No aparecerán en las listas de asistencia hasta que subas el nuevo archivo Excel.");
    }
    </script>

</body>
</html>