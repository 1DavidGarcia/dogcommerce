<?php
if (session_status() == PHP_SESSION_NONE) session_start();

function getCartItemCount() {
    if (!isset($_SESSION['cart'])) return 0;

    $count = 0;
    foreach ($_SESSION['cart'] as $item) {
        $count += $item['quantity'];
    }
    return $count;
}
