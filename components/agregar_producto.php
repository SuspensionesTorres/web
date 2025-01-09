<?php
// Iniciar sesión
session_start();

// Verificar si la sesión está activa
if (isset($_SESSION['iduser'])) {

    // Incluir la configuración de la base de datos
    require "./../includes/config.php"; 

    // Verificar si el método de la solicitud es POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Recibir los datos del formulario
        $clave = mysqli_real_escape_string($conexion, $_POST['clave']);
        $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
        $existencia = mysqli_real_escape_string($conexion, $_POST['existencia']);
        $precio = mysqli_real_escape_string($conexion, $_POST['precio']);
        $categoria = mysqli_real_escape_string($conexion, $_POST['categoria']);  // Nuevo campo categoria

        // Validar que los campos no estén vacíos
        if (!empty($clave) && !empty($nombre) && !empty($existencia) && !empty($precio) && !empty($categoria)) {
            
            // Preparar la consulta SQL para insertar el producto
            $query = "INSERT INTO productos (clave, nombre, existencia, precio, categoria) 
                      VALUES ('$clave', '$nombre', '$existencia', '$precio', '$categoria')";

            // Ejecutar la consulta
            if (mysqli_query($conexion, $query)) {
                // Redirigir de nuevo a la página de productos si la inserción es exitosa
                header("Location: ./../page/productos.php?mensaje=Producto+agregado+exitosamente");
                exit();
            } else {
                // Mostrar mensaje de error si la consulta falla
                echo "Error: " . $query . "<br>" . mysqli_error($conexion);
            }
        } else {
            // Si los campos están vacíos, redirigir con un mensaje de error
            header("Location: productos.php?mensaje=Por+favor+complete+todos+los+campos");
            exit();
        }
    }
} else {
    // Redirigir al login si no hay sesión activa
    header("Location: index.php");
    exit();
}
?>
