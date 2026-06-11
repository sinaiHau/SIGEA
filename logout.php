<?php
//  acceder a la sesion 
session_start();

//  Borrar valores de la sesion 
session_unset();
session_destroy();

// Al login 
header("Location: Index.php");
exit();
?>
