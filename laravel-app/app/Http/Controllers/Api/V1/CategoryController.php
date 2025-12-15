<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    // PUBLIC — список категорий
    public function index()
    {
        return ApiResponse::success(Category::orderBy('name')->get());
    }

    // ADMIN — создать категорию
    public function store(Request $r)
    {
        if (Auth::user()->role !== 'admin') {
            return ApiResponse::error('Forbidden', null, 403);
        }

        $r->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
        ]);

        $slug = $r->slug ?? Str::slug($r->name);

        $cat = Category::create([
            'name' => $r->name,
            'slug' => $slug
        ]);

        return ApiResponse::success($cat, 'Category created successfully', 201);
    }

    // ADMIN — обновить категорию
    public function update(Request $r, $id)
    {
        if (Auth::user()->role !== 'admin') {
            return ApiResponse::error('Forbidden', null, 403);
        }

        $category = Category::findOrFail($id);

        $r->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => "sometimes|string|max:255|unique:categories,slug,{$id}",
        ]);

        $category->update([
            'name' => $r->name ?? $category->name,
            'slug' => $r->slug ?? $category->slug,
        ]);

        return ApiResponse::success($category, 'Category updated successfully');
    }

    // ADMIN — удалить категорию
    public function destroy($id)
    {
        if (Auth::user()->role !== 'admin') {
            return ApiResponse::error('Forbidden', null, 403);
        }

        Category::findOrFail($id)->delete();

        return ApiResponse::success(null, 'Category deleted successfully');
    }
}
