<div class="login col-12 col-md-6 col-sm-6 p-5 mt-4">
  <div class="row">
<img class="logo" src="./../assets/resources/logo.jpg">

<h6 class="mb-3 nom">Punto de venta</h6>


<form action="" method="post">
  
  <div class="mb-1 w-85 labelt">
    <label for="usuario"><span><img style= "width:25px;" src="../assets/resources/user1.png">  Usuario:</label>
    <div class="p-1">
    <input type="text" class="form-control" name="usuarioU">
    </div>
  </div>

  <div class="mb-1 w-85 labelt">
    <label for="contrasena"><span><img style= "width:25px;" src="./../assets/resources/pass1.png">  Contraseña:</label>
    <div class="p-1">
    <input type="password" class="form-control" name="contrasenaU">
    </div>
  </div>

  <div class="mb-2 p-2 d-flex justify-content-center">
  <button type="submit" class="btn" style="background:#e5e1e1;">Entrar</button>
  </div>
</form>

<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $contador = 0;
    $usuario = $_POST['usuarioU'];
    $contrasena = $_POST['contrasenaU'];

    // Verifica si hay espacios vacíos en el nombre de usuario o contraseña
    if (strpos($usuario, ' ') !== false || strpos($contrasena, ' ') !== false) {
        $mensaje = "El nombre de usuario y la contraseña no pueden contener espacios.";
    } else {
        include "../includes/config.php";

        $consulta = "SELECT * FROM usuarios WHERE usuario = '$usuario' AND contrasena = '$contrasena'";
        $respuesta = mysqli_query($conexion, $consulta);

        while ($fila = mysqli_fetch_array($respuesta)) {
            $iduser = $fila['id_usuario'];
            $nombre = $fila['nombre']; // Asegúrate de que el campo 'nombre' exista en la base de datos
            $apellido = $fila['apellido']; // Asegúrate de que el campo 'apellido' exista en la base de datos
            $_SESSION['nombre'] = $nombre; // Guardar nombre en la sesión
            $_SESSION['apellido'] = $apellido; // Guardar apellido en la sesión
            $contador++;
        }

        if ($contador > 0) {
            $_SESSION['iduser'] = $iduser;
            header("Location: menu.php");
        } else {
            $mensaje = "Usuario o contraseña incorrectos.";
        }
    }



    // Si son incorrectas o hay otros errores, mostrar el mensaje de error
    echo '<div class="p-1 alert alert-danger d-flex justify-content-center" role="alert">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Warning:">
            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
        </svg>
        <div>' . $mensaje . '</div>
    </div>
    <script>
          setTimeout(function(){
            window.location="./"
          }, 1500)
          </script>';
}
?>
        
   

</div>

    

