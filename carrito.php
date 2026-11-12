<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$items = [];
$total = 0;

foreach (($_SESSION['carrito'] ?? []) as $id => $cantidad) {
    $stmt = $conexion->prepare("SELECT id, nombre, precio, imagen FROM productos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $p = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($p) {
        $p['cantidad'] = (int)$cantidad;
        $p['subtotal'] = $p['precio'] * $p['cantidad'];
        $total += $p['subtotal'];
        $items[] = $p;
    } else {
        unset($_SESSION['carrito'][$id]);
    }
}

$sin_imagen = "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='150' height='150'><rect width='100%' height='100%' fill='%23f3e8ff'/><text x='50%' y='50%' fill='%238b37e6' font-size='14' text-anchor='middle' dominant-baseline='middle'>Sin imagen</text></svg>";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Carrito - NYX Cosmetics</title>
    <link rel="stylesheet" href="estilos.css?v=3.0">
    <style>
        /* Estilos blindados para el Carrito y Tamaño Uniforme de Imágenes */
        .lista-carrito {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 30px;
        }

        .item-carrito {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 20px;
            padding: 15px 20px;
            border-radius: 14px;
            background-color: #fcf8ff;
            border: 1px solid #f0e2fc;
        }

        .img-item {
            width: 85px !important;
            height: 85px !important;
            min-width: 85px !important;
            min-height: 85px !important;
            max-width: 85px !important;
            max-height: 85px !important;
            border-radius: 10px;
            overflow: hidden;
            background-color: #ffffff;
            border: 1px solid #f0e2fc;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 5px;
            flex-shrink: 0;
        }

        .img-item img {
            width: 100% !important;
            height: 100% !important;
            max-width: 75px !important;
            max-height: 75px !important;
            object-fit: contain !important;
        }

        .info-item {
            flex: 1;
            text-align: left;
        }

        .info-item h3 {
            font-size: 16px;
            color: #8b37e6;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .info-item .precio {
            font-size: 13.5px;
            color: #7b6f88;
        }

        .aviso-limite {
            font-size: 11.5px;
            color: #dc2626;
            font-weight: 600;
            margin-top: 3px;
        }

        .cantidad-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-cantidad {
            background-color: #c483f9;
            color: white;
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 6px;
            font-weight: 800;
            font-size: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-cantidad:hover:not(:disabled) {
            background-color: #b05cf6;
        }

        .btn-cantidad:disabled, .btn-deshabilitado {
            background-color: #e9d5ff;
            cursor: not-allowed;
        }

        .cantidad-valor {
            font-weight: 700;
            font-size: 15px;
            color: #4a3e56;
            min-width: 22px;
            text-align: center;
        }

        .subtotal-item {
            font-size: 16px;
            font-weight: 800;
            color: #8b37e6;
            min-width: 90px;
            text-align: right;
        }

        .btn-eliminar {
            background: transparent;
            border: none;
            color: #ef4444;
            font-size: 18px;
            font-weight: 800;
            cursor: pointer;
            padding: 6px 10px;
            border-radius: 6px;
        }

        .btn-eliminar:hover {
            background-color: #fee2e2;
        }

        .ticket {
            background-color: #faf5ff;
            border: 2px dashed #c483f9;
            border-radius: 14px;
            padding: 25px;
            margin-top: 25px;
        }

        .ticket h3 {
            text-align: center;
            color: #8b37e6;
            font-size: 18px;
            font-weight: 800;
            letter-spacing: 1px;
        }

        .ticket-sub {
            text-align: center;
            font-size: 12.5px;
            color: #7b6f88;
            margin-bottom: 15px;
        }

        .ticket-separador {
            border-bottom: 1px dashed #e9d5ff;
            margin: 12px 0;
        }

        .ticket-linea {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            color: #4a3e56;
            margin-bottom: 6px;
        }

        .ticket-total {
            font-size: 17px;
            font-weight: 800;
            color: #8b37e6;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <header>
        <div class="top-bar">NYX COSMETICS</div>
        <nav>
            <ul>
                <li><a href="index.php">Inicio</a></li>
                <li><a href="labiales.php">Labiales</a></li>
                <li><a href="ojos.php">Ojos</a></li>
                <li><a href="rostro.php">Rostro</a></li>
                <li><a href="logout.php" class="btn-cerrar-header">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <div class="encabezado-seccion">
        <h1>Tu Carrito de Compras</h1>
        <p>Revisa tus productos antes de finalizar tu pedido.</p>
    </div>

    <div class="contenedor-principal">
        <div class="catalogo-container">
            <h2 class="subtitulo-catalogo">Productos Seleccionados</h2>

            <?php if (isset($_SESSION['aviso'])): ?>
                <div class="msg-error" style="margin-bottom: 20px;">
                    <?php echo htmlspecialchars($_SESSION['aviso']); unset($_SESSION['aviso']); ?>
                </div>
            <?php endif; ?>

            <?php if (empty($items)): ?>
                <p class="descripcion-inicio">Tu carrito se encuentra actualmente vacío.</p>
                <a href="index.php" class="btn-comprar" style="max-width: 250px;">Explorar Productos</a>
            <?php else: ?>

                <div class="lista-carrito">
                    <?php foreach ($items as $it): ?>
                        <div class="item-carrito">
                            <div class="img-item">
                                <img src="img/<?php echo htmlspecialchars($it['imagen']); ?>" alt="<?php echo htmlspecialchars($it['nombre']); ?>" onerror="this.onerror=null; this.src='<?php echo $sin_imagen; ?>';">
                            </div>

                            <div class="info-item">
                                <h3><?php echo htmlspecialchars($it['nombre']); ?></h3>
                                <div class="precio">$<?php echo number_format($it['precio'], 2); ?> USD c/u</div>
                                <?php if ($it['cantidad'] >= 5): ?>
                                    <div class="aviso-limite">Límite máximo alcanzado (5 unidades)</div>
                                <?php endif; ?>
                            </div>

                            <div class="cantidad-item">
                                <form method="POST" action="actualizar_carrito.php" style="margin:0;">
                                    <input type="hidden" name="id" value="<?php echo (int)$it['id']; ?>">
                                    <input type="hidden" name="accion" value="menos">
                                    <button type="submit" class="btn-cantidad" <?php echo $it['cantidad'] <= 1 ? 'disabled' : ''; ?>>−</button>
                                </form>

                                <span class="cantidad-valor"><?php echo $it['cantidad']; ?></span>

                                <?php if ($it['cantidad'] >= 5): ?>
                                    <button type="button" class="btn-cantidad btn-deshabilitado" onclick="alert('Has alcanzado el límite máximo de 5 unidades por producto');">+</button>
                                <?php else: ?>
                                    <form method="POST" action="actualizar_carrito.php" style="margin:0;">
                                        <input type="hidden" name="id" value="<?php echo (int)$it['id']; ?>">
                                        <input type="hidden" name="accion" value="mas">
                                        <button type="submit" class="btn-cantidad">+</button>
                                    </form>
                                <?php endif; ?>
                            </div>

                            <div class="subtotal-item">$<?php echo number_format($it['subtotal'], 2); ?></div>

                            <form method="POST" action="actualizar_carrito.php" style="margin:0;">
                                <input type="hidden" name="id" value="<?php echo (int)$it['id']; ?>">
                                <input type="hidden" name="accion" value="eliminar">
                                <button type="submit" class="btn-eliminar" title="Eliminar del carrito">✕</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Mini Ticket de Resumen -->
                <div class="ticket">
                    <h3>NYX COSMETICS</h3>
                    <p class="ticket-sub">Resumen de Compra</p>
                    <div class="ticket-separador"></div>

                    <?php foreach ($items as $it): ?>
                        <div class="ticket-linea">
                            <span><?php echo htmlspecialchars($it['nombre']); ?> (x<?php echo $it['cantidad']; ?>)</span>
                            <span>$<?php echo number_format($it['subtotal'], 2); ?></span>
                        </div>
                    <?php endforeach; ?>

                    <div class="ticket-separador"></div>
                    <div class="ticket-linea ticket-total">
                        <span>Total a Pagar</span>
                        <span>$<?php echo number_format($total, 2); ?> USD</span>
                    </div>

                    <div style="margin-top: 20px;">
                        <a href="checkout.php" class="btn-comprar">Proceder al Pago</a>
                    </div>
                </div>

            <?php endif; ?>
        </div>

        <?php include("sidebar.php"); ?>
    </div>

    <footer>
        GONZALEZ MARES SOFIA YAMILETH 5-J | P2-PRACTICA 1 FORMULARIO 1027
    </footer>

</body>
</html>