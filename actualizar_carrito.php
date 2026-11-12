<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $accion = $_POST['accion'] ?? '';

    if (isset($_SESSION['carrito'][$id])) {
        if ($accion === 'mas') {
            if ($_SESSION['carrito'][$id] < 5) {
                $_SESSION['carrito'][$id]++;
            } else {
                $_SESSION['aviso'] = "Has alcanzado el límite máximo de 5 unidades por producto";
            }
        } elseif ($accion === 'menos') {
            if ($_SESSION['carrito'][$id] > 1) {
                $_SESSION['carrito'][$id]--;
            }
        } elseif ($accion === 'eliminar') {
            unset($_SESSION['carrito'][$id]);
        }
    }
}

header("Location: carrito.php");
exit();