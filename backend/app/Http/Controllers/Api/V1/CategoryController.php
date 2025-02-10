<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CategoryIndexRequest;
use App\Http\Requests\Api\V1\CategoryRequest;
use App\Http\Resources\Api\V1\CategoryResource;
use App\Http\Resources\Api\V1\CategoryResourceCollection;
use App\Services\CategoryService;

class CategoryController extends Controller
{
    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function store(CategoryRequest $request): CategoryResource
    {
        $category = $this->categoryService->createCategory($request->validated(), $request->user()->id);
        return new CategoryResource($category);
    }

    public function index(CategoryIndexRequest $request): CategoryResourceCollection
    {
        $categories = $this->categoryService->getCategories($request->validated(), $request->user()->id);
        return new CategoryResourceCollection($categories);
    }
}
