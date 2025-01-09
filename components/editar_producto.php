<?php
require './../includes/config.php'; // Conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los valores del formulario
    $id = $_POST['id']; // ID del producto a actualizar
    $clave = $_POST['clave'];
    $nombre = $_POST['nombre'];
    $existencia = $_POST['existencia'];
    $precio = $_POST['precio'];
    
    // Asegurarse de que la categoría sea solo texto
    $categoria = filter_var($_POST['categoria'], FILTER_SANITIZE_STRING); // Sanitiza la entrada para asegurar que solo sea texto

    // Depuración: Verifica el valor de la categoría
    var_dump($categoria); // Esto te permitirá ver qué valor tiene la categoría en este momento.

    // Actualizar producto
    $query = "UPDATE productos SET clave = ?, nombre = ?, existencia = ?, precio = ?, categoria = ? WHERE id_producto = ?";
    $stmt = mysqli_prepare($conexion, $query);

    // Usar tipos correctos para cada parámetro: 'ssdsds'
    mysqli_stmt_bind_param($stmt, 'ssdsds', $clave, $nombre, $existencia, $precio, $categoria, $id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: ./../page/productos.php?mensaje=Producto+editado+exitosamente");
        exit();
    } else {
        header("Location: ./../page/productos.php?mensaje=Error+al+editar+el+producto");
        exit();
    }
} else {
    // Si no es una petición POST, redirigir
    header("Location: ./../page/productos.php");
    exit();
}
?>
