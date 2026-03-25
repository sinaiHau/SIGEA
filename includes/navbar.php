

<link rel="stylesheet" href="css/navbarstyle.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">



    <h4>SIGEA  <?php echo date("Y"); ?>- Instituto tecnologico de Chetumal- <?php echo $_SESSION['rol']; ?> IT Chetumal</h4>
<header class="main-header">
    <div class="logo-container">
        <img src="logos/logo3.jpeg" alt="SEP" class="logo-img">
        <img src="logos/logo2.jpeg" alt="TecNM" class="logo-img">
        <img src="logos/logo4.jpeg" alt="IT Chetumal" class="logo-img">
    </div>

    <div class="navbar-container">
        <nav class="navbar">
            <a href="dashboard2.php">Inicio</a>

            <div class="dropdown">
                <a href="javascript:void(0)" class="dropbtn" onclick="toggleSubMenu('menuEventos')">Eventos</a>
                <div id="menuEventos" class="dropdown-content">
                    <?php if ($_SESSION['rol'] == 'admin' || $_SESSION['rol'] == 'organizador'): ?>
                        <a href="">Crear evento</a>
                    <?php endif; ?>
                    <a href="">Calendario de eventos</a>
                </div>
            </div>

            <?php if ($_SESSION['rol'] == 'admin' || $_SESSION['rol'] == 'organizador'): ?>
                <div class="dropdown">
                    <a href="javascript:void(0)" class="dropbtn" onclick="toggleSubMenu('menuReportes')">Reportes</a>
                    <div id="menuReportes" class="dropdown-content">
                        <a href="">Generar reporte</a>
                        <a href="">Visualizar reportes</a>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($_SESSION['rol'] == 'admin' || $_SESSION['rol'] == 'organizador'): ?>
                <div class="dropdown">
                    <a href="javascript:void(0)" class="dropbtn" onclick="toggleSubMenu('menuConfigGlobal')">Configuracion</a>
                    <div id="menuConfigGlobal" class="dropdown-content">
                        <?php if ($_SESSION['rol'] == 'admin'): ?>
                            <a href=""> Usuarios</a>
                            <a href=""> Institucion</a>
                            <a href="">Opcion3</a>
                            <a href="">Opcion4</a>
                        <?php else: ?>
                            <a href=""> Perfil</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <a href="logout.php" class="nav-desktop" onclick="return confirmarCerrarSesion()">Cerrar sesión</a>

            <div class="dropdown-mobile nav-mobile">
              
                   <a href="logout.php" onclick="return confirmarCerrarSesion()" title="Cerrar Sesión">
            <i class="fas fa-sign-out-alt"></i>
        </a>
                </div>
            </div>
        </nav>
    </div>
</header>

<script>
function toggleExtraMenu(event) {
    event.stopPropagation();
    const menu = document.getElementById("extraMenu");
    menu.classList.toggle("show");
}

function toggleSubMenu(menuId) {
    const target = document.getElementById(menuId);
    const isOpen = target.classList.contains("show");
    closeAllSubMenus();
    if (!isOpen) target.classList.add("show");
}

function closeAllSubMenus() {
    const menus = document.getElementsByClassName("dropdown-content");
    for (let i = 0; i < menus.length; i++) {
        menus[i].classList.remove("show");
    }
}

window.onclick = function(event) {
    // Si el clic NO fue en un botón de menú (dropbtn)
    if (!event.target.closest('.dropbtn') && !event.target.closest('.btn-mas')) {
        closeAllSubMenus();
        
        const extra = document.getElementById("extraMenu");
        if(extra) extra.classList.remove("show");
    }
}

function confirmarCerrarSesion() {
    return confirm("¿Estás seguro de que quieres cerrar sesión?");
}
</script>