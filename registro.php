<?php
session_start();
include("conexion.php");

$mensaje = "";
$exito = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre   = trim($_POST['nombre'] ?? '');
    $usuario  = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($nombre === '' || $usuario === '' || $password === '') {
        $mensaje = "Por favor, completa todos los campos.";
    } elseif (strlen($password) < 8) {
        $mensaje = "La contraseña debe tener al menos 8 caracteres.";
    } else {
        $stmt_check = $conexion->prepare("SELECT id FROM usuarios WHERE usuario = ?");
        $stmt_check->bind_param("s", $usuario);
        $stmt_check->execute();
        $resultado = $stmt_check->get_result();

        if ($resultado->num_rows > 0) {
            $mensaje = "El usuario ya está registrado. Intenta con otro.";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, usuario, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $nombre, $usuario, $password_hash);

            if ($stmt->execute()) {
                $exito = true;
                $mensaje = "¡Registro exitoso! Ya puedes iniciar sesión.";
            } elseif ($conexion->errno === 1062) {
                $mensaje = "El usuario ya está registrado. Intenta con otro.";
            } else {
                $mensaje = "Error al registrar el usuario.";
            }
            $stmt->close();
        }
        $stmt_check->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registro de Cliente - NYX Cosmetics</title>
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
        <h1>Crea tu Cuenta</h1>
        <p>Regístrate para formar parte de la comunidad NYX Cosmetics.</p>
    </div>

    <div class="contenedor-principal" style="justify-content: center;">
        <div class="caja-login card-auth">
            <h2>Registro de Cliente</h2>

            <?php if (!empty($mensaje)): ?>
                <div class="<?php echo $exito ? 'msg-exito' : 'msg-error'; ?>">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>

            <?php if ($exito): ?>
                <a href="login.php" class="btn-comprar">Ir a Iniciar Sesión</a>
            <?php else: ?>
                <form action="registro.php" method="POST">
                    <div class="form-group">
                        <label for="nombre">Nombre Completo:</label>
                        <input type="text" id="nombre" name="nombre" placeholder="Tu nombre completo" required maxlength="100" value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="usuario">Nombre de Usuario:</label>
                        <input type="text" id="usuario" name="usuario" placeholder="Crea tu usuario" required maxlength="50" value="<?php echo htmlspecialchars($_POST['usuario'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="password">Contraseña (mínimo 8 caracteres):</label>
                        <input type="password" id="password" name="password" placeholder="Crea tu contraseña" required minlength="8">
                    </div>

                    <button type="submit" class="btn-comprar">Registrarse</button>
                </form>

                <p class="texto-registro">
                    ¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a>
                </p>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        GONZALEZ MARES SOFIA YAMILETH 5-J | P2-PRACTICA 1 FORMULARIO 1027
    </footer>

</body>
</html>