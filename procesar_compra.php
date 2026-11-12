<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['usuario_id']) || empty($_SESSION['carrito'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id      = (int)$_SESSION['usuario_id'];
    $nombre_completo = trim($_POST['nombre_completo'] ?? '');
    $telefono        = trim($_POST['telefono'] ?? '');
    $direccion       = trim($_POST['direccion'] ?? '');
    $metodo_pago     = trim($_POST['metodo_pago'] ?? '');

    if (empty($nombre_completo) || empty($telefono) || empty($direccion) || empty($metodo_pago)) {
        header("Location: checkout.php");
        exit();
    }

    // 1. Calcular total y recopilar productos del carrito
    $total = 0;
    $items = [];

    foreach ($_SESSION['carrito'] as $id => $cantidad) {
        $stmt = $conexion->prepare("SELECT id, precio FROM productos WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $p = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($p) {
            $subtotal = $p['precio'] * $cantidad;
            $total += $subtotal;
            $items[] = [
                'producto_id'     => (int)$p['id'],
                'cantidad'        => (int)$cantidad,
                'precio_unitario' => (float)$p['precio'],
                'subtotal'        => (float)$subtotal
            ];
        }
    }

    // 2. Guardar pedido en la base de datos vinculado al usuario
    if ($total > 0 && !empty($items)) {
        $stmt_ped = $conexion->prepare("INSERT INTO pedidos (usuario_id, nombre_completo, direccion, telefono, metodo_pago, total) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt_ped->bind_param("issssd", $usuario_id, $nombre_completo, $direccion, $telefono, $metodo_pago, $total);
        
        if ($stmt_ped->execute()) {
            $pedido_id = $stmt_ped->insert_id;
            $stmt_ped->close();

            // 3. Guardar el detalle de cada producto comprado
            $stmt_det = $conexion->prepare("INSERT INTO detalles_pedido (pedido_id, producto_id, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)");
            foreach ($items as $item) {
                $stmt_det->bind_param("iiidd", $pedido_id, $item['producto_id'], $item['cantidad'], $item['precio_unitario'], $item['subtotal']);
                $stmt_det->execute();
            }
            $stmt_det->close();

            // 4. Vaciar el carrito y redirigir a confirmación
            $_SESSION['carrito'] = [];
            $_SESSION['pedido_exitoso_id'] = $pedido_id;

            header("Location: confirmacion.php");
            exit();
        }
    }
}

header("Location: carrito.php");
exit();