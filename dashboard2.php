<?php
session_start(); 

// Si no hay usuario al login 
if (!isset($_SESSION['rol'])) {
    header("Location: Index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/dashboardstyle.css">

</head>
<body>
<?php
// menu principal 
include('includes/navbar.php'); 
?>
<div class="dashboard-container">
    <!-- Imagen enorme del tec-->
   
    <div class="hero-image-container">
            <img src="logos/foto_it.jpeg" alt="Instituto Tecnológico de Chetumal" class="hero-img">
        </div>
</div>

</body>
</html>
