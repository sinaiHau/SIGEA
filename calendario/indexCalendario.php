<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
$base = "../";

include("conexion.php");
include("Catalogo.php");

$departamentos = new Catalogo(
    $conexion,
    "catalogo_departamento",
    "id_departamento",
    "nombre_departamento"
);

$tipos = new Catalogo(
    $conexion,
    "catalogo_tipo_evento",
    "id_tipo_evento",
    "nombre_tipo_evento"
);

$temas = new Catalogo(
    $conexion,
    "catalogo_tema",
    "id_tema",
    "tema"
);

$periodo = new Catalogo(
    $conexion,
    'catalogo_periodo',
    'id_periodo',
    'periodo'
);

$institucion = new Catalogo(
    $conexion,
    'catalogo_institucion',
    'id_institucion',
    'nombre_institucion'
);

$turno = new Catalogo(
    $conexion,
    'catalogo_turno',
    'id_turno',
    'turno'
);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Calendario de Eventos</title>

    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= $base ?>css/navbarstyle.css?v=<?php echo time(); ?>">


    <style>
        body {
            padding: 20px;
            
        }
        #calendar {
            background: #c2b7ff;
            padding: 15px;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <?php
     include("../includes/navbar.php");
     ?>
<div class="dashboard-container" >
<div class="container">
    <h2 class="text-center mb-4 text-dark">Calendario de Eventos</h2>
    <div id="calendar"></div>
</div>

<!-- MODAL CREAR EVENTO -->
<div class="modal fade" id="modalEvento" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4">

      <div class="modal-header border-0">
        <h5 class="modal-title">Nuevo Evento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <form id="formEvento">

        <div class="mb-3">
            <input type="text" class="form-control form-control-lg"
                   id="nombre_evento" placeholder="Nombre Evento" required>
          </div>

        <div class="mb-3">
    <select id="id_departamento" class="form-select" required>
        <option value="">Seleccione un departamento</option>

        <?php
        $resultado = $departamentos->listar();
        while($fila = $resultado->fetch_assoc()){
        ?>
            <option value="<?= $fila['id_departamento'] ?>">
                <?= htmlspecialchars($fila['nombre_departamento']) ?>
            </option>
        <?php } ?>
    </select>
</div>

          <div class="mb-3">
           <select id="id_tipo_evento" class="form-select" required>
            <option value="">Seleccione el tipo del evento</option>
             <?php
        $resultado = $tipos->listar();
        while($fila = $resultado->fetch_assoc()){
        ?>
            <option value="<?= $fila['id_tipo_evento'] ?>">
                <?= htmlspecialchars($fila['nombre_tipo_evento']) ?>
            </option>
        <?php } ?>
           </select>
            
          </div>

          <div class="mb-3">
            <select id="id_tema" class="form-select" required>
            <option value="">Seleccione el tema del evento</option>
             <?php
        $resultado = $temas->listar();
        while($fila = $resultado->fetch_assoc()){
        ?>
            <option value="<?= $fila['id_tema'] ?>">
                <?= htmlspecialchars($fila['tema']) ?>
            </option>
        <?php } ?>
           </select>
            
          </div>

          <div class="mb-3">
            <select id="id_periodo" class="form-select" required>
            <option value="">Seleccione el periodo del evento</option>
             <?php
        $resultado = $periodo->listar();
        while($fila = $resultado->fetch_assoc()){
        ?>
            <option value="<?= $fila['id_periodo'] ?>">
                <?= htmlspecialchars($fila['periodo']) ?>
            </option>
        <?php } ?>
           </select>
            
          </div>

          <div class="mb-3">
            <select id="id_institucion" class="form-select" required>
            <option value="">Seleccione la institución del evento</option>
             <?php
        $resultado = $institucion->listar();
        while($fila = $resultado->fetch_assoc()){
        ?>
            <option value="<?= $fila['id_institucion'] ?>">
                <?= htmlspecialchars($fila['nombre_institucion']) ?>
            </option>
        <?php } ?>
           </select>
            
          </div>

           <div class="mb-3">
            <select id="id_turno" class="form-select" required>
            <option value="">Seleccione el turno del evento</option>
             <?php
        $resultado = $turno->listar();
        while($fila = $resultado->fetch_assoc()){
        ?>
            <option value="<?= $fila['id_turno'] ?>">
                <?= htmlspecialchars($fila['turno']) ?>
            </option>
        <?php } ?>
           </select>
            
          </div>

           <div class="mb-3">
            <input type="text" class="form-control"
                   id="objetivo" placeholder="Objetivo" required>
          </div>

           <div class="mb-3">
            <label class="form-label">Inicio</label>
            <input type="datetime-local" class="form-control" id="inicio">
          </div>

          <div class="mb-3">
            <label class="form-label">Fin</label>
            <input type="datetime-local" class="form-control" id="fin">
          </div>

        </form>
      </div>

      <div class="modal-footer border-0">
        <button class="btn btn-danger me-auto" id="btnEliminar" style="display:none;">Eliminar</button>
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-primary" id="guardarEvento">Guardar</button>
    </div>

    </div>
  </div>
