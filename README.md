# Products API

REST API для управления товарами с фильтрацией, сортировкой и пагинацией.

## Требования

- PHP 8.2+
- Laravel 12+
- MySQL 8.0+

## Установка

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Настрой `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

```bash
php artisan migrate
php artisan db:seed  # если есть сидеры
```

## Эндпоинты

### GET /api/products

Получить список товаров.

**Query параметры:**

| Параметр | Тип | Описание | Пример |
|---|---|---|---|
| `q` | string | Поиск по названию | `?q=телефон` |
| `category_id` | integer | Фильтр по категории | `?category_id=3` |
| `price_from` | number | Цена от | `?price_from=100` |
| `price_to` | number | Цена до | `?price_to=5000` |
| `in_stock` | boolean | Наличие на складе | `?in_stock=true` |
| `sort` | string | Сортировка | `?sort=price_asc` |
| `limit` | integer | Кол-во записей (макс. 100, по умолчанию 20) | `?limit=10` |
| `offset` | integer | Смещение (по умолчанию 0) | `?offset=20` |

**Допустимые значения `sort`:**
- `price_asc` — цена по возрастанию
- `price_desc` — цена по убыванию
- `name_asc` — название А-Я
- `name_desc` — название Я-А

**Пример запроса:**
```
GET /api/products?category_id=2&price_from=500&price_to=3000&in_stock=true&sort=price_asc&limit=10&offset=0
```

**Пример ответа:**
```json
{
    "products": [
        {
            "id": 1,
            "name": "Товар 1",
            "price": "999.00",
            "category_id": 2,
            "in_stock": true,
            "rating": 4.5,
            "created_at": "2026-04-21T10:00:00",
            "updated_at": "2026-04-21T10:00:00"
        }
    ],
    "total": 42,
    "limit": 10,
    "offset": 0,
    "has_more": true
}
```

## Пагинация

API использует `limit/offset` пагинацию.

Следующая страница:
```
GET /api/products?limit=10&offset=10
GET /api/products?limit=10&offset=20
```

`has_more: true` означает что есть ещё записи.

## Ошибки

| HTTP код | Описание |
|---|---|
| `422` | Невалидный параметр запроса |
| `500` | Внутренняя ошибка сервера |

**422 — невалидный параметр:**
```json
{
    "error": "Invalid sort parameter"
}
```

**500 — внутренняя ошибка:**
```json
{
    "error": "Server error"
}
```

Если возникает при проблемах с базой данных, недоступности сервиса или необработанном исключении:
В `APP_DEBUG=true` ответ будет содержать полный стектрейс (только для локальной разработки).

## Структура проекта

```
app/
├── Http/Controllers/
│   └── ProductController.php   # принимает запрос, возвращает ответ
├── Services/
│   └── ProductService.php      # бизнес-логика, валидация, фильтрация
└── Models/
    ├── Product.php              # модель товара
    └── Category.php             # модель категории

database/
├── migrations/                  # миграции таблиц products, categories
└── factories/                   # фабрики для тестовых данных

routes/
└── api.php                      # маршруты API
```

## Тестовые данные

```bash
php artisan tinker
> Product::factory()->count(100)->create();
```
