<?php
session_start();

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['pedido_exitoso_id'])) {
    header("Location: index.php");
    exit();
}

$num_pedido = $_SESSION['pedido_exitoso_id'];
unset($_SESSION['pedido_exitoso_id']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pedido Confirmado - NYX Cosmetics</title>
    <link rel="stylesheet" href="estilos.css?v=1.0">
</head>
<body>

    <header>
        <div class="top-bar">NYX COSMETICS</div>
        <nav>
            <ul>
                <li><a href="index.php">Inicio</a></li>
                <li><a href="logout.php" class="btn-cerrar-header">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <div class="contenedor-principal" style="justify-content: center;">
        <div class="caja-login card-auth" style="text-align: center; max-width: 480px;">
            <h2 style="color: #8b37e6; margin-bottom: 15px;">¡Pedido Registrado con Éxito!</h2>
            <div class="msg-exito" style="font-size: 15px; margin-bottom: 20px;">
                Tu número de folio es: <strong>#<?php echo $num_pedido; ?></strong>
            </div>
            <p style="color: #7b6f88; margin-bottom: 25px; font-size: 14px;">
                La orden ha sido vinculada a tu usuario y guardada en la base de datos de NYX Cosmetics.
            </p>
            <a href="index.php" class="btn-comprar">Volver al Inicio</a>
        </div>
    </div>

    <footer>
        GONZALEZ MARES SOFIA YAMILETH 5-J | P2-PRACTICA 1 FORMULARIO 1027
    </footer>

</body>
</html>