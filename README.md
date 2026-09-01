# E-commerce API

API RESTful para un sistema de comercio electrónico desarrollada con Laravel 12, PHP y MySQL.

El proyecto permite gestionar usuarios, productos, órdenes de compra y pagos, utilizando autenticación mediante tokens con Laravel Sanctum y documentación de la API mediante Swagger/OpenAPI.

## Tecnologias utilizadas

* Laravel 12
* PHP 8.2+
* MySQL
* Laravel Sanctum
* L5-Swagger
* Swagger/OpenAPI
* Composer

## Requisitos

Antes de instalar el proyecto es necesario tener:

* PHP 8.2 o superior
* Composer
* MySQL
* Laravel 12
* Servidor web local, por ejemplo XAMPP

## Instalacion

Clonar el repositorio:

```bash
git clonehttps://github.com/fermar807/ecommerce-api
```

Ingresar al proyecto:

```bash
cd ecommerce-api
```

Instalar las dependencias:

```bash
composer install
```

Crear el archivo `.env`:

```bash
copy .env.example .env
```

Generar la clave de la aplicacion:

```bash
php artisan key:generate
```

## Configuracion de base de datos

Crear una base de datos MySQL llamada:

```text
ecommerce
```

Configurar las variables de base de datos en `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecommerce
DB_USERNAME=root
DB_PASSWORD=
```

## Migraciones

Ejecutar:

```bash
php artisan migrate
```

Esto creara las tablas necesarias para el funcionamiento de la API.

## Datos de prueba

El proyecto incluye un Factory y un Seeder para generar productos de prueba.

Ejecutar:

```bash
php artisan db:seed --class=ProductSeeder
```

Esto generara productos utilizando datos ficticios.

## Ejecutar la aplicacion

Iniciar el servidor de desarrollo:

```bash
php artisan serve
```

La API estara disponible en:

```text
http://127.0.0.1:8000
```

## Autenticacion

La API utiliza Laravel Sanctum para la autenticacion mediante tokens.

### Registrar usuario

```http
POST /api/register
```

Ejemplo:

```json
{
    "name": "Fernando Gonzalez",
    "email": "fernando@example.com",
    "password": "Password123",
    "password_confirmation": "Password123"
}
```

### Iniciar sesion

```http
POST /api/login
```

Ejemplo:

```json
{
    "email": "fernando@example.com",
    "password": "Password123"
}
```

El endpoint devuelve un token que debe utilizarse para acceder a los endpoints protegidos.

En las peticiones autenticadas se debe enviar:

```http
Authorization: Bearer TOKEN
```

## Endpoints

### Authentication

| Metodo | Endpoint        | Autenticacion |
| ------ | --------------- | ------------- |
| POST   | `/api/register` | No            |
| POST   | `/api/login`    | No            |

### Products

| Metodo | Endpoint                     | Autenticacion |
| ------ | ---------------------------- | ------------- |
| GET    | `/api/products`              | No            |
| GET    | `/api/products/{id}`         | No            |
| POST   | `/api/products`              | Si            |
| PUT    | `/api/products/{id}`         | Si            |
| DELETE | `/api/products/{id}`         | Si            |
| POST   | `/api/products/{id}/restore` | Si            |

Los productos utilizan Soft Delete, por lo que al eliminar un producto no se elimina fisicamente de la base de datos.

### Orders

| Metodo | Endpoint           | Autenticacion |
| ------ | ------------------ | ------------- |
| GET    | `/api/orders`      | Si            |
| GET    | `/api/orders/{id}` | Si            |
| POST   | `/api/orders`      | Si            |

### Payments

| Metodo | Endpoint                        | Autenticacion |
| ------ | ------------------------------- | ------------- |
| POST   | `/api/orders/{orderId}/payment` | Si            |

## Swagger / OpenAPI

La API cuenta con documentacion interactiva mediante Swagger UI.

Para generar la documentacion:

```bash
php artisan l5-swagger:generate
```

Swagger UI esta disponible en:

```text
http://127.0.0.1:8000/api/documentation
```

Desde Swagger UI es posible consultar los endpoints disponibles y probar las operaciones de la API.

Para los endpoints protegidos se debe utilizar el boton `Authorize` e ingresar el token de Sanctum.

## Estructura principal

```text
app/
├── Http/
│   ├── Controllers/
│   │   ├── ProductController.php
│   │   ├── OrderController.php
│   │   ├── PaymentController.php
│   │   └── UserController.php
│   │
│   └── Requests/
│       └── ProductRequest.php
│
├── Models/
│   ├── Product.php
│   ├── Order.php
│   ├── OrderItem.php
│   └── Payment.php
│
└── Docs/
    └── OpenApi.php
```

## Estado del proyecto

Actualmente se encuentran implementados:

* Registro de usuarios
* Inicio de sesion
* Autenticacion mediante Laravel Sanctum
* CRUD de productos
* Soft Delete y restauracion de productos
* Validacion mediante Form Requests
* Creacion de ordenes
* Consulta de ordenes
* Registro de pagos
* Factory y Seeder de productos
* Documentacion Swagger/OpenAPI

### Pendientes

* Mejorar el manejo global y consistente de errores.
* Implementar transacciones de base de datos en el procesamiento de pagos.
* Integracion con Stripe, si finalmente se requiere.

## Autor

Proyecto desarrollado como ejercicio practico de desarrollo de APIs RESTful con Laravel.
