<?php
// Iniciar sesión
session_start();

// Establecer zona horaria
date_default_timezone_set('America/Mexico_City'); // Ajusta según tu zona horaria

// Verificar si la sesión está activa
if (isset($_SESSION['iduser'])) {
    include "./../includes/headermenu.php"; 
    require "./../includes/config.php"; // Conexión a la base de datos
?>

<title>Corte</title>
<link rel="stylesheet" type="text/css" href="./../assets/css/master.css">

<style>
    .center-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }
</style>

<!-- Contenido principal -->
<div class="content" id="content">
    <div class="center-content">
        <!-- Icono de Corte -->
        <br>

        <!-- Formulario de búsqueda -->
        <form method="POST" action="corte.php" class="row mb-4">
            <!-- Rango de fechas -->
            <div class="col-md-4 mb-3">
                <label for="fechaInicio" class="form-label">Fecha inicio</label>
                <input type="date" class="form-control" id="fechaInicio" name="fechaInicio" value="<?= isset($_POST['fechaInicio']) ? $_POST['fechaInicio'] : date('Y-m-d') ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label for="fechaFin" class="form-label">Fecha fin</label>
                <input type="date" class="form-control" id="fechaFin" name="fechaFin" value="<?= isset($_POST['fechaFin']) ? $_POST['fechaFin'] : date('Y-m-d') ?>">
            </div>
            <!-- Búsqueda por empleado -->
            <div class="col-md-4 mb-3">
                <label for="empleado" class="form-label">Empleado</label>
                <select class="form-control" id="empleado" name="empleado">
                    <option value="">-- Todos los empleados --</option>
                    <?php
                    // Consultar la lista de empleados
                    $empleadosQuery = "SELECT id_usuario, nombre, apellido FROM Usuarios";
                    $empleadosResult = mysqli_query($conexion, $empleadosQuery);
                    if ($empleadosResult && mysqli_num_rows($empleadosResult) > 0) {
                        while ($empleado = mysqli_fetch_assoc($empleadosResult)) {
                            $selected = (isset($_POST['empleado']) && $_POST['empleado'] == $empleado['id_usuario']) ? 'selected' : '';
                            echo "<option value='{$empleado['id_usuario']}' $selected>{$empleado['nombre']} {$empleado['apellido']}</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <!-- Botón de búsqueda -->
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Buscar</button>
            </div>
        </form>

        <!-- Resultados de la búsqueda -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped" style="table-layout: fixed;">
                <thead>
                    <tr>
                        <th>ID Corte</th>
                        <th>Productos</th>
                        <th>Fecha</th>
                        <th>Empleado</th>
                        <th>Total</th>
                    </tr>
                </thead>
            </table>
            <div class="table-scroll">
                <table class="table table-bordered table-striped" style="table-layout: fixed;">
                    <tbody>
                        <?php
                        // Obtener valores del formulario
                        $fechaInicio = isset($_POST['fechaInicio']) ? $_POST['fechaInicio'] : date('Y-m-d');
                        $fechaFin = isset($_POST['fechaFin']) ? $_POST['fechaFin'] : date('Y-m-d');
                        $empleado = isset($_POST['empleado']) ? $_POST['empleado'] : null;

                        // Construir la consulta base
                        $query = "SELECT corte.id_corte, corte.fecha, usuarios.nombre, usuarios.apellido, 
                                         SUM(detalle.cantidad * detalle.precio) as total, 
                                         GROUP_CONCAT(productos.nombre, ' (', detalle.cantidad, ')') as productos 
                                  FROM CorteVenta corte
                                  INNER JOIN Ventas ventas ON corte.id_venta = ventas.id_venta
                                  INNER JOIN DetalleVentas detalle ON ventas.id_venta = detalle.id_venta
                                  INNER JOIN Productos productos ON detalle.id_producto = productos.id_producto
                                  INNER JOIN Usuarios usuarios ON ventas.id_usuario = usuarios.id_usuario
                                  WHERE 1=1";

                        // Agregar filtros si existen
                        if (!empty($fechaInicio) && !empty($fechaFin)) {
                            $query .= " AND corte.fecha BETWEEN '$fechaInicio' AND '$fechaFin'";
                        }

                        if (!empty($empleado)) {
                            $query .= " AND usuarios.id_usuario = '$empleado'";
                        }

                        // Agregar agrupamiento y orden por fecha descendente
                        $query .= " GROUP BY corte.id_corte, corte.fecha, usuarios.nombre, usuarios.apellido 
                                    ORDER BY corte.fecha DESC"; // La última venta será la primera

                        // Ejecutar consulta
                        $totalGeneral = 0;
                        if ($result = mysqli_query($conexion, $query)) {
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>";
                                    echo "<td>{$row['id_corte']}</td>";

                                    // Mostrar productos y cantidades
                                    $productos = explode(',', $row['productos']);
                                    $productosList = "";
                                    foreach ($productos as $producto) {
                                        $productosList .= "<li>$producto</li>";
                                    }
                                    echo "<td><ul>$productosList</ul></td>";

                                    // Formatear fecha
                                    $fechaFormateada = date("d/m/Y", strtotime($row['fecha']));
                                    echo "<td>$fechaFormateada</td>";

                                    // Mostrar nombre y apellido del empleado
                                    echo "<td>{$row['nombre']} {$row['apellido']}</td>";

                                    // Asegúrate de que el total se muestre con formato y alineado a la derecha
                                    echo "<td class='text-end'>\$" . number_format($row['total'], 2) . "</td>";
                                    echo "</tr>";

                                    // Acumulando el total
                                    $totalGeneral += $row['total'];
                                }
                            } else {
                                echo "<tr><td colspan='5' class='text-center'>No se encontraron resultados</td></tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center'>Error en la consulta</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Total General fuera del scroll -->
        <div class="total-general">
            <strong>Total General: </strong>$<?= number_format($totalGeneral, 2) ?>
        </div>
    </div>
</div>

<?php include "./../includes/footer.php"; ?>

<?php
} else {
    header("Location: index.php");
    exit();
}
?>
