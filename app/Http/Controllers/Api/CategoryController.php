<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    public function index()
    {
        $categories = Category::latest()->get();

        return response()->json($categories);
    }


    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|unique:categories',
        ]);

        $category = Category::create([
            'nama' => $request->nama,
        ]);

        return response()->json([
            'message'  => 'Kategori berhasil dibuat',
            'category' => $category,
        ], 201);
    }


    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Kategori tidak ditemukan'], 404);
        }

        return response()->json($category);
    }


    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Kategori tidak ditemukan'], 404);
        }

        $request->validate([
            'nama' => 'required|string|unique:categories,nama,' . $id, 
        ]);

        $category->update(['nama' => $request->nama]);

        return response()->json([
            'message'  => 'Kategori berhasil diupdate',
            'category' => $category,
        ]);
    }


    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Kategori tidak ditemukan'], 404);
        }

        $category->delete();

        return response()->json(['message' => 'Kategori berhasil dihapus']);
    }
}
