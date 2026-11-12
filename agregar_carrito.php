<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$permitidas = ['index.php', 'labiales.php', 'ojos.php', 'rostro.php'];
$volver = $_GET['volver'] ?? 'index.php';
if (!in_array($volver, $permitidas, true)) {
    $volver = 'index.php';
}

$id = (int)($_GET['id'] ?? 0);

$stmt = $conexion->prepare("SELECT id FROM productos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$existe = $stmt->get_result()->num_rows > 0;
$stmt->close();

if ($existe) {
    $actual = $_SESSION['carrito'][$id] ?? 0;
    if ($actual >= 5) {
        $_SESSION['aviso'] = "Has alcanzado el límite máximo de 5 unidades por producto";
    } else {
        $_SESSION['carrito'][$id] = $actual + 1;
        $_SESSION['aviso'] = "Producto agregado al carrito.";
    }
}

header("Location: " . $volver);
exit();