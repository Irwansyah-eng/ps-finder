<?php

namespace App\Http\Controllers;

use App\Services\SawService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RentalController extends Controller
{
    public function index()
    {
        return view('home', ['criteria' => SawService::CRITERIA]);
    }

    public function ranking(Request $request, SawService $saw): JsonResponse
    {
        $items = $saw->rank((array) $request->query('w', []));

        return response()->json([
            'total' => array_sum(array_column($items, 'n')),
            'items' => $items,
        ]);
    }
}
