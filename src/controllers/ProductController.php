<?php
// src/controllers/ProductController.php
require_once __DIR__ . '/../models/Product.php';

class ProductController {
    public function index() {
        $productModel = new Product();
        $products = $productModel->getAll();
        include __DIR__ . '/../views/products/list.php';
    }
}
