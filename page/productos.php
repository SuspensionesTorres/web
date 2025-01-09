<?php
session_start();

// Verificar si la sesión está activa
if (isset($_SESSION['iduser'])) {

    include "./../includes/headermenu.php"; 
    require "./../includes/config.php"; 
?>

<title>Productos</title>
<link rel="stylesheet" type="text/css" href="./../assets/css/master.css">

<!-- Contenido principal -->
<div class="content" id="content">
    <div class="center-content">
        

        <div class="mt-4">
            <h3>Lista de Productos</h3>

            <div class="d-flex justify-content-end mb-3">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addProductModal">
                    <i class="bi bi-plus-circle"></i> Agregar
                </button>
            </div>

            <!-- Buscador -->
            <div class="mb-3">
                <input type="text" class="form-control" id="buscar" placeholder="Buscar producto...">
            </div>

            <div class="table-responsive" style="max-height: 550px; overflow-y: auto;">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Clave</th>
                            <th>Nombre</th>
                            <th>Existencia</th>
                            <th>Precio</th>
                            <th>Categoría</th> <!-- Columna para categoría -->
                            <th style="width: 120px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="product-table-body">
                        <?php
                        $query = "SELECT id_producto, clave, nombre, existencia, precio, categoria FROM productos";
                        $result = mysqli_query($conexion, $query);

                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>{$row['clave']}</td>";
                            echo "<td>{$row['nombre']}</td>";
                            echo "<td>{$row['existencia']}</td>";
                            echo "<td>\${$row['precio']}</td>";
                            echo "<td>{$row['categoria']}</td>"; 
                            echo "<td class='text-center'>
                                    <button class='btn btn-info btn-sm me-2' title='Editar' onclick='editProduct({$row['id_producto']}, \"{$row['clave']}\", \"{$row['nombre']}\", {$row['existencia']}, {$row['precio']}, \"{$row['categoria']}\")'>
                                        <i class='bi bi-pencil'></i>
                                    </button>
                                    <a href='./../components/eliminar_producto.php?id={$row['id_producto']}' class='btn btn-danger btn-sm' onclick='return confirm(\"¿Estás seguro de eliminar este producto?\");' title='Eliminar'>
                                    <i class='bi bi-trash'></i>
                                    </a>

                                  </td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal para agregar producto -->
<div class="modal fade" id="addProductModal" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProductModalLabel">Registrar Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="./../components/agregar_producto.php" method="POST">
                    <div class="mb-3">
                        <label for="clave" class="form-label">Clave</label>
                        <input type="text" class="form-control" id="clave" name="clave" required>
                    </div>
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="existencia" class="form-label">Existencia</label>
                        <input type="number" class="form-control" id="existencia" name="existencia" required>
                    </div>
                    <div class="mb-3">
                        <label for="precio" class="form-label">Precio</label>
                        <input type="number" step="0.01" class="form-control" id="precio" name="precio" required>
                    </div>
                    <div class="mb-3">
                        <label for="categoria" class="form-label">Categoría</label>
                        <input type="text" class="form-control" id="edit-categoria" name="categoria" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Agregar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar producto -->
<div class="modal fade" id="editProductModal" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProductModalLabel">Editar Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="./../components/editar_producto.php" method="POST">
                    <input type="hidden" id="edit-id" name="id">
                    <div class="mb-3">
                        <label for="edit-clave" class="form-label">Clave</label>
                        <input type="text" class="form-control" id="edit-clave" name="clave" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="edit-nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-existencia" class="form-label">Existencia</label>
                        <input type="number" class="form-control" id="edit-existencia" name="existencia" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-precio" class="form-label">Precio</label>
                        <input type="number" step="0.01" class="form-control" id="edit-precio" name="precio" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-categoria" class="form-label">Categoría</label>
                        <input type="text" class="form-control" id="edit-categoria" name="categoria" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include "./../includes/footer.php"; ?>

<script>
document.getElementById('buscar').addEventListener('keyup', function() {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll('#product-table-body tr');

    rows.forEach(row => {
        const clave = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
        const nombre = row.querySelector('td:nth-child(2)').textContent.toLowerCase();

        if (clave.includes(filter) || nombre.includes(filter)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
function editProduct(id, clave, nombre, existencia, precio, categoria) {
    document.getElementById('edit-id').value = id;
    document.getElementById('edit-clave').value = clave;
    document.getElementById('edit-nombre').value = nombre;
    document.getElementById('edit-existencia').value = existencia;
    document.getElementById('edit-precio').value = precio;
    // Asegúrate de que la categoría sea un string
    document.getElementById('edit-categoria').value = String(categoria);  
    const editModal = new bootstrap.Modal(document.getElementById('editProductModal'));
    editModal.show();
}


</script>

<?php
} else {
    header("Location: index.php");
    exit();
}
?>
