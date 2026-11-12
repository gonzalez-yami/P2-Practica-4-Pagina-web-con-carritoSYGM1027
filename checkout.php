<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

if (empty($_SESSION['carrito'])) {
    header("Location: carrito.php");
    exit();
}

$items = [];
$total = 0;

foreach ($_SESSION['carrito'] as $id => $cantidad) {
    $stmt = $conexion->prepare("SELECT id, nombre, precio FROM productos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $p = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($p) {
        $p['cantidad'] = (int)$cantidad;
        $p['subtotal'] = $p['precio'] * $p['cantidad'];
        $total += $p['subtotal'];
        $items[] = $p;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Finalizar Compra - NYX Cosmetics</title>
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

    <div class="encabezado-seccion">
        <h1>Datos de Envío y Pago</h1>
        <p>Ingresa tus datos para completar tu pedido.</p>
    </div>

    <div class="contenedor-principal" style="justify-content: center;">
        <div class="caja-login card-auth" style="max-width: 500px;">
            <h2>Información del Pedido</h2>

            <form action="procesar_compra.php" method="POST">
                <div class="form-group">
                    <label for="nombre_completo">Nombre Completo:</label>
                    <input type="text" id="nombre_completo" name="nombre_completo" placeholder="Nombre de quien recibe" required value="<?php echo htmlspecialchars($_SESSION['nombre_cliente'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="telefono">Teléfono de Contacto:</label>
                    <input type="tel" id="telefono" name="telefono" placeholder="Ej. 656 123 4567" required>
                </div>

                <div class="form-group">
                    <label for="direccion">Dirección Completa de Envío:</label>
                    <input type="text" id="direccion" name="direccion" placeholder="Calle, número, colonia, ciudad" required>
                </div>

                <div class="form-group">
                    <label for="metodo_pago">Método de Pago:</label>
                    <select id="metodo_pago" name="metodo_pago" required style="width: 100%; padding: 11px; border: 1px solid #e9d5ff; border-radius: 8px; font-size: 14px; background-color: #faf5ff;">
                        <option value="Tarjeta de Débito / Crédito">Tarjeta de Débito / Crédito</option>
                        <option value="Transferencia bancaria">Transferencia bancaria</option>
                    </select>
                </div>

                <div class="ticket" style="margin-bottom: 20px;">
                    <div class="ticket-linea ticket-total">
                        <span>Total a Pagar:</span>
                        <span>$<?php echo number_format($total, 2); ?> USD</span>
                    </div>
                </div>

                <button type="submit" class="btn-comprar">Confirmar y Guardar Pedido</button>
            </form>
        </div>
    </div>

    <footer>
        GONZALEZ MARES SOFIA YAMILETH 5-J | P2-PRACTICA 1 FORMULARIO 1027
    </footer>

</body>
</html>