</div>
</div>
<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
let modo = 'crear';
let idEventoActual = null;

document.addEventListener('DOMContentLoaded', function() {

    const calendarEl = document.getElementById('calendar');
    const modalEl = document.getElementById('modalEvento');
    const modal = new bootstrap.Modal(modalEl);

    const btnEliminar = document.getElementById('btnEliminar');
    const formEvento = document.getElementById('formEvento');

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',

        /* BOTÓN CREAR */
        customButtons: {
            crearEvento: {
                text: 'Crear',
                click: function () {
                    modo = 'crear';
                    idEventoActual = null;
                    formEvento.reset();
                    btnEliminar.style.display = 'none';

                    const ahora = new Date().toISOString().slice(0,16);
                    inicio.value = ahora;
                    fin.value = ahora;

                    modal.show();
                }
            }
        },

        headerToolbar: {
            left: 'crearEvento prev,next today',
            center: 'title',
            right: 'timeGridDay,timeGridWeek,dayGridMonth'
        },
        buttonText: {
            today: 'hoy',
            month: 'mes',
            week: 'semana',
            day: 'día'
        },

        events: 'eventos.php',

       
        eventClick: function(info) {
            modo = 'editar';
            idEventoActual = info.event.id;
            btnEliminar.style.display = 'inline-block';

            fetch('eventos.php?id=' + idEventoActual)
                .then(res => res.json())
                .then(data => {
                    nombre_evento.value = data.nombre_evento;
                    id_departamento.value = data.id_departamento;
                    id_tipo_evento.value = data.id_tipo_evento;
                    id_tema.value = data.id_tema;
                    id_periodo.value = data.id_periodo;
                    id_institucion.value = data.id_institucion;
                    id_turno.value = data.id_turno;
                    objetivo.value = data.objetivo;
                    inicio.value = data.inicio.replace(' ', 'T');
                    fin.value = data.fin.replace(' ', 'T');

                    modal.show();
                });
        }
    });

    calendar.render();

    /* GUARDAR (CREAR O EDITAR) */
    guardarEvento.addEventListener('click', function () {

        const datos = new FormData();
        datos.append('nombre_evento', nombre_evento.value);
        datos.append('id_departamento', id_departamento.value);
        datos.append('id_tipo_evento', id_tipo_evento.value);
        datos.append('id_tema', id_tema.value);
        datos.append('id_periodo', id_periodo.value);
        datos.append('id_institucion', id_institucion.value);
        datos.append('id_turno', id_turno.value);
        datos.append('objetivo', objetivo.value);
        datos.append('inicio', inicio.value);
        datos.append('fin', fin.value);

        let url = 'guardar_Evento.php';
        if (modo === 'editar') {
            datos.append('id_evento', idEventoActual);
            url = 'editarEvento.php';
        }

        fetch(url, { method:'POST', body: datos })
            .then(() => {
                calendar.refetchEvents();
                modal.hide();
            });
    });

    /* ELIMINAR */
    btnEliminar.addEventListener('click', function () {

        if (!confirm('¿Deseas eliminar este evento?')) return;

        const datos = new FormData();
        datos.append('id_evento', idEventoActual);

        fetch('eliminarEvento.php', {
            method:'POST',
            body: datos
        })
        .then(() => {
            calendar.refetchEvents();
            modal.hide();
            modo = 'crear';
            idEventoActual = null;
        });
    });

});
</script>

</body>
</html>
