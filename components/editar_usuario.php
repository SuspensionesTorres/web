<?php
// Iniciar sesión
session_start();

// Incluir el archivo de configuración
include "./../includes/config.php";

// Verificar si se recibió el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener datos del formulario
    $id_usuario = $_POST['id_usuario'];
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $usuario = $_POST['usuario'];
    $rol = $_POST['rol']; // Asumimos que el rol ya es un entero (1 o 2)
    $contrasena = $_POST['contrasena']; // Obtener la nueva contraseña

    // Si la nueva contraseña está vacía, no la actualizamos
    if (empty($contrasena)) {
        $stmt = mysqli_prepare($conexion, "UPDATE usuarios SET nombre=?, apellido=?, usuario=?, rol=? WHERE id_usuario=?");
        mysqli_stmt_bind_param($stmt, "ssssi", $nombre, $apellidos, $usuario, $rol, $id_usuario);
    } else {
        // Si se proporciona una nueva contraseña, la actualizamos
        $stmt = mysqli_prepare($conexion, "UPDATE usuarios SET nombre=?, apellido=?, usuario=?, contrasena=?, rol=? WHERE id_usuario=?");
        mysqli_stmt_bind_param($stmt, "sssiii", $nombre, $apellidos, $usuario, $contrasena, $rol, $id_usuario);
    }

    // Ejecutar la consulta
    if (mysqli_stmt_execute($stmt)) {
        // Redirigir a usuarios.php con mensaje de éxito
        header("Location: ./../page/usuarios.php?mensaje=Usuario+actualizado+correctamente");
        exit();
    } else {
        // Redirigir a usuarios.php con mensaje de error
        header("Location: ./../page/usuarios.php?mensaje=Error+al+actualizar+usuario");
        exit();
    }
}

// Cerrar la conexión
mysqli_close($conexion);
?>
