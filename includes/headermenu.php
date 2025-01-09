<?php
// Verificar si la sesión ya está iniciada antes de llamarla
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si la sesión está activa
if (isset($_SESSION['iduser'])) {
    // Incluir la cabecera
    include "./../includes/config.php";

    // Obtener el rol del usuario desde la base de datos si no está en la sesión
    if (!isset($_SESSION['rol'])) {
        $iduser = $_SESSION['iduser'];
        $consulta = "SELECT rol FROM usuarios WHERE id_usuario = '$iduser'";
        $respuesta = mysqli_query($conexion, $consulta);
        if ($fila = mysqli_fetch_array($respuesta)) {
            $_SESSION['rol'] = $fila['rol']; // Guardar el rol en la sesión
        }
    }

    $rol = $_SESSION['rol']; // Obtener el rol desde la sesión
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="./../assets/css/master.css">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@1&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <title>Suspensiones Torres</title>

    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
        }

        /* Estilo de la barra superior */
        .top-bar {
            height: 60px;
            background-color: #f0f0f0;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
            flex-wrap: wrap; /* Permite que los elementos se ajusten en pantallas pequeñas */
        }

        /* Contenedor de "Suspensiones Torres" */
        .logo-container {
            flex-shrink: 0;
            font-size: 20px;
            font-weight: bold;
            color: blue;
            margin-right: 20px; /* Asegura que se mantenga al lado izquierdo */
            text-align: left;
        }

        /* Menú de opciones a la derecha */
        .menu-bar {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-grow: 1;
            flex-wrap: wrap; /* Permite que los elementos del menú se ajusten */
            gap: 15px; /* Espacio entre los enlaces */
        }

        .menu-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .menu-container a {
            color: black;
            text-decoration: none;
            font-weight: bold;
            padding: 5px 10px;
        }

        /* Estilo para el enlace activo con línea azul debajo */
        .menu-container a.active {
            border-bottom: 3px solid blue;
            color: black;
        }

        .menu-container a:hover {
            text-decoration: underline;
        }

        /* Estilo para el contenedor de usuario */
        .user-container {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            position: relative;
            flex-shrink: 0;
        }

        .user-container span {
            color: black;
        }

        /* Estilo para el botón de cerrar sesión */
        .logout-btn {
            display: none;
            margin-left: -5px;
            margin-top: 10px;
            position: absolute;
            top: 100%;
            left: 0;
            width: auto;
            padding: 10px;
            white-space: nowrap;
        }

        /* Cuando el usuario hace clic, muestra el botón de cerrar sesión */
        .user-container.show-logout .logout-btn {
            display: inline-block;
        }

        /* El contenido ocupa el 100% de la pantalla sin barra lateral */
        .content {
            padding: 70px 20px 20px;
            min-height: 100vh;
            background-color: #fff;
        }

        /* Media Queries para pantallas más pequeñas */
        @media (max-width: 768px) {
            .top-bar {
                flex-direction: column; /* Apilar los elementos en pantallas pequeñas */
                padding: 10px;
                height: auto; /* Ajustar la altura de la barra */
            }

            .logo-container {
                font-size: 18px;
                margin-bottom: 10px;
                text-align: center;
            }

            .menu-container {
                flex-direction: column;
                gap: 15px;
                align-items: center;
                width: 100%;
                justify-content: center; /* Centrar los elementos */
            }
            

            /* Asegurarse de que los elementos de usuario se alineen bien */
            .user-container {
                margin-top: 10px;
                justify-content: center;
            }
        }
    </style>
</head>

<body>

<!-- Barra superior con "Suspensiones Torres" y menú -->
<div class="top-bar">
    <div class="logo-container">
        Suspensiones Torres
    </div>
    <div class="menu-bar">
        <div class="menu-container">
            <a href="menu.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'menu.php' ? 'active' : ''; ?>">
                <i class="bi bi-house-door"></i> Inicio
            </a>
            <a href="venta.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'venta.php' ? 'active' : ''; ?>">
                <i class="bi bi-cart"></i> Venta
            </a>
            <a href="productos.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'productos.php' ? 'active' : ''; ?>">
                <i class="bi bi-box"></i> Productos
            </a>
            <?php if ($rol == 1): ?>
                <a href="usuarios.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'usuarios.php' ? 'active' : ''; ?>">
                    <i class="bi bi-person-lines-fill"></i> Usuarios
                </a>
                <a href="corte.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'corte.php' ? 'active' : ''; ?>">
                    <i class="bi bi-cash"></i> Corte
                </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="user-container" onclick="toggleLogout(event)">
        <span><i class="bi bi-person-circle"></i> <?php echo $_SESSION['nombre'] . ' ' . $_SESSION['apellido']; ?></span>
        <a href="destruir.php" class="btn btn-secondary logout-btn">Cerrar sesión</a>
    </div>
</div>


<!-- Scripts de Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script para las teclas de acceso rápido -->
<script>
    document.addEventListener('keydown', function(event) {
        // Las teclas F1 y F2 están disponibles para ambos roles
        if (event.key === 'F1') {
            event.preventDefault();
            window.location.href = 'menu.php';
        } else if (event.key === 'F2') {
            event.preventDefault();
            window.location.href = 'venta.php';
        } else if (event.key === 'F3') {
            event.preventDefault();
            window.location.href = 'productos.php';
        } else if (event.key === 'F4') {
            event.preventDefault();
            window.location.href = 'usuarios.php';
        } else if (event.key === 'F5') {
            event.preventDefault();
            window.location.href = 'corte.php';
        }
    });

    // Función para mostrar u ocultar el botón de cerrar sesión
    function toggleLogout(event) {
        // Evitar que el evento de clic en el contenedor se propague
        event.stopPropagation();
        document.querySelector('.user-container').classList.toggle('show-logout');
    }

    // Detectar clics fuera del contenedor del usuario para ocultar el botón
    document.addEventListener('click', function(event) {
        if (!document.querySelector('.user-container').contains(event.target)) {
            document.querySelector('.user-container').classList.remove('show-logout');
        }
    });
</script>

</body>
</html>

<?php
} else {
    header("Location: index.php");
    exit();
}
?>
