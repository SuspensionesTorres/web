<?php
require './../includes/config.php'; // Conexión a la base de datos

if (isset($_GET['id'])) {
    $id_producto = $_GET['id'];

    // Iniciar una transacción
    mysqli_begin_transaction($conexion);

    try {
        // Eliminar los registros relacionados en detalleventas
        $queryDetalle = "DELETE FROM detalleventas WHERE id_producto = ?";
        $stmtDetalle = mysqli_prepare($conexion, $queryDetalle);
        mysqli_stmt_bind_param($stmtDetalle, 'i', $id_producto);
        mysqli_stmt_execute($stmtDetalle);

        // Eliminar el producto de la tabla productos
        $queryProducto = "DELETE FROM productos WHERE id_producto = ?";
        $stmtProducto = mysqli_prepare($conexion, $queryProducto);
        mysqli_stmt_bind_param($stmtProducto, 'i', $id_producto);
        mysqli_stmt_execute($stmtProducto);

        // Confirmar la transacción
        mysqli_commit($conexion);

        // Redirigir con un mensaje de éxito
        header("Location: ./../page/productos.php?mensaje=Producto+eliminado+exitosamente");
        exit();

    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        mysqli_rollback($conexion);
        header("Location: ./../page/productos.php?mensaje=Error+al+eliminar+el+producto");
        exit();
    }
} else {
    header("Location: ./../page/productos.php?mensaje=No+se+proporcionó+un+ID+de+producto");
    exit();
}

?>
