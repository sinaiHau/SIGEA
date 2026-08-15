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
            background: #ffffff;
            padding: 15px;
            border-radius: 10px;
        }

.modal-content{
    border:none;
    border-radius:16px;
    box-shadow:0 10px 30px rgba(0,0,0,.15);
}

.modal-header{
    border:none;
    padding:20px 25px 10px;
}

.modal-body{
    padding:20px 25px;
}

.modal-title{
    font-size:28px;
    font-weight:500;
}

.form-label{
    font-size:13px;
    font-weight:600;
    color:#5f6368;
    margin-bottom:4px;
}

.form-control,
.form-select{
    height:48px;
    border-radius:10px;
    border:1px solid #dcdfe3;
}

#objetivo{
    height:90px;
}

.btn-primary{
    border-radius:25px;
    padding:10px 25px;
}

.btn-secondary{
    border-radius:25px;
}

.btn-danger{
    border-radius:25px;
}

.campo-card{
    background:#f8f9fa;
    padding:15px;
    border-radius:12px;
    margin-bottom:12px;
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
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content rounded-4">

      <div class="modal-header border-0">
        <h5 class="modal-title">Nuevo Evento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

     <div class="modal-body">
    <form id="formEvento">

        <!-- Nombre -->
        <div class="mb-4">
            <input
                type="text"
                class="form-control form-control-lg"
                id="nombre_evento"
                placeholder="Agregar título del evento"
                required>
        </div>

        <!-- FILA 1 -->
        <div class="row">

            <div class="col-md-6 mb-3">
                <label class="form-label">Departamento</label>
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

            <div class="col-md-6 mb-3">
                <label class="form-label">Tipo de evento</label>
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

        </div>

        <!-- FILA 2 -->
        <div class="row">

            <div class="col-md-6 mb-3">
                <label class="form-label">Tema</label>
                <select id="id_tema" class="form-select" required>

                    <option value="">Seleccione el tema</option>

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

            <div class="col-md-6 mb-3">
                <label class="form-label">Periodo</label>
                <select id="id_periodo" class="form-select" required>

                    <option value="">Seleccione el periodo</option>

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

        </div>

        <!-- FILA 3 -->
        <div class="row">

            <div class="col-md-6 mb-3">
                <label class="form-label">Institución</label>

                <select id="id_institucion" class="form-select" required>

                    <option value="">Seleccione la institución</option>

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

            <div class="col-md-6 mb-3">
                <label class="form-label">Turno</label>

                <select id="id_turno" class="form-select" required>

                    <option value="">Seleccione el turno</option>

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

        </div>

        <!-- Objetivo -->
        <div class="mb-3">
            <label class="form-label">Objetivo</label>

            <textarea
                class="form-control"
                id="objetivo"
                rows="3"
                placeholder="Describe el objetivo del evento"
                required></textarea>
        </div>

        <!-- Fechas -->
        <div class="row">

            <div class="col-md-6 mb-3">
                <label class="form-label">Inicio</label>
                <input
                    type="datetime-local"
                    class="form-control"
                    id="inicio">
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Fin</label>
                <input
                    type="datetime-local"
                    class="form-control"
                    id="fin">
            </div>

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
//ahora
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

                    const ahora = new Date();
                    const fechaLocal = 
                    ahora.getFullYear() + '-' + 
                    String(ahora.getMonth()+1).padStart(2, '0') + '-' +
                    String(ahora.getDate()).padStart(2, '0') + 'T' +
                    String(ahora.getHours()).padStart(2, '0') + ':' +
                    String(ahora.getMinutes()).padStart(2, '0');
                    inicio.value = fechaLocal;
                    fin.value = fechaLocal;

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

        selectable: true,
        selectMirror: true,

        selectable: true,
selectMirror: true,

select: function(info) {

    modo = 'crear';

    let fechaFin = new Date(info.end);

    fechaFin.setDate(fechaFin.getDate() - 1);

    let finFormateado =
        fechaFin.getFullYear() + '-' +
        String(fechaFin.getMonth()+1).padStart(2,'0') + '-' +
        String(fechaFin.getDate()).padStart(2,'0');

    inicio.value = info.startStr + 'T08:00';
    fin.value = finFormateado + 'T09:00';

    modal.show();
},
        events: 'eventos.php',

        dateClick: function(info) {
         modo = 'crear';
         idEventoActual = null;
         formEvento.reset();
         btnEliminar.style.display = 'none';
         document.querySelector('.modal-title').innerHTML =
             'Nuevo Evento';
         inicio.value = info.dateStr + 'T08:00';
         fin.value = info.dateStr + 'T09:00';
         modal.show();
         setTimeout(() => {
             nombre_evento.focus();
         }, 200);
},
       
        eventClick: function(info) {
            modo = 'editar';
            idEventoActual = info.event.id;
            btnEliminar.style.display = 'inline-block';
            document.querySelector('.modal-title').innerHTML =
             'Editar Evento';
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
