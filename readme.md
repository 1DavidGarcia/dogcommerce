# YEALTECH E-commerce

Sistema web de catálogo y ventas de productos, desarrollado en PHP con MySQL.

## Características

- Registro e inicio de sesión de usuarios
- Panel de administración (productos, usuarios, pedidos)
- Carrito de compras y pagos con PayPal
- Gestión de stock y pedidos
- Catálogo visual con imágenes
- FAQ con chatbot inteligente

## Estructura del proyecto

```
config/
    database.php
public/
    index.php
    cart.php
    cart_action.php
    login.php
    logout.php
    register.php
    orders.php
    success.php
    faq.php
    admin/
        dashboard.php
        products.php
        create_product.php
        edit_product.php
        delete_product.php
        orders.php
        users.php
    uploads/
src/
    controllers/
    models/
    views/
    helpers/
    middleware/
vendor/
```

## Instalación

1. Clona el repositorio y colócalo en tu servidor local (ej: Laragon, XAMPP).
2. Crea una base de datos MySQL llamada `ecommerce_db`.
3. Importa las tablas necesarias (users, products, orders, order_items, etc).
4. Configura los datos de conexión en [`config/database.php`](config/database.php) si es necesario.
5. Accede a `public/index.php` desde tu navegador.

## Acceso administrador

- Regístrate y cambia el rol del usuario a `admin` desde el panel de usuarios.

## Créditos

- TailwindCSS para estilos
- PayPal SDK para pagos
- Dialogflow para chatbot FAQ

---

¡Listo para vender!
