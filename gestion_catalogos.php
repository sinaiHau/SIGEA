<?php
session_start();
include('includes/conexion.php');
include('clases/Catalogo.php');

// Configuración de tus tablas (puedes agregar todas las de tu diagrama aquí)
$catalogos = [
    'grupos'      => ['tabla' => 'catalogo_grupos', 'id' => 'id_grupo', 'nom' => 'nombre_grupo', 'titulo' => 'Grupos'],
    'lugares'     => ['tabla' => 'catalogo_lugares_evento', 'id' => 'id_lugar', 'nom' => 'nombre_lugar', 'titulo' => 'Lugares'],
    'institucion' => ['tabla' => 'catalogo_institucion', 'id' => 'id_institucion', 'nom' => 'nombre_institucion', 'titulo' => 'Instituciones'],
    'carreras'    => ['tabla' => 'catalogo_carrera', 'id' => 'id_carrera', 'nom' => 'nombre_carrera', 'titulo' => 'Carreras'],
    'deptos'      => ['tabla' => 'catalogo_departamento', 'id' => 'id_departamento', 'nom' => 'nombre_departamento', 'titulo' => 'Departamentos'],
    'temas'       => ['tabla' => 'catalogo_tema', 'id' => 'id_tema', 'nom' => 'tema', 'titulo' => 'Temas de Eventos'],
    'tipos_evento' => ['tabla' => 'catalogo_tipo_evento', 'id' => 'id_tipo_evento', 'nom' => 'nombre_tipo_evento', 'titulo' => 'Tipos de Evento'],
];

$id_cat = isset($_GET['id']) ? $_GET['id'] : 'institucion';
$info = $catalogos[$id_cat];

$catalogo = new Catalogo($conexion, $info['tabla'], $info['id'], $info['nom']);

// Lógica para Guardar
if (isset($_POST['btn_guardar'])) {
    $nuevo_valor = trim($_POST['txt_nombre']); // trim quita espacios accidentales al inicio/final

    if ($catalogo->existe($nuevo_valor)) {
        // Si ya existe, redirigimos con un mensaje de error (msj=3)
        header("Location: gestion_catalogos.php?id=$id_cat&msj=3");
        exit;
    } else {
        // Si no existe, procedemos a guardar
        if ($catalogo->agregar($nuevo_valor)) {
            header("Location: gestion_catalogos.php?id=$id_cat&msj=1");
            exit;
        }
    }
}

// Lógica para Eliminar
if (isset($_GET['del'])) {
    $catalogo->eliminar($_GET['del']);
    header("Location: gestion_catalogos.php?id=$id_cat&msj=2");
    exit;
}

$registros = $catalogo->listar();
?>


 
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar <?php echo $info['titulo']; ?></title>

    
    <link rel="stylesheet" href="css/navbarstyle.css">
    <style>
        .admin-section { max-width: 800px; margin: 70px 50px; font-family: sans-serif; }
        .card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .input-group { display: flex; gap: 10px; margin-bottom: 20px; }
        input[type="text"] { flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
        button { background: #003366; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; }
        
        .lista-items { list-style: none; padding: 0; }
        .lista-items li { 
            display: flex; 
            justify-content: space-between; 
            padding: 12px; 
            border-bottom: 1px solid #eee; 
        }
        .lista-items li:hover { background: #f9f9f9; }
        .btn-delete { color: #d9534f; text-decoration: none; font-size: 0.9em; }
        
        .tabs { display: flex; gap: 10px; margin-bottom: 15px; overflow-x: auto; }
        .tab-link { 
            text-decoration: none; color: #666; padding: 10px 10px; 
            background: #eee; border-radius: 20px; font-size: 0.9em; 
        }
        .tab-link.active { background: #003366; color: white; padding: 10px 15px; }
    </style>
</head>
<body>

<?php include("includes/navbar.php"); ?>

<div class="admin-section">
    <?php include("includes/boton_regresar.php"); ?>
    <div class="tabs">
        <?php foreach ($catalogos as $key => $val): ?>
            <a href="?id=<?php echo $key; ?>" class="tab-link <?php echo ($id_cat == $key) ? 'active' : ''; ?>">
                <?php echo $val['titulo']; ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="card">
        <h3>Gestionar <?php echo $info['titulo']; ?></h3>
        <?php if(isset($_GET['msj'])): ?>
    <div style="padding: 10px; margin-bottom: 15px; border-radius: 5px; font-size: 0.9em; 
        <?php 
            if($_GET['msj'] == '3') echo "background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;";
            elseif($_GET['msj'] == '1') echo "background: #d4edda; color: #155724; border: 1px solid #c3e6cb;";
            else echo "background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb;";
        ?>">
        <?php 
            if($_GET['msj'] == '3') echo "<strong>¡Atención!</strong> Ese registro ya existe en el catálogo.";
            elseif($_GET['msj'] == '1') echo "Registro guardado correctamente.";
            elseif($_GET['msj'] == '2') echo "Registro eliminado.";
        ?>
    </div>
<?php endif; ?>
        <form method="POST" class="input-group">
            <input type="text" name="txt_nombre" required placeholder="Nombre de la nueva <?php echo $id_cat; ?>...">
            <button type="submit" name="btn_guardar">Agregar</button>
        </form>

        <ul class="lista-items">
            <?php if($registros->num_rows > 0): ?>
                <?php while($fila = mysqli_fetch_array($registros)): ?>
                <li>
                    <span><?php echo $fila[$info['nom']]; ?></span>
                    <a href="?id=<?php echo $id_cat; ?>&del=<?php echo $fila[$info['id']]; ?>" 
                       class="btn-delete" onclick="return confirm('¿Eliminar este registro?')">
                       <i class="fas fa-trash"></i> 
                    </a>
                </li>
                <?php endwhile; ?>
            <?php else: ?>
                <li style="color: gray;">No hay registros todavía.</li>
            <?php endif; ?>
        </ul>
    </div>
</div>

</body>
</html>
