<?php

namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class PlaceController extends Controller
{
    // ... (Fungsi isAuthorized dan show tidak perlu diubah, sudah benar) ...
    private function isAuthorized(Request $request): bool
    {
        $clientKey = $request->bearerToken();
        $validKey = env('LICENSE_API_KEY');

        return $clientKey && hash_equals($validKey, $clientKey);
    }

    /**
     * Ambil semua tempat (cached & authorized).
     */
    public function index(Request $request)
    {
        if (!$this->isAuthorized($request)) {
            return response()->json(
                ['message' => 'Unauthorized: Invalid API Key'],
                Response::HTTP_UNAUTHORIZED
            );
        }

        $places = Cache::remember('places_all', 60, function () {
            // DIUBAH: Hapus select() untuk mengambil semua kolom
            // atau tambahkan field baru Anda: ->select('id', 'name', ..., 'field_baru')
            return Place::orderBy('id')->get();
        });

        return response()->json($places, Response::HTTP_OK);
    }

    /**
     * Ambil 1 tempat berdasarkan ID.
     */
    public function show(Request $request, $id)
    {
        // ... Tidak ada perubahan, method ini sudah benar ...
        if (!$this->isAuthorized($request)) {
            return response()->json(['message' => 'Unauthorized: Invalid API Key'], Response::HTTP_UNAUTHORIZED);
        }
        $place = Cache::remember("place_{$id}", 60, function () use ($id) {
            return Place::find($id);
        });
        if (!$place) {
            return response()->json(['message' => 'Place not found'], Response::HTTP_NOT_FOUND);
        }
        return response()->json($place, Response::HTTP_OK);
    }


    /**
     * Ambil semua tempat berdasarkan ID Kategori.
     */
    public function getByCategory(Request $request, $categoryId)
    {
        if (!$this->isAuthorized($request)) {
            return response()->json(
                ['message' => 'Unauthorized: Invalid API Key'],
                Response::HTTP_UNAUTHORIZED
            );
        }

        $places = Cache::remember("places_by_category_{$categoryId}", 60, function () use ($categoryId) {
            // DIUBAH: Hapus select() untuk mengambil semua kolom
            return Place::where('idCategory', $categoryId)
                ->orderBy('id')
                ->get();
        });

        return response()->json($places, Response::HTTP_OK);
    }
}