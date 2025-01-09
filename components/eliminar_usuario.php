<?php
session_start();
include "./../includes/config.php"; // Incluye tu archivo de conexión

if (isset($_SESSION['iduser']) && isset($_GET['id_usuario'])) {
    $id_usuario = intval($_GET['id_usuario']); // Asegúrate de que es un entero

    // Eliminar el usuario
    $sql = "DELETE FROM usuarios WHERE id_usuario = $id_usuario";
    if (mysqli_query($conexion, $sql)) {
        header("Location: ./../page/usuarios.php?mensaje=Usuario eliminado exitosamente");
    } else {
        echo "Error al eliminar el usuario: " . mysqli_error($conexion);
    }
} else {
    echo "No se pudo eliminar el usuario.";
}
?>
