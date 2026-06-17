<?php
session_start();
$base = "";
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: Index.php');
    exit;
}
include('includes/conexion_importarAlumno.php');

// ── Pestaña activa ──
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'evento';

// ── Cargar catálogos para los filtros ──
$tipos_evento = $conexion_alumnos->query("SELECT id_tipo_evento, nombre_tipo_evento FROM catalogo_tipo_evento ORDER BY nombre_tipo_evento");
$grupos       = $conexion_alumnos->query("SELECT id_grupo, grupo FROM catalogo_grupos ORDER BY grupo");
$periodos     = $conexion_alumnos->query("SELECT id_periodo, periodo FROM catalogo_periodo ORDER BY periodo");
$eventos      = $conexion_alumnos->query("SELECT id_evento, nombre_evento FROM eventos ORDER BY nombre_evento");

// ══════════════════════════════════════════
//  LÓGICA DE CONSULTAS SEGÚN PESTAÑA
// ══════════════════════════════════════════

$resultados = null;
$titulo_reporte = '';
$columnas = [];

// ── TAB: Por Evento ──
if ($tab === 'evento' && isset($_POST['btn_evento'])) {
    $id_ev = intval($_POST['id_evento']);
    $titulo_reporte = "Asistencia del evento seleccionado";
    $columnas = ['N° Control','Nombre','Carrera','Grupo','Semestre','Hora entrada'];

    $resultados = $conexion_alumnos->query("
        SELECT a.num_control,
               CONCAT(a.nombre_alumno,' ',a.primer_ap_alum,' ',a.segundo_ap_alum) AS nombre,
               cc.nombre_carrera, cg.grupo, a.semestre_alumno, aa.hora_entrada
        FROM asistencia_alumnos aa
        JOIN alumnos a ON aa.id_alumno = a.id_alumno
        LEFT JOIN catalogo_carrera cc ON a.id_carrera = cc.id_carrera
        LEFT JOIN catalogo_grupos cg ON a.id_grupo = cg.id_grupo
        WHERE aa.id_sesion = $id_ev
        ORDER BY a.primer_ap_alum
    ");
}

// ── TAB: Por Tipo de Evento ──
if ($tab === 'tipo' && isset($_POST['btn_tipo'])) {
    $id_tipo = intval($_POST['id_tipo_evento']);
    $titulo_reporte = "Asistencia por tipo de evento";
    $columnas = ['Evento','N° Control','Nombre','Grupo','Hora entrada'];

    $resultados = $conexion_alumnos->query("
        SELECT e.nombre_evento,
               a.num_control,
               CONCAT(a.nombre_alumno,' ',a.primer_ap_alum,' ',a.segundo_ap_alum) AS nombre,
               cg.grupo, aa.hora_entrada
        FROM asistencia_alumnos aa
        JOIN alumnos a ON aa.id_alumno = a.id_alumno
        JOIN eventos e ON aa.id_sesion = e.id_evento
        LEFT JOIN catalogo_grupos cg ON a.id_grupo = cg.id_grupo
        WHERE e.id_tipo_evento = $id_tipo
        ORDER BY e.nombre_evento, a.primer_ap_alum
    ");
}

// ── TAB: Por Filtros (fecha/grupo/periodo) ──
if ($tab === 'filtros' && isset($_POST['btn_filtros'])) {
    $titulo_reporte = "Asistencia con filtros aplicados";
    $columnas = ['Evento','N° Control','Nombre','Grupo','Semestre','Hora entrada'];

    $where = "1=1";
    if (!empty($_POST['id_grupo']))   $where .= " AND a.id_grupo = ".intval($_POST['id_grupo']);
    if (!empty($_POST['id_periodo'])) $where .= " AND e.id_periodo = ".intval($_POST['id_periodo']);
    if (!empty($_POST['fecha_ini']) && !empty($_POST['fecha_fin'])) {
        $fi = $conexion_alumnos->real_escape_string($_POST['fecha_ini']);
        $ff = $conexion_alumnos->real_escape_string($_POST['fecha_fin']);
        $where .= " AND DATE(aa.hora_entrada) BETWEEN '$fi' AND '$ff'";
    }

    $resultados = $conexion_alumnos->query("
        SELECT e.nombre_evento, a.num_control,
               CONCAT(a.nombre_alumno,' ',a.primer_ap_alum,' ',a.segundo_ap_alum) AS nombre,
               cg.grupo, a.semestre_alumno, aa.hora_entrada
        FROM asistencia_alumnos aa
        JOIN alumnos a ON aa.id_alumno = a.id_alumno
        JOIN eventos e ON aa.id_sesion = e.id_evento
        LEFT JOIN catalogo_grupos cg ON a.id_grupo = cg.id_grupo
        WHERE $where
        ORDER BY e.nombre_evento, a.primer_ap_alum
    ");
}

// ── TAB: Estadísticas ──
$stats = [];
if ($tab === 'estadisticas') {
    $stats['total_eventos']    = $conexion_alumnos->query("SELECT COUNT(*) as total FROM eventos")->fetch_assoc()['total'] ?? 0;
    $stats['total_asistentes'] = $conexion_alumnos->query("SELECT COUNT(*) as total FROM asistencia_alumnos")->fetch_assoc()['total'] ?? 0;

    $stats['por_tipo'] = $conexion_alumnos->query("
        SELECT ct.nombre_tipo_evento, COUNT(aa.id_asistencia_alumno) as total
        FROM eventos e
        JOIN catalogo_tipo_evento ct ON e.id_tipo_evento = ct.id_tipo_evento
        LEFT JOIN asistencia_alumnos aa ON e.id_evento = aa.id_sesion
        GROUP BY ct.id_tipo_evento ORDER BY total DESC
    ");

    $stats['por_genero'] = $conexion_alumnos->query("
        SELECT cs.sexo, COUNT(aa.id_asistencia_alumno) as total
        FROM asistencia_alumnos aa
        JOIN alumnos a ON aa.id_alumno = a.id_alumno
        JOIN catalogo_sexo cs ON a.id_sexo = cs.id_sexo
        GROUP BY cs.id_sexo
    ");

    $stats['por_carrera'] = $conexion_alumnos->query("
        SELECT cc.nombre_carrera, COUNT(aa.id_asistencia_alumno) as total
        FROM asistencia_alumnos aa
        JOIN alumnos a ON aa.id_alumno = a.id_alumno
        JOIN catalogo_carrera cc ON a.id_carrera = cc.id_carrera
        GROUP BY cc.id_carrera ORDER BY total DESC
    ");

    $stats['por_grupo'] = $conexion_alumnos->query("
        SELECT cg.grupo, COUNT(aa.id_asistencia_alumno) as total
        FROM asistencia_alumnos aa
        JOIN alumnos a ON aa.id_alumno = a.id_alumno
        JOIN catalogo_grupos cg ON a.id_grupo = cg.id_grupo
        GROUP BY cg.id_grupo ORDER BY total DESC
    ");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Generar Reporte | SIGEA</title>
    <link rel="stylesheet" href="css/navbarstyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-section { max-width: 960px; margin: 70px 50px; font-family: sans-serif; }
        .tabs { display: flex; gap: 10px; margin-bottom: 15px; overflow-x: auto; flex-wrap: wrap; }
        .tab-link {
            text-decoration: none; color: #666; padding: 10px 15px;
            background: #eee; border-radius: 20px; font-size: 0.9em; white-space: nowrap;
        }
        .tab-link.active { background: #003366; color: white; }

        .card { background: white; padding: 24px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .card h3 { margin-top: 0; color: #003366; }

        .form-filtro { display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end; margin-bottom: 10px; }
        .form-filtro label { font-size: 13px; color: #555; display: block; margin-bottom: 4px; }
        .form-filtro select, .form-filtro input[type="date"] {
            padding: 9px 12px; border: 1px solid #ccc; border-radius: 6px;
            font-size: 14px; min-width: 180px;
        }
        .btn-consultar {
            background: #003366; color: white; border: none;
            padding: 10px 22px; border-radius: 6px; cursor: pointer;
            font-size: 14px; font-weight: 600;
        }
        .btn-consultar:hover { background: #00509e; }
        .btn-export {
            background: #27ae60; color: white; border: none;
            padding: 8px 18px; border-radius: 6px; cursor: pointer;
            font-size: 13px; margin-left: 8px; text-decoration: none;
            display: inline-block;
        }
        .btn-export-pdf { background: #c0392b; }
        .btn-export:hover { opacity: .85; }

        /* Tabla resultados */
        .tabla-reporte { width: 100%; border-collapse: collapse; font-size: 13px; margin-top: 16px; }
        .tabla-reporte th { background: #003366; color: white; padding: 10px 12px; text-align: left; }
        .tabla-reporte td { padding: 9px 12px; border-bottom: 1px solid #eee; }
        .tabla-reporte tr:nth-child(even) td { background: #f7f9fc; }
        .tabla-reporte tr:hover td { background: #eef2f7; }
        .sin-datos { text-align: center; color: #aaa; padding: 30px; }

        /* Estadísticas */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px; }
        .stat-card { background: #003366; color: white; border-radius: 10px; padding: 20px; text-align: center; }
        .stat-card .num { font-size: 36px; font-weight: 700; }
        .stat-card .lbl { font-size: 13px; opacity: .8; margin-top: 4px; }
        .stat-seccion { margin-bottom: 20px; }
        .stat-seccion h4 { color: #003366; margin-bottom: 10px; border-bottom: 2px solid #003366; padding-bottom: 6px; }
        .barra-wrap { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; font-size: 13px; }
        .barra-label { min-width: 160px; color: #444; }
        .barra-bg { flex: 1; background: #eee; border-radius: 20px; height: 18px; overflow: hidden; }
        .barra-fill { height: 100%; background: #003366; border-radius: 20px; transition: width .4s; }
        .barra-num { min-width: 30px; text-align: right; color: #555; font-weight: 600; }
    </style>
</head>
<body>
<?php include("includes/navbar.php"); ?>

<div class="admin-section">
    <?php include("includes/boton_regresar.php"); ?>

    <!-- Pestañas -->
    <div class="tabs">
        <a href="?tab=evento"       class="tab-link <?php echo $tab=='evento'       ? 'active' : ''; ?>">Por Evento</a>
        <a href="?tab=tipo"         class="tab-link <?php echo $tab=='tipo'         ? 'active' : ''; ?>">Por Tipo de Evento</a>
        <a href="?tab=filtros"      class="tab-link <?php echo $tab=='filtros'      ? 'active' : ''; ?>">Por Fecha / Grupo / Periodo</a>
        <a href="?tab=estadisticas" class="tab-link <?php echo $tab=='estadisticas' ? 'active' : ''; ?>">Estadísticas</a>
    </div>

    <!-- ══ TAB: POR EVENTO ══ -->
    <?php if ($tab === 'evento'): ?>
    <div class="card">
        <h3><i class="fas fa-calendar-check"></i> Reporte por Evento</h3>
        <form method="POST" class="form-filtro">
            <div>
                <label>Selecciona el evento</label>
                <select name="id_evento" required>
                    <option value="">-- Selecciona --</option>
                    <?php if($eventos) while($ev = $eventos->fetch_assoc()): ?>
                        <option value="<?php echo $ev['id_evento']; ?>"><?php echo htmlspecialchars($ev['nombre_evento']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" name="btn_evento" class="btn-consultar"><i class="fas fa-search"></i> Consultar</button>
        </form>

        <?php if ($resultados): ?>
            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:10px;">
                <span style="font-size:13px; color:#555;"><?php echo $resultados->num_rows; ?> registro(s) encontrados</span>
                <div>
                    <a href="exportar_reporte.php?tab=evento&formato=excel&id_evento=<?php echo intval($_POST['id_evento']??0); ?>" class="btn-export"><i class="fas fa-file-excel"></i> Excel</a>
                    <a href="exportar_reporte.php?tab=evento&formato=pdf&id_evento=<?php echo intval($_POST['id_evento']??0); ?>" class="btn-export btn-export-pdf"><i class="fas fa-file-pdf"></i> PDF</a>
                </div>
            </div>
            <table class="tabla-reporte">
                <thead><tr><?php foreach($columnas as $col): ?><th><?php echo $col; ?></th><?php endforeach; ?></tr></thead>
                <tbody>
                <?php if($resultados->num_rows > 0):
                    while($r = $resultados->fetch_assoc()): ?>
                    <tr>
                        <?php foreach($r as $v): ?><td><?php echo htmlspecialchars($v ?? '—'); ?></td><?php endforeach; ?>
                    </tr>
                <?php endwhile; else: ?>
                    <tr><td colspan="<?php echo count($columnas); ?>" class="sin-datos">No hay registros para este evento.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- ══ TAB: POR TIPO DE EVENTO ══ -->
    <?php elseif ($tab === 'tipo'): ?>
    <div class="card">
        <h3><i class="fas fa-tags"></i> Reporte por Tipo de Evento</h3>
        <form method="POST" class="form-filtro">
            <div>
                <label>Tipo de evento</label>
                <select name="id_tipo_evento" required>
                    <option value="">-- Selecciona --</option>
                    <?php if($tipos_evento) while($t = $tipos_evento->fetch_assoc()): ?>
                        <option value="<?php echo $t['id_tipo_evento']; ?>"><?php echo htmlspecialchars($t['nombre_tipo_evento']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" name="btn_tipo" class="btn-consultar"><i class="fas fa-search"></i> Consultar</button>
        </form>

        <?php if ($resultados): ?>
            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:10px;">
                <span style="font-size:13px; color:#555;"><?php echo $resultados->num_rows; ?> registro(s)</span>
                <div>
                    <a href="exportar_reporte.php?tab=tipo&formato=excel&id_tipo=<?php echo intval($_POST['id_tipo_evento']??0); ?>" class="btn-export"><i class="fas fa-file-excel"></i> Excel</a>
                    <a href="exportar_reporte.php?tab=tipo&formato=pdf&id_tipo=<?php echo intval($_POST['id_tipo_evento']??0); ?>" class="btn-export btn-export-pdf"><i class="fas fa-file-pdf"></i> PDF</a>
                </div>
            </div>
            <table class="tabla-reporte">
                <thead><tr><?php foreach($columnas as $col): ?><th><?php echo $col; ?></th><?php endforeach; ?></tr></thead>
                <tbody>
                <?php if($resultados->num_rows > 0):
                    while($r = $resultados->fetch_assoc()): ?>
                    <tr><?php foreach($r as $v): ?><td><?php echo htmlspecialchars($v ?? '—'); ?></td><?php endforeach; ?></tr>
                <?php endwhile; else: ?>
                    <tr><td colspan="<?php echo count($columnas); ?>" class="sin-datos">No hay registros.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- ══ TAB: POR FILTROS ══ -->
    <?php elseif ($tab === 'filtros'): ?>
    <div class="card">
        <h3><i class="fas fa-filter"></i> Reporte por Fecha / Grupo / Periodo</h3>
        <form method="POST" class="form-filtro">
            <div>
                <label>Grupo (opcional)</label>
                <select name="id_grupo">
                    <option value="">-- Todos --</option>
                    <?php if($grupos) while($g = $grupos->fetch_assoc()): ?>
                        <option value="<?php echo $g['id_grupo']; ?>"><?php echo htmlspecialchars($g['grupo']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div>
                <label>Periodo académico (opcional)</label>
                <select name="id_periodo">
                    <option value="">-- Todos --</option>
                    <?php if($periodos) while($p = $periodos->fetch_assoc()): ?>
                        <option value="<?php echo $p['id_periodo']; ?>"><?php echo htmlspecialchars($p['periodo']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div>
                <label>Fecha inicio</label>
                <input type="date" name="fecha_ini">
            </div>
            <div>
                <label>Fecha fin</label>
                <input type="date" name="fecha_fin">
            </div>
            <button type="submit" name="btn_filtros" class="btn-consultar"><i class="fas fa-search"></i> Consultar</button>
        </form>

        <?php if ($resultados): ?>
            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:10px;">
                <span style="font-size:13px; color:#555;"><?php echo $resultados->num_rows; ?> registro(s)</span>
                <div>
                    <a href="exportar_reporte.php?tab=filtros&formato=excel" class="btn-export"><i class="fas fa-file-excel"></i> Excel</a>
                    <a href="exportar_reporte.php?tab=filtros&formato=pdf" class="btn-export btn-export-pdf"><i class="fas fa-file-pdf"></i> PDF</a>
                </div>
            </div>
            <table class="tabla-reporte">
                <thead><tr><?php foreach($columnas as $col): ?><th><?php echo $col; ?></th><?php endforeach; ?></tr></thead>
                <tbody>
                <?php if($resultados->num_rows > 0):
                    while($r = $resultados->fetch_assoc()): ?>
                    <tr><?php foreach($r as $v): ?><td><?php echo htmlspecialchars($v ?? '—'); ?></td><?php endforeach; ?></tr>
                <?php endwhile; else: ?>
                    <tr><td colspan="<?php echo count($columnas); ?>" class="sin-datos">No hay registros con esos filtros.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- ══ TAB: ESTADÍSTICAS ══ -->
    <?php elseif ($tab === 'estadisticas'): ?>
    <div class="card">
        <h3><i class="fas fa-chart-bar"></i> Estadísticas Generales</h3>

        <!-- Totales -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="num"><?php echo $stats['total_eventos']; ?></div>
                <div class="lbl">Total de eventos</div>
            </div>
            <div class="stat-card" style="background:#27ae60;">
                <div class="num"><?php echo $stats['total_asistentes']; ?></div>
                <div class="lbl">Total de asistencias registradas</div>
            </div>
        </div>

        <!-- Por tipo de evento -->
        <div class="stat-seccion">
            <h4><i class="fas fa-tags"></i> Participación por tipo de evento</h4>
            <?php
            $max_tipo = 1;
            $rows_tipo = [];
            if ($stats['por_tipo'] && $stats['por_tipo']->num_rows > 0) {
                while($r = $stats['por_tipo']->fetch_assoc()) $rows_tipo[] = $r;
                $max_tipo = max(array_column($rows_tipo, 'total')) ?: 1;
            }
            if ($rows_tipo): foreach($rows_tipo as $r): ?>
            <div class="barra-wrap">
                <div class="barra-label"><?php echo htmlspecialchars($r['nombre_tipo_evento']); ?></div>
                <div class="barra-bg"><div class="barra-fill" style="width:<?php echo round($r['total']/$max_tipo*100); ?>%"></div></div>
                <div class="barra-num"><?php echo $r['total']; ?></div>
            </div>
            <?php endforeach; else: ?>
                <p style="color:#aaa; font-size:13px;">Sin datos aún.</p>
            <?php endif; ?>
        </div>

        <!-- Por género -->
        <div class="stat-seccion">
            <h4><i class="fas fa-venus-mars"></i> Asistentes por género</h4>
            <?php
            $rows_gen = [];
            $total_gen = 0;
            if ($stats['por_genero'] && $stats['por_genero']->num_rows > 0) {
                while($r = $stats['por_genero']->fetch_assoc()) { $rows_gen[] = $r; $total_gen += $r['total']; }
            }
            if ($rows_gen): foreach($rows_gen as $r):
                $pct = $total_gen > 0 ? round($r['total']/$total_gen*100) : 0; ?>
            <div class="barra-wrap">
                <div class="barra-label"><?php echo htmlspecialchars($r['sexo']); ?></div>
                <div class="barra-bg"><div class="barra-fill" style="width:<?php echo $pct; ?>%; background:#4a90d9;"></div></div>
                <div class="barra-num"><?php echo $r['total']; ?> (<?php echo $pct; ?>%)</div>
            </div>
            <?php endforeach; else: ?>
                <p style="color:#aaa; font-size:13px;">Sin datos aún.</p>
            <?php endif; ?>
        </div>

        <!-- Por carrera -->
        <div class="stat-seccion">
            <h4><i class="fas fa-graduation-cap"></i> Participación por carrera</h4>
            <?php
            $rows_car = [];
            if ($stats['por_carrera'] && $stats['por_carrera']->num_rows > 0)
                while($r = $stats['por_carrera']->fetch_assoc()) $rows_car[] = $r;
            $max_car = $rows_car ? max(array_column($rows_car,'total')) : 1;
            if ($rows_car): foreach($rows_car as $r): ?>
            <div class="barra-wrap">
                <div class="barra-label"><?php echo htmlspecialchars($r['nombre_carrera']); ?></div>
                <div class="barra-bg"><div class="barra-fill" style="width:<?php echo round($r['total']/$max_car*100); ?>%; background:#8e44ad;"></div></div>
                <div class="barra-num"><?php echo $r['total']; ?></div>
            </div>
            <?php endforeach; else: ?>
                <p style="color:#aaa; font-size:13px;">Sin datos aún.</p>
            <?php endif; ?>
        </div>

        <!-- Por grupo -->
        <div class="stat-seccion">
            <h4><i class="fas fa-users"></i> Distribución por grupo académico</h4>
            <?php
            $rows_grp = [];
            if ($stats['por_grupo'] && $stats['por_grupo']->num_rows > 0)
                while($r = $stats['por_grupo']->fetch_assoc()) $rows_grp[] = $r;
            $max_grp = $rows_grp ? max(array_column($rows_grp,'total')) : 1;
            if ($rows_grp): foreach($rows_grp as $r): ?>
            <div class="barra-wrap">
                <div class="barra-label"><?php echo htmlspecialchars($r['grupo']); ?></div>
                <div class="barra-bg"><div class="barra-fill" style="width:<?php echo round($r['total']/$max_grp*100); ?>%; background:#e67e22;"></div></div>
                <div class="barra-num"><?php echo $r['total']; ?></div>
            </div>
            <?php endforeach; else: ?>
                <p style="color:#aaa; font-size:13px;">Sin datos aún.</p>
            <?php endif; ?>
        </div>

        <div style="margin-top:16px;">
            <a href="exportar_reporte.php?tab=estadisticas&formato=pdf" class="btn-export btn-export-pdf"><i class="fas fa-file-pdf"></i> Exportar estadísticas PDF</a>
            <a href="exportar_reporte.php?tab=estadisticas&formato=excel" class="btn-export"><i class="fas fa-file-excel"></i> Exportar Excel</a>
        </div>
    </div>
    <?php endif; ?>
</div>
</body>
</html>