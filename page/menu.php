<?php
session_start();

// Verificar si la sesión está activa
if (isset($_SESSION['iduser'])) {

    // Guardar cambios en el bloc de notas
    if (isset($_POST['guardarNotepad']) && $_SESSION['rol'] == 1) {
        $nuevoContenido = $_POST['notepadContent'];
        $file_path = './bloc_de_notas.txt';

        if (file_put_contents($file_path, $nuevoContenido) !== false) {
            $_SESSION['mensaje'] = "Cambios guardados exitosamente.";
        } else {
            $_SESSION['mensaje'] = "Error al guardar los cambios.";
        }

        header("Location: menu.php");
        exit();
    }

    include "./../includes/headermenu.php"; 
?>
<title>Menú</title>

<link rel="stylesheet" type="text/css" href="./../assets/css/master.css">

<div class="content" id="content">
    <div class="center-content" style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
        <br><br>

        <div style="display: flex; justify-content: space-between; align-items: center; width: 60%;">
            <div class="notepad-container" style="flex: 1; max-width: 400px; margin-right: 20px;">
                <h3>Recomendaciones</h3>
                <form method="POST" action="menu.php">
                    <textarea name="notepadContent" class="form-control" rows="10" <?php echo ($_SESSION['rol'] == 1) ? '' : 'readonly'; ?>>
                        <?php
                            $file_path = './bloc_de_notas.txt';
                            if (file_exists($file_path)) {
                                echo htmlspecialchars(file_get_contents($file_path));
                            }
                        ?>
                    </textarea>
                    
                    <?php if ($_SESSION['rol'] == 1): ?>
                        <button type="submit" name="guardarNotepad" class="btn btn-primary mt-3">Guardar Cambios</button>
                    <?php endif; ?>
                </form>
            </div>

            <div class="logo-container" style="flex: 1; text-align: right;">
                <img src="./../assets/resources/logo.jpg" alt="Logo de Suspensiones Torres" class="img-fluid img-logo">
            </div>
        </div>
        
        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="success-message"><?php echo $_SESSION['mensaje']; ?></div>
            <?php unset($_SESSION['mensaje']); ?>
        <?php endif; ?>
    </div>
</div>

<?php include "./../includes/footer.php"; ?>
<?php
} else {
    header("Location: index.php");
    exit();
}
?>

<script>
    // Ocultar mensaje después de 1.5 segundos
    setTimeout(function() {
        var message = document.querySelector('.success-message');
        if (message) {
            message.style.display = 'none';
        }
    }, 1500);
</script>
