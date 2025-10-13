<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    /**
     * Verifikasi API Key dari Header Authorization.
     */
    private function isAuthorized(Request $request): bool
    {
        $clientKey = $request->bearerToken(); // Laravel sudah sediakan ini
        $validKey = env('LICENSE_API_KEY');

        return $clientKey && hash_equals($validKey, $clientKey);
    }

    /**
     * Ambil semua kategori (cached & authorized).
     */
    public function index(Request $request)
    {
        if (!$this->isAuthorized($request)) {
            return response()->json(
                ['message' => 'Unauthorized: Invalid API Key'],
                Response::HTTP_UNAUTHORIZED
            );
        }

        // Cache selama 60 detik, hindari query berulang
        $categories = Cache::remember('categories_all', 60, function () {
            return Category::select('id', 'name', 'images')->orderBy('id')->get();
        });

        return response()->json($categories, Response::HTTP_OK);
    }

    /**
     * Ambil 1 kategori berdasarkan ID.
     */
    public function show(Request $request, $id)
    {
        if (!$this->isAuthorized($request)) {
            return response()->json(
                ['message' => 'Unauthorized: Invalid API Key'],
                Response::HTTP_UNAUTHORIZED
            );
        }

        // Cache tiap kategori secara individu
        $category = Cache::remember("category_{$id}", 60, function () use ($id) {
            return Category::select('id', 'name', 'images')->find($id);
        });

        if (!$category) {
            return response()->json(
                ['message' => 'Category not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        return response()->json($category, Response::HTTP_OK);
    }
}
