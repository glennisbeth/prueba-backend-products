# Products API

RESTful API built with Laravel to manage products and their prices in different currencies.

## Requirements

- PHP 8.2+
- Composer
- SQLite (default) or MySQL / PostgreSQL

## Installation

```bash
git clone https://github.com/glennisbeth/prueba-backend-products.git
cd prueba-backend-products
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

The project uses SQLite by default. If the database file does not exist, `php artisan migrate` will offer to create it. To use another database, update the `DB_*` variables in `.env` before running the migrations.

The seeders create a test user, four currencies (USD, EUR, COP, MXN) and 10 sample products.

| Email | Password |
|---|---|
| test@example.com | password |

## Documentation

The API is documented with OpenAPI 3. With the server running, the interactive Swagger UI is available at:

http://localhost:8000/docs

To try the protected endpoints, call `POST /login`, copy the token, click **Authorize** and paste it.

The specification file is `public/openapi.yaml`, and it can also be imported into tools like Postman or Insomnia.

## Authentication

Every endpoint except login requires a Sanctum token. Get one with:

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password"}'
```

Send it in the `Authorization: Bearer <token>` header. Tokens expire after 24 hours.

## Endpoints

All routes are prefixed with `/api`.

| Method | Route | Description |
|---|---|---|
| POST | /login | Get a token |
| POST | /logout | Revoke the current token |
| GET | /currencies | List currencies |
| GET | /products | List products (paginated, supports `?per_page=` and `?search=`) |
| POST | /products | Create a product |
| GET | /products/{id} | Get a product with its prices |
| PUT | /products/{id} | Update a product |
| DELETE | /products/{id} | Delete a product |
| GET | /products/{id}/prices | List the prices of a product |
| POST | /products/{id}/prices | Register a price in another currency |

### Create a product

```json
{
    "name": "Mechanical keyboard",
    "description": "Red switches and backlight",
    "price": 120.50,
    "currency_id": 1,
    "tax_cost": 22.90,
    "manufacturing_cost": 60
}
```

Response `201`:

```json
{
    "data": {
        "id": 11,
        "name": "Mechanical keyboard",
        "description": "Red switches and backlight",
        "price": "120.50",
        "currency_id": 1,
        "currency": { "id": 1, "name": "USD", "symbol": "$", "exchange_rate": "1.000000" },
        "tax_cost": "22.90",
        "manufacturing_cost": "60.00",
        "created_at": "2026-10-29T15:04:11+00:00",
        "updated_at": "2026-10-29T15:04:11+00:00"
    }
}
```

`PUT /products/{id}` accepts partial updates: only the fields to change need to be sent.

### Register a price in another currency

```json
{
    "currency_id": 2,
    "price": 115
}
```

`price` is optional. When it is not sent, it is calculated from the product's base price and the exchange rates of both currencies. Exchange rates are expressed against USD, so the amount is first converted to USD and then to the target currency.

A product cannot have two prices in the same currency, and a price cannot be registered in the product's base currency.

### Response codes

| Code | When |
|---|---|
| 200 | Successful request |
| 201 | Resource created |
| 204 | Resource deleted / logout |
| 401 | Missing or invalid token |
| 404 | Resource not found |
| 422 | Validation error |
| 429 | Too many requests |

## Project structure

- `app/Http/Controllers/Api`: API controllers
- `app/Http/Requests`: request validation
- `app/Http/Resources`: JSON response formatting
- `app/Services/CurrencyConverter.php`: currency conversion logic
- `database/`: migrations, factories and seeders
- `public/openapi.yaml`: OpenAPI specification
- `tests/`: feature and unit tests

## Security

- Token authentication with Laravel Sanctum, with tokens expiring after 24 hours.
- Rate limiting: 5 login attempts per minute and 60 requests per minute for the rest of the API.
- Input validation with Form Requests, and mass assignment restricted with `$fillable`.
- Responses go through API Resources, so only the defined fields are exposed.
- Products are soft deleted.
- `APP_DEBUG` should be set to `false` in production.

## Tests

```bash
php artisan test
```

Tests run on an in-memory SQLite database and cover authentication, the products CRUD, product prices and the currency conversion.