<?php
// Iniciar sesión
session_start();

// Incluir el archivo de configuración
include "./../includes/config.php";

// Verificar si se recibió el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener datos del formulario
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];
    $rol = (int)$_POST['rol']; // Asignar rol directamente desde el formulario

    // Preparar la consulta SQL
    $stmt = mysqli_prepare($conexion, "INSERT INTO usuarios (nombre, apellido, usuario, contrasena, rol) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssssi", $nombre, $apellidos, $usuario, $contrasena, $rol);

    // Ejecutar la consulta
    if (mysqli_stmt_execute($stmt)) {
        // Redirigir a usuarios.php con mensaje de éxito
        header("Location: ./../page/usuarios.php?mensaje=Usuario+agregado+correctamente");
        exit();
    } else {
        // Redirigir a usuarios.php con mensaje de error
        header("Location: ./../page/usuarios.php?mensaje=Error+al+agregar+usuario");
        exit();
    }
}

// Cerrar la conexión
mysqli_close($conexion);
?>
