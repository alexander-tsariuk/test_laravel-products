<?php

namespace App\Services\API;

use App\Models\Product;
use Exception;

class ProductService
{
    /**
     * Набор свойств сортировки
     * @var array|array[]
     */
    private array $sortMap = [
        'price_asc'     => ['price', 'asc'],
        'price_desc'    => ['price', 'desc'],
        'rating_desc'   => ['rating', 'desc'],
        'newest'     => ['id', 'desc'],
    ];

    /**
     * Метод валидации параметров запроса
     * @throws Exception
     */
    public function validateQueryParams(array $params): void
    {
        if (isset($params['sort']) && !array_key_exists($params['sort'], $this->sortMap)) {
            throw new Exception('Invalid sort parameter', 422);
        }

        if (isset($params['price_from']) && !is_numeric($params['price_from'])) {
            throw new Exception('Invalid price_from parameter', 422);
        }

        if (isset($params['price_to']) && !is_numeric($params['price_to'])) {
            throw new Exception('Invalid price_to parameter', 422);
        }

        if (isset($params['in_stock'])) {
            $val = filter_var($params['in_stock'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($val === null) {
                throw new Exception('Invalid in_stock parameter', 422);
            }
        }

        if (isset($params['category_id']) && !ctype_digit((string) $params['category_id'])) {
            throw new Exception('Invalid category_id parameter', 422);
        }
    }

    /**
     * Метод получения списка товаров
     * @throws Exception
     */
    public function getProducts(array $params): array
    {
        $this->validateQueryParams($params);

        $query = Product::query();

        if (!empty($params['q'])) {
            $query->where('name', 'like', "%{$params['q']}%");
        }

        if (!empty($params['price_from'])) {
            $query->where('price', '>=', $params['price_from']);
        }

        if (!empty($params['price_to'])) {
            $query->where('price', '<=', $params['price_to']);
        }

        if (!empty($params['category_id'])) {
            $query->where('category_id', $params['category_id']);
        }

        if (isset($params['in_stock'])) {
            $inStock = filter_var($params['in_stock'], FILTER_VALIDATE_BOOLEAN);
            $query->where('in_stock', $inStock);
        }

        if (!empty($params['sort'])) {
            $query->orderBy(...$this->sortMap[$params['sort']]);
        }

        $limit  = min((int) ($params['limit'] ?? 20), 100);
        $offset = (int) ($params['offset'] ?? 0);

        $total    = $query->count();
        $products = $query->limit($limit)->offset($offset)->get();

        return [
            'products' => $products,
            'total'    => $total,
            'limit'    => $limit,
            'offset'   => $offset,
            'has_more' => ($offset + $limit) < $total,
        ];
    }
}
