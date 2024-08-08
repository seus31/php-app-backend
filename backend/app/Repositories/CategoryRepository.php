<?php

namespace App\Repositories;

use App\Models\Category;

class CategoryRepository
{
    public function create(array $category)
    {
        return Category::create($category);
    }

    public function getCategories(array $categoryIndexConditions)
    {
        return Category::paginate($categoryIndexConditions['per_page'], ['*'], 'page', $categoryIndexConditions['page']);
    }
}
