<?php

namespace App\Repositories;

use App\Models\Category;

class CategoryRepository
{
    public function create(array $category)
    {
        return Category::create($category);
    }
}
