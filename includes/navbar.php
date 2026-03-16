<?php


?>
<link rel="stylesheet" href="css/navbarstyle.css?v=<?php echo time(); ?>">

<header class="main-header">
    <div class="logo-container">
        <img src="logos/logo3.jpeg" alt="SEP" class="logo-img">
        <img src="logos/logo2.jpeg" alt="TecNM" class="logo-img">
        <img src="logos/logo4.jpeg" alt="IT Chetumal" class="logo-img">
    </div>

    <div class="navbar-container">
        <nav class="navbar">
            <a href="dashboard2.php">Inicio</a>

            <?php if ($_SESSION['rol'] == 'admin'): ?>
                <a href="">Eventos</a>
                <a href="">Reportes</a>
                 <a href="">Usuarios</a>
            <?php endif; ?>

            <?php if ($_SESSION['rol'] == 'organizador'): ?>
                <a href="">Eventos</a>
                <a href="">Reportes</a>
                <a href="">Configuracion</a>

            <?php endif; ?>

            <?php if ($_SESSION['rol'] == 'tomadorLista'): ?>
                <a href="">Eventos</a>
            <?php endif; ?>

            <div class="dropdown-mobile">
                <button class="btn-mas" onclick="toggleExtraMenu(event)"> + </button>
                <div class="dropdown-content" id="extraMenu">
                    <?php if ($_SESSION['rol'] == 'admin'): ?>
                        <a href="">Configuraciones</a>
                       
                    <?php endif; ?>
                    
                    
                    <a href="logout.php" class="btn-salir-drop" onclick="return confirmarCerrarSesion()">Cerrar sesión</a>
                </div>
            </div>
        </nav>
    </div>
</header>

<script>
function toggleExtraMenu(event) {
    // Apartir de aqui  no funciona
    event.stopPropagation(); 
    
    const menu = document.getElementById("extraMenu");
    menu.classList.toggle("show");
}

// Este código detecta clics en CUALQUIER parte de la pantalla <--tampoco funciona
window.onclick = function(event) {
    const menu = document.getElementById("extraMenu");
    
    // Si el menú está abierto Y el clic NO fue dentro del menú ni en el botón...
    if (menu.classList.contains('show')) {
        // Verificamos que el clic no haya sido dentro del cuadrito del menú
        if (!menu.contains(event.target) ) {
            menu.classList.remove('show');
        }
    }
}

function confirmarCerrarSesion() {
    return confirm("¿Estás seguro de que quieres cerrar sesión?");
}
</script>