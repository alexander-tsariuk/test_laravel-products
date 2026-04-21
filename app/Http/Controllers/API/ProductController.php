<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\API\ProductService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class ProductController extends Controller
{

    public function __construct(private ProductService $productService) {}

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $result = $this->productService->getProducts($request->query());

            return response()->json([
                'result' => $result,
                'error' =>  false
            ]);

        } catch (Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'error' => true
            ], $exception->getCode());
        }
    }
}
