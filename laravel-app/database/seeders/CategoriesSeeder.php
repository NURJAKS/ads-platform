<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoriesSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            'Одежда и обувь' => 'clothing',
            'Аксессуары' => 'accessories',
            'Часы и украшения' => 'watches-jewelry',
            'Детские товары' => 'kids',
            'Косметика и парфюм' => 'cosmetics',
        ];

        foreach ($categories as $name => $slug) {
            Category::firstOrCreate(
                ['slug' => $slug],
                ['name' => $name]
            );
        }
    }
}
