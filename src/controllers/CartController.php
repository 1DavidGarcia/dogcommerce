<?php
// src/controllers/CartController.php

if (session_status() == PHP_SESSION_NONE) session_start();

class CartController {

    public function add($id, $name, $price, $image = null) {
        if (!isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id] = [
                'name' => $name,
                'price' => $price,
                'quantity' => 1,
                'image' => $image
            ];
        } else {
            $_SESSION['cart'][$id]['quantity'] += 1;
        }

        header('Location: /ecomerce/public/cart.php');
        exit();
    }

    public function remove($product_id) {
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
        }
        header('Location: /ecomerce/public/cart.php');
        exit();
    }

    public function clear() {
        unset($_SESSION['cart']);
        header('Location: /ecomerce/public/cart.php');
        exit();
    }
}

// Procesamiento por GET (opcional si usas directamente este archivo)
if (isset($_GET['action'])) {
    $controller = new CartController();

    switch ($_GET['action']) {
        case 'add':
            $controller->add(
                $_GET['id'],
                $_GET['name'],
                $_GET['price'],
                $_GET['image'] ?? null // ✅ se agrega image
            );
            break;
        case 'remove':
            $controller->remove($_GET['id']);
            break;
        case 'clear':
            $controller->clear();
            break;
    }
}
