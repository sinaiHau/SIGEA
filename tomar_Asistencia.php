<?php
session_start();
include("includes/conexion_importarAlumno.php");
include("includes/navbar.php");

// Consultar grupos disponibles (solo con alumnos activos)
$sql_grupos = "SELECT DISTINCT grupo FROM alumnos WHERE estatus = 'Activo' ORDER BY grupo ASC";
$res_grupos = $conexion_alumnos->query($sql_grupos);

$grupo_sel = isset($_POST['grupo']) ? $_POST['grupo'] : '';
$alumnos = [];

// Traer grupo seleccionado y su lista de alumnos activos
if ($grupo_sel) {
    $grupo_escapado = $conexion_alumnos->real_escape_string($grupo_sel);
    $sql_lista = "SELECT numero_control, nombre FROM alumnos 
                  WHERE grupo = '$grupo_escapado' AND estatus = 'Activo' 
                  ORDER BY nombre ASC";
    $alumnos = $conexion_alumnos->query($sql_lista);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pasar Lista | SIGEA</title>
    <link rel="stylesheet" href="css/asistencia_style.css?v=<?php echo time(); ?>">
</head>
<body>

<div class="main-container">
    <h2 class="page-title">Asistencia por Grupo</h2>

    <div class="card-sigea">
        <div class="card-header">Seleccionar el Grupo para el Evento</div>
        <div class="card-body">
            <form action="" method="POST" class="form-filtro">
                <select name="grupo" required>
                    <option value="">-- Seleccione un grupo --</option>
                    <?php while($g = $res_grupos->fetch_assoc()): ?>
                        <option value="<?php echo $g['grupo']; ?>" <?php if($grupo_sel == $g['grupo']) echo 'selected'; ?>>
                            Grupo: <?php echo $g['grupo']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" class="btn-sigea">Cargar Lista</button>
            </form>
        </div>
    </div>

    <?php if ($grupo_sel): ?>
    <div class="card-sigea">
        <div class="card-header">Lista de Alumnos: <?php echo $grupo_sel; ?></div>
        <div class="card-body no-padding">
            <table class="tabla-sigea">
                <thead>
                    <tr>
                        <th>N° Control</th>
                        <th>Nombre del Alumno</th>
                        <th style="text-align: center;">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($alumnos->num_rows > 0): ?>
                        <?php while($row = $alumnos->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['numero_control']; ?></td>
                            <td><?php echo $row['nombre']; ?></td>
                            <td style="text-align: center;">
                                <button class="btn-check">✔️ Registrar</button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="3" style="text-align:center; padding:20px;">No hay alumnos activos en este grupo.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>

</body>
</html>