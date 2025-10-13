<?php

namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class PlaceController extends Controller
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
     * Ambil semua tempat (cached & authorized).
     * Sesuai dengan: @GET("api/places")
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
        $places = Cache::remember('places_all', 60, function () {
            return Place::select('id', 'name', 'idCategory', 'images')->orderBy('id')->get();
        });

        return response()->json($places, Response::HTTP_OK);
    }

    /**
     * Ambil 1 tempat berdasarkan ID.
     * --- INI ADALAH IMPLEMENTASI UNTUK: @GET("api/places/{id}") ---
     */
    public function show(Request $request, $id)
    {
        if (!$this->isAuthorized($request)) {
            return response()->json(
                ['message' => 'Unauthorized: Invalid API Key'],
                Response::HTTP_UNAUTHORIZED
            );
        }

        // Cache tiap tempat secara individu
        $place = Cache::remember("place_{$id}", 60, function () use ($id) {
            // Mengambil semua kolom untuk detail
            return Place::find($id);
        });

        if (!$place) {
            return response()->json(
                ['message' => 'Place not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        return response()->json($place, Response::HTTP_OK);
    }

    /**
     * Ambil semua tempat berdasarkan ID Kategori.
     * Sesuai dengan: @GET("api/places/category/{id}")
     */
    public function getByCategory(Request $request, $categoryId)
    {
        if (!$this->isAuthorized($request)) {
            return response()->json(
                ['message' => 'Unauthorized: Invalid API Key'],
                Response::HTTP_UNAUTHORIZED
            );
        }

        // Cache hasil query berdasarkan categoryId selama 60 detik
        $places = Cache::remember("places_by_category_{$categoryId}", 60, function () use ($categoryId) {
            return Place::select('id', 'name', 'idCategory', 'images')
                ->where('idCategory', $categoryId)
                ->orderBy('id')
                ->get();
        });

        // Jika tidak ada tempat, akan mengembalikan array kosong, yang merupakan respons valid.
        return response()->json($places, Response::HTTP_OK);
    }
}

