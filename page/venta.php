<?php
// Iniciar sesión
session_start();

// Verificar si la sesión está activa
if (isset($_SESSION['iduser'])) {
    include "./../includes/headermenu.php"; 
    require "./../includes/config.php"; // Asegúrate de que el archivo config.php esté bien ubicado

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accept_sale'])) {
        // Obtener los productos seleccionados y la cantidad
        $productos = json_decode($_POST['productos'], true); // Productos seleccionados
        $total_venta = $_POST['total']; // Total de la venta
        $id_usuario = $_SESSION['iduser']; // Usuario que realizó la venta

        // Iniciar la transacción
        mysqli_begin_transaction($conexion);

        try {
            // Insertar venta en la tabla Ventas
            $query_venta = "INSERT INTO ventas (id_usuario, fecha, total) VALUES ($id_usuario, NOW(), $total_venta)";
            if (mysqli_query($conexion, $query_venta)) {
                // Obtener el id de la venta insertada
                $id_venta = mysqli_insert_id($conexion);

                // Insertar los productos en la tabla DetalleVentas
                foreach ($productos as $producto) {
                    $id_producto = $producto['id'];
                    $cantidad = $producto['cantidad'];
                    $precio = $producto['precio'];

                    // Insertar el detalle de la venta
                    $query_detalle = "INSERT INTO detalleventas (id_venta, id_producto, cantidad, precio) 
                                      VALUES ($id_venta, $id_producto, $cantidad, $precio)";
                    mysqli_query($conexion, $query_detalle);

                    // Actualizar la existencia del producto
                    $query_actualizar_producto = "UPDATE productos SET existencia = existencia - $cantidad WHERE id_producto = $id_producto";
                    mysqli_query($conexion, $query_actualizar_producto);
                }

                // Insertar el corte de venta en la tabla CorteVenta
                $query_corte = "INSERT INTO corteventa (fecha, id_usuario, id_venta) 
                                VALUES (NOW(), $id_usuario, $id_venta)";
                if (mysqli_query($conexion, $query_corte)) {
                    // Confirmar la transacción
                    mysqli_commit($conexion);

                    // Redirigir a la página de ventas o mostrar mensaje de éxito
                    echo "<script>
                        document.body.insertAdjacentHTML('beforeend', '<div class=\"success-message\">Venta realizada exitosamente.</div>');
                        setTimeout(function() {
                            window.location.href = 'venta.php';
                        }, 2000);
                    </script>";
                } else {
                    // Si falla la inserción en CorteVenta
                    mysqli_rollback($conexion);
                    echo "<script>alert('Error al registrar el corte de venta');</script>";
                }
            } else {
                // Si ocurre un error, hacer rollback
                mysqli_rollback($conexion);
                echo "<script>alert('Error al realizar la venta');</script>";
            }
        } catch (Exception $e) {
            // Si ocurre un error, hacer rollback
            mysqli_rollback($conexion);
            echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
        }
    }
    ?>
    <title>Ventas</title>
    <link rel="stylesheet" type="text/css" href="./../assets/css/master.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        .sales-container {
            display: flex;
            justify-content: space-between;
            width: 100%;
            padding: 20px;
            gap: 20px;
        }

        .product-list {
            width: 45%;
        }

        .product-item {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            border-bottom: 1px solid #ddd;
            cursor: pointer;
        }

        .product-header span {
            font-weight: bold;
        }

        .selected-products {
            width: 45%;
            border: 1px solid #ddd;
            padding: 10px;
            height: 400px;
            overflow-y: auto;
        }

        .selected-product-item {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
        }

        .quantity {
            width: 50px;
        }

        .remove-btn {
            cursor: pointer;
            color: red;
        }

        .remove-btn i {
            font-size: 20px; /* Tamaño del ícono */
        }

        .total-container {
            margin-top: 20px;
            font-size: 1.2em;
            font-weight: bold;
        }

        .action-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        #search-bar {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            font-size: 1em;
        }

        #category-dropdown {
            width: 100%;
            padding: 10px;
            font-size: 1em;
            margin-bottom: 20px;
        }
    </style>

    <div class="content" id="content">
        <div class="center-content">
            <br>

            <form method="POST" action="venta.php">
                <!-- Contenedor principal para productos y productos seleccionados -->
                <div class="sales-container">
                    <!-- Lista de productos (izquierda) -->
                    <div class="product-list" id="product-list">
                        <h3>Productos disponibles</h3>

                        <!-- Barra de búsqueda de texto -->
                        <input type="text" id="search-bar" class="search-bar" placeholder="Buscar por nombre o clave.">

                        <!-- Desplegable para buscar por categoría -->
                        <select id="category-dropdown" class="category-dropdown">
                            <option value="">Seleccionar categoría</option>
                            <?php
                            // Obtener las categorías únicas desde la base de datos
                            $category_query = "SELECT DISTINCT categoria FROM productos";
                            $category_result = mysqli_query($conexion, $category_query);

                            while ($category = mysqli_fetch_assoc($category_result)) {
                                echo "<option value='{$category['categoria']}'>{$category['categoria']}</option>";
                            }
                            ?>
                        </select>

                        <!-- Encabezado de la lista de productos -->
                        <div class="product-header">
                            <span>Clave</span>
                            <span>Nombre</span>
                            <span>Precio</span>
                            <span>Existencia</span>
                            <span>Categoría</span>
                        </div>

                        <!-- Lista de productos desde la base de datos -->
                        <?php
                        $query = "SELECT id_producto, clave, nombre, existencia, precio, categoria FROM productos";
                        $result = mysqli_query($conexion, $query);

                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<div class='product-item' data-id='{$row['id_producto']}' data-clave='{$row['clave']}' data-nombre='{$row['nombre']}' data-existencia='{$row['existencia']}' data-precio='{$row['precio']}' data-categoria='{$row['categoria']}'>";
                            echo "<span>{$row['clave']}</span>";
                            echo "<span>{$row['nombre']}</span>";
                            echo "<span>\${$row['precio']}</span>";
                            echo "<span>{$row['existencia']}</span>";
                            echo "<span>{$row['categoria']}</span>"; // Mostrar la categoría
                            echo "</div>";
                        }
                        ?>
                    </div>

                    <!-- Productos seleccionados (derecha) -->
                    <div class="selected-products" id="selected-products">
                        <h3>Productos seleccionados</h3>
                        <div id="selected-items"></div>
                        <div class="total-container">
                            Total: <span id="total-price">$0.00</span>
                        </div>
                        <div class="action-buttons">
                            <button class="btn-cancel" id="btn-cancel">Cancelar</button>
                            <button class="btn-accept" type="submit" name="accept_sale">Aceptar compra</button>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="productos" id="productos"> <!-- Se agregará el JSON con los productos seleccionados -->
                <input type="hidden" name="total" id="total"> <!-- Total de la venta -->

            </form>
        </div>
    </div>

    <?php include "./../includes/footer.php"; ?>

    <script>
    let total = 0;
    let productosSeleccionados = [];

    // Filtrar productos por texto (clave o nombre)
    document.getElementById('search-bar').addEventListener('input', function() {
        const searchText = this.value.toLowerCase();

        document.querySelectorAll('.product-item').forEach(item => {
            const clave = item.getAttribute('data-clave').toLowerCase();
            const nombre = item.getAttribute('data-nombre').toLowerCase();

            if (clave.includes(searchText) || nombre.includes(searchText)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });

    document.querySelectorAll('.product-item').forEach(item => {
        item.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            const productName = this.getAttribute('data-nombre');
            const productPrice = parseFloat(this.getAttribute('data-precio'));
            const productExistence = parseInt(this.getAttribute('data-existencia'));

            // Verificar si el producto ya fue seleccionado
            const existingProduct = document.getElementById('selected-' + productId);

            if (existingProduct) {
                const quantityInput = existingProduct.querySelector('.quantity');
                const currentQuantity = parseInt(quantityInput.value);

                if (currentQuantity < productExistence) {
                    quantityInput.value = currentQuantity + 1;
                    updateTotal(quantityInput, productPrice);
                }
            } else {
                const selectedProduct = document.createElement('div');
                selectedProduct.classList.add('selected-product-item');
                selectedProduct.id = 'selected-' + productId;

                const quantityInput = document.createElement('input');
                quantityInput.type = 'number';
                quantityInput.classList.add('quantity');
                quantityInput.value = 1;
                quantityInput.min = 1;
                quantityInput.max = productExistence;
                quantityInput.addEventListener('input', function() {
                    updateTotal(quantityInput, productPrice);
                });

                // Ícono de eliminar producto (X)
                const removeBtn = document.createElement('span');
                removeBtn.classList.add('remove-btn');
                removeBtn.innerHTML = "<i class='fas fa-times'></i>";  // Ícono de "X"
                removeBtn.addEventListener('click', function() {
                    selectedProduct.remove();
                    productosSeleccionados = productosSeleccionados.filter(product => product.id !== productId);
                    updateTotal(quantityInput, productPrice);
                });

                selectedProduct.innerHTML = `
                    <span>${productName}</span>
                    <span>\$${productPrice}</span>
                `;
                selectedProduct.appendChild(quantityInput);
                selectedProduct.appendChild(removeBtn);

                document.getElementById('selected-items').appendChild(selectedProduct);

                productosSeleccionados.push({ id: productId, nombre: productName, precio: productPrice, cantidad: 1 });
                updateTotal(quantityInput, productPrice);
            }
        });
    });

    // Actualizar total
    function updateTotal(quantityInput, productPrice) {
        let newTotal = 0;
        document.querySelectorAll('.selected-product-item').forEach(item => {
            const quantity = parseInt(item.querySelector('.quantity').value);
            const price = parseFloat(item.querySelector('span:nth-child(2)').textContent.replace('$', ''));
            newTotal += quantity * price;
        });

        total = newTotal;
        document.getElementById('total-price').textContent = '$' + newTotal.toFixed(2);
        document.getElementById('total').value = newTotal.toFixed(2);

        // Actualizar los productos seleccionados
        const productosJSON = productosSeleccionados.map(product => {
            const quantity = parseInt(document.getElementById('selected-' + product.id).querySelector('.quantity').value);
            return { id: product.id, cantidad: quantity, precio: product.precio };
        });
        document.getElementById('productos').value = JSON.stringify(productosJSON);
    }

    // Filtro por categoría
    document.getElementById('category-dropdown').addEventListener('change', function() {
        const selectedCategory = this.value.toLowerCase();
        document.querySelectorAll('.product-item').forEach(item => {
            const category = item.getAttribute('data-categoria').toLowerCase();
            const searchText = document.getElementById('search-bar').value.toLowerCase();
            const clave = item.getAttribute('data-clave').toLowerCase();
            const nombre = item.getAttribute('data-nombre').toLowerCase();

            // Mostrar solo los productos que coinciden con la categoría y/o la búsqueda por texto
            if ((selectedCategory && !category.includes(selectedCategory)) || 
                (!clave.includes(searchText) && !nombre.includes(searchText))) {
                item.style.display = 'none';
            } else {
                item.style.display = '';
            }
        });
    });
</script>


<?php
} else {
    echo "<script>alert('Por favor, inicia sesión');</script>";
}
?>
