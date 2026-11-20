<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\Rental;
use App\Services\SawService;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    public const KECAMATAN = [
        'Loa Janan Ilir', 'Palaran', 'Sambutan', 'Samarinda Ilir', 'Samarinda Kota',
        'Samarinda Seberang', 'Samarinda Ulu', 'Samarinda Utara', 'Sungai Kunjang', 'Sungai Pinang',
    ];

    public function create()
    {
        return view('survei', [
            'rentals' => Rental::orderBy('nama')->get(),
            'criteria' => SawService::CRITERIA + ['puas' => 'Kepuasan keseluruhan'],
            'kecamatan' => self::KECAMATAN,
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'rental_id' => 'required|exists:rentals,id',
            'kecamatan' => 'nullable|string|max:60',
            'puas' => 'required|integer|between:1,5',
        ];
        foreach (array_keys(SawService::CRITERIA) as $k) {
            $rules[$k] = 'required|integer|between:1,5';
        }

        Rating::create($request->validate($rules));

        return redirect()->route('survei')->with('ok', 'Terima kasih, penilaianmu sudah tersimpan.');
    }
}
