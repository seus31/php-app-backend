<?php

namespace App\Services;

use App\Repositories\CategoryRepository;

class CategoryService
{
    protected CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function createCategory(array $category)
    {
        return $this->categoryRepository->create($category);
    }

    public function getCategories(array $categoryIndexConditions)
    {
        if (empty($categoryIndexConditions['page'])) {
            $categoryIndexConditions['page'] = config('const.pagination.page');
        }

        if (empty($categoryIndexConditions['per_page'])) {
            $categoryIndexConditions['per_page'] = config('const.pagination.per_page');
        }

        return $this->categoryRepository->getCategories($categoryIndexConditions);
    }
}
