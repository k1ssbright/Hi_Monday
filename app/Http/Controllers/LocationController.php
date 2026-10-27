<?php

namespace App\Http\Controllers;

use App\Models\Address\Amphures;
use App\Models\Address\Provinces;
use App\Models\Address\Tambons;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    private function getAddressList()
    {
        return Tambons::query()
            ->select(
                'tambons.id as tambon_id',
                'amphures.id as amphure_id',
                'provinces.id as province_id',
                'provinces.name_th as pv_th',
                'provinces.name_en as pv_en',
                'amphures.name_th as ap_th',
                'amphures.name_en as ap_en',
                'tambons.name_th as tb_th',
                'tambons.name_en as tb_en',
                'tambons.zip_code'
            )
            ->join('amphures', 'amphures.id', '=', 'tambons.amphure_id')
            ->join('provinces', 'provinces.id', '=', 'amphures.province_id')
            ->orderBy('provinces.name_th')
            ->orderBy('amphures.name_th')
            ->orderBy('tambons.name_th')
            ->orderBy('tambons.zip_code')
            ->get();
    }

    public function thaiLocationInter(): JsonResponse
    {
        $list = [];

        foreach ($this->getAddressList() as $row) {
            $base = [
                'tambon_id' => $row->tambon_id,
                'amphure_id' => $row->amphure_id,
                'province_id' => $row->province_id,
                'zip_code' => $row->zip_code,
            ];

            $list[] = array_merge($base, [
                'tambon_name' => $row->tb_th,
                'amphure_name' => $row->ap_th,
                'province_name' => $row->pv_th,
                'address' => "{$row->tb_th}, {$row->ap_th}, {$row->pv_th}, {$row->zip_code}",
            ]);

            $list[] = array_merge($base, [
                'tambon_name' => $row->tb_en,
                'amphure_name' => $row->ap_en,
                'province_name' => $row->pv_en,
                'address' => "{$row->tb_en}, {$row->ap_en}, {$row->pv_en}, {$row->zip_code}",
            ]);
        }

        return response()->json($list);
    }

    public function provinces(): JsonResponse
    {
        $provinces = Provinces::orderBy('name_th')->get(['id', 'name_th', 'name_en', 'geography_id']);

        return response()->json($provinces);
    }

    public function amphures(Request $request): JsonResponse
    {
        $amphures = Amphures::query()
            ->when($request->province_id, fn ($q) => $q->where('province_id', $request->province_id))
            ->orderBy('name_th')
            ->get(['id', 'name_th', 'name_en', 'province_id']);

        return response()->json($amphures);
    }

    public function tambons(Request $request): JsonResponse
    {
        $tambons = Tambons::query()
            ->when($request->amphure_id, fn ($q) => $q->where('amphure_id', $request->amphure_id))
            ->orderBy('name_th')
            ->get(['id', 'name_th', 'name_en', 'amphure_id', 'zip_code']);

        return response()->json($tambons);
    }
}
