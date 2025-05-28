<?php
session_start();
require_once __DIR__ . '/../src/controllers/CartController.php';

$controller = new CartController();

$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? null;
$name = $_GET['name'] ?? null;
$price = $_GET['price'] ?? null;
$image = $_GET['image'] ?? null;


switch ($action) {
    case 'add':
        if ($id && $name && $price) {
            $controller->add($id, $name, $price, $image);
            
        }
        break;
    case 'remove':
        if ($id) {
            $controller->remove($id);
        }
        break;
    case 'clear':
        $controller->clear();
        break;
    case 'increase':
        if ($id && isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity'] += 1;
        }
        break;
    case 'decrease':
        if ($id && isset($_SESSION['cart'][$id])) {
            if ($_SESSION['cart'][$id]['quantity'] > 1) {
                $_SESSION['cart'][$id]['quantity'] -= 1;
            } else {
                unset($_SESSION['cart'][$id]); // eliminar si llega a 0
            }
        }
        break;
    default:
        header('Location: cart.php');
        break;
}
