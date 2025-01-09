<?php
$servidor = "sql307.infinityfree.com"; // Hostname que aparece en la imagen
$usuariobd = "if0_38075188"; // MySQL Username
$contrasenabd = "oBqGiAhaGUnInd "; // MySQL Password (sustituye con la contraseña real)
$nombrebd = "if0_38075188_suspensiones"; // Nombre de la base de datos

// Establecer la conexión de mysqli
$conexion = mysqli_connect($servidor, $usuariobd, $contrasenabd, $nombrebd);

// Verificar la conexión
if (!$conexion) {
    die("Conexión fallida: " . mysqli_connect_error());
} else {
    echo "Conexión exitosa a la base de datos.";
}
?>
