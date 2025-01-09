<?php
session_start();
require './../includes/config.php'; // Asegúrate de incluir tu archivo de conexión

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contrasena = $_POST['contrasena_verificar'];
    $id_usuario = $_POST['id_usuario'];

    // Consulta para obtener la contraseña hash de la base de datos
    $sql = "SELECT contrasena FROM usuarios WHERE id_usuario = ?";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $_SESSION['iduser']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $contrasenaGuardada);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    // Verifica la contraseña ingresada
    if (password_verify($contrasena, $contrasenaGuardada)) {
        // Si la contraseña es correcta, redirige a la página anterior y muestra la contraseña
        header("Location: ./../page/usuarios.php?mensaje=Contraseña+correcta&contrasena_mostrar=" . urlencode($row['contrasena']));
        exit();
    } else {
        // Contraseña incorrecta, redirige con un mensaje de error
        header("Location: ./../page/usuarios.php?mensaje=Contraseña+incorrecta");
        exit();
    }
}
?>
