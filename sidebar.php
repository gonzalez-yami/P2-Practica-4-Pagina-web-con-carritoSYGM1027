<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$cant_total_carrito = array_sum($_SESSION['carrito'] ?? []);
?>
<aside class="sidebar">
    <?php if (isset($_SESSION['nombre_cliente'])): ?>
        <div class="caja-login caja-bienvenida">
            <h3>¡Bienvenid@, <?php echo htmlspecialchars($_SESSION['nombre_cliente']); ?>!</h3>
            <p>Sesión activa</p>
        </div>

        <div class="caja-login">
            <h3>Tu Carrito</h3>
            <p style="margin-bottom: 12px; font-size: 14px; color: #7b6f88;">
                Productos: <strong><?php echo $cant_total_carrito; ?></strong>
            </p>
            <a href="carrito.php" class="btn-login">Ver mi Carrito</a>
        </div>
    <?php else: ?>
        <div class="caja-login">
            <h3>Iniciar Sesión</h3>
            <a href="login.php" class="btn-login">Iniciar Sesión</a>
        </div>
    <?php endif; ?>
</aside>