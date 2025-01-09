<?php
// Iniciar sesión
session_start();

// Verificar si la sesión está activa
if (isset($_SESSION['iduser'])) {
    include "./../includes/headermenu.php"; 
    include "./../includes/config.php"; // Asegúrate de incluir tu archivo de conexión

    // Consulta para obtener los usuarios
    $sql = "SELECT id_usuario, nombre, apellido, usuario, contrasena, rol FROM usuarios";
    $result = mysqli_query($conexion, $sql);

    if (!$result) {
        die("Error en la consulta: " . mysqli_error($conexion));
    }

    // Mostrar mensaje de éxito o error
    if (isset($_GET['mensaje'])) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: '" . htmlspecialchars($_GET['mensaje']) . "',
                showConfirmButton: false,
                timer: 3000
            });
        </script>";
    }
?>

<title>Usuarios</title>
<link rel="stylesheet" type="text/css" href="./../assets/css/master.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

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
       


        <!-- Formulario de registro de usuario -->
        <div class="row">
            <div class="col-12">
                <br>
                <h3>Registrar Usuario</h3>
                <form action="./../components/agregar_usuario.php" method="POST">
                    <div class="row mb-3">
                        <div class="col-12 col-md-4">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="apellidos" class="form-label">Apellidos</label>
                            <input type="text" class="form-control" id="apellidos" name="apellidos" required>
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="usuario" class="form-label">Usuario</label>
                            <input type="text" class="form-control" id="usuario" name="usuario" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12 col-md-6">
                            <label for="contrasena" class="form-label">Contraseña</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="contrasena" name="contrasena" required>
                                <button type="button" class="btn btn-outline-secondary" id="togglePasswordRegistro">
                                    <i class="bi bi-eye-slash" id="iconRegistro"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="rol" class="form-label">Rol</label>
                            <select class="form-select" id="rol" name="rol" required>
                                <option value="1">Administrador</option>
                                <option value="2">Empleado</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        <button type="submit" class="btn btn-primary">Agregar Usuario</button>
                    </div>
                </form>
            </div>
        </div>

        <br>

        <!-- Tabla de usuarios -->
        <h3>Lista de Usuarios</h3>
        <div class="row mt-4">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID Usuario</th>
                                <th>Nombre</th>
                                <th>Apellidos</th>
                                <th>Usuario</th>
                                <th>Contraseña</th>
                                <th>Rol</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Recorrer los resultados y generar filas en la tabla
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['id_usuario']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['apellido']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['usuario']) . "</td>";

                                // Mostrar la contraseña oculta con el ícono
                                echo "<td>
                                        <div class='input-group'>
                                            <input type='password' class='form-control' id='contrasena" . htmlspecialchars($row['id_usuario']) . "' value='" . htmlspecialchars($row['contrasena']) . "' disabled>
                                            <button type='button' class='btn btn-outline-secondary' id='togglePassword" . htmlspecialchars($row['id_usuario']) . "'>
                                                <i class='bi bi-eye-slash' id='icon" . htmlspecialchars($row['id_usuario']) . "'></i>
                                            </button>
                                        </div>
                                      </td>";

                                echo "<td>" . ($row['rol'] == 1 ? 'Administrador' : 'Empleado') . "</td>";
                                echo "<td>
                                    <a href='#' class='btn btn-info btn-sm me-2' data-bs-toggle='modal' data-bs-target='#editarModal" . htmlspecialchars($row['id_usuario']) . "'>
                                    <i class='bi bi-pencil'></i>
                                    </a>
                                    <a href='./../components/eliminar_usuario.php?id_usuario=" . htmlspecialchars($row['id_usuario']) . "' class='btn btn-danger btn-sm' onclick=\"return confirm('¿Estás seguro de que deseas eliminar este usuario?');\">
                                    <i class='bi bi-trash'></i>
                                    </a>
                                </td>";
                                echo "</tr>";

                                // Modal para editar usuario con backdrop y teclado deshabilitado
                                echo "
                                <div class='modal fade' id='editarModal" . htmlspecialchars($row['id_usuario']) . "' tabindex='-1' aria-labelledby='editarModalLabel' aria-hidden='true' data-bs-backdrop='static' data-bs-keyboard='false'>
                                    <div class='modal-dialog'>
                                        <div class='modal-content'>
                                            <div class='modal-header'>
                                                <h5 class='modal-title' id='editarModalLabel'>Editar Usuario</h5>
                                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                            </div>
                                            <div class='modal-body'>
                                                <form action='./../components/editar_usuario.php' method='POST'>
                                                    <input type='hidden' name='id_usuario' value='" . htmlspecialchars($row['id_usuario']) . "'>
                                                    <div class='mb-3'>
                                                        <label for='nombre' class='form-label'>Nombre</label>
                                                        <input type='text' class='form-control' id='nombre' name='nombre' value='" . htmlspecialchars($row['nombre']) . "' required>
                                                    </div>
                                                    <div class='mb-3'>
                                                        <label for='apellidos' class='form-label'>Apellidos</label>
                                                        <input type='text' class='form-control' id='apellidos' name='apellidos' value='" . htmlspecialchars($row['apellido']) . "' required>
                                                    </div>
                                                    <div class='mb-3'>
                                                        <label for='usuario' class='form-label'>Usuario</label>
                                                        <input type='text' class='form-control' id='usuario' name='usuario' value='" . htmlspecialchars($row['usuario']) . "' required>
                                                    </div>
                                                    <div class='mb-3'>
                                                        <label for='contrasena' class='form-label'>Contraseña</label>
                                                        <div class='input-group'>
                                                            <input type='password' class='form-control' id='contrasena_editar" . htmlspecialchars($row['id_usuario']) . "' name='contrasena'>
                                                            <button type='button' class='btn btn-outline-secondary' id='togglePasswordEditar" . htmlspecialchars($row['id_usuario']) . "'>
                                                                <i class='bi bi-eye-slash' id='iconEditar" . htmlspecialchars($row['id_usuario']) . "'></i>
                                                            </button>
                                                        </div>
                                                        <small class='text-muted'>Deja en blanco para no cambiar la contraseña</small>
                                                    </div>
                                                    <div class='mb-3'>
                                                        <label for='rol' class='form-label'>Rol</label>
                                                        <select class='form-select' id='rol' name='rol' required>
                                                            <option value='1'" . ($row['rol'] == 1 ? ' selected' : '') . ">Administrador</option>
                                                            <option value='2'" . ($row['rol'] == 2 ? ' selected' : '') . ">Empleado</option>
                                                        </select>
                                                    </div>
                                                    <div class='modal-footer'>
                                                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cerrar</button>
                                                        <button type='submit' class='btn btn-primary'>Guardar Cambios</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts para mostrar/ocultar contraseñas -->
<script>
    document.querySelectorAll('[id^="togglePassword"]').forEach(button => {
        button.addEventListener('click', function () {
            const input = button.previousElementSibling;
            const icon = button.querySelector('i');
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("bi-eye-slash");
                icon.classList.add("bi-eye");
            } else {
                input.type = "password";
                icon.classList.remove("bi-eye");
                icon.classList.add("bi-eye-slash");
            }
        });
    });
</script>

<?php
} else {
    header("Location: ./../index.php"); // Redirigir si no hay sesión activa
    exit();
}
?>
