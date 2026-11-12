<?php
session_start();
include("conexion.php");

$mensaje = "";
$usuario_no_existe = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario  = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($usuario !== '' && $password !== '') {
        $stmt = $conexion->prepare("SELECT id, nombre, usuario, password FROM usuarios WHERE usuario = ?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $datos = $resultado->fetch_assoc();
            if (password_verify($password, $datos['password'])) {
                session_regenerate_id(true);
                $_SESSION['usuario_id'] = $datos['id'];
                $_SESSION['nombre_cliente'] = $datos['nombre'];
                header("Location: index.php");
                exit();
            } else {
                $mensaje = "Contraseña incorrecta. Inténtalo de nuevo.";
            }
        } else {
            $mensaje = "El usuario ingresado no existe.";
            $usuario_no_existe = true;
        }
        $stmt->close();
    } else {
        $mensaje = "Por favor, llena todos los campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar Sesión - NYX Cosmetics</title>
    <link rel="stylesheet" href="estilos.css?v=1.0">
</head>
<body>

    <header>
        <div class="top-bar">NYX COSMETICS</div>
        <nav>
            <ul>
                <li><a href="index.php">Inicio</a></li>
                <?php if (isset($_SESSION['nombre_cliente'])): ?>
                    <li><a href="logout.php" class="btn-cerrar-header">Cerrar Sesión</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="activo">Cuenta</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <div class="encabezado-seccion">
        <h1>Iniciar Sesión</h1>
        <p>Ingresa a tu cuenta para gestionar tus pedidos y compras en NYX Cosmetics.</p>
    </div>

    <div class="contenedor-principal" style="justify-content: center;">
        <div class="caja-login card-auth">
            <h2>Bienvenida de nuevo</h2>

            <?php if (!empty($mensaje)): ?>
                <div class="msg-error">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="usuario">Nombre de Usuario:</label>
                    <input type="text" id="usuario" name="usuario" placeholder="Tu usuario" required value="<?php echo htmlspecialchars($_POST['usuario'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" placeholder="Tu contraseña" required>
                </div>

                <button type="submit" class="btn-comprar">Ingresar a mi Cuenta</button>
            </form>

            <p class="texto-registro">
                ¿No tienes una cuenta aún? <a href="registro.php">Regístrate aquí</a>
            </p>
        </div>
    </div>

    <footer>
        GONZALEZ MARES SOFIA YAMILETH 5-J | P2-PRACTICA 1 FORMULARIO 1027
    </footer>

</body>
</html>