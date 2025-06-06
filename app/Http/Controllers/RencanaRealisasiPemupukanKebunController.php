<?php

namespace App\Http\Controllers;

use App\Helpers\AuthHelper;
use App\Models\MasterData;
use App\Models\RencanaRealisasiPemupukan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class RencanaRealisasiPemupukanKebunController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pageTitle = trans('global-message.list_form_title', ['form' => trans('Rencana Realisasi Pemupukan Data')]);
        $auth_user = AuthHelper::authSession();
        $assets = ['data-table'];

        // Cache regionals for 24 hours
        $regionals = \Cache::remember('rencana_realisasi_regionals', 60, function () {
            return MasterData::select('rpc')->distinct()->pluck('rpc');
        });

        // Define defaults
        if ($auth_user->regional !== 'head_office') {
            $default_regional = $auth_user->regional;
            $default_kebun = $auth_user->kebun;
        } else {
            $default_regional = $request->input('regional');
            $default_kebun = $request->input('kebun');
        }

        if ($request->ajax()) {
            $cacheKey = "rencana_realisasi_{$default_regional}_{$default_kebun}";

            $data = \Cache::remember($cacheKey, 10, function () use ($auth_user, $request) {
                $query = RencanaRealisasiPemupukan::query();

                // Apply regional filter
                if ($regional = $request->input('regional')) {
                    $query->where('regional', $regional);
                }

                // Restrict by user role if not head_office
                if ($auth_user->regional !== 'head_office') {
                    $query->where('regional', $auth_user->regional);
                }

                return $query->select([
                    'regional',
                    'kebun',
                    'rencana_plant',
                    DB::raw("SUM(rencana_semester_1) as rencana_semester_1"),
                    DB::raw("SUM(realisasi_semester_1) as realisasi_semester_1"),
                    DB::raw("SUM(rencana_semester_2) as rencana_semester_2"),
                    DB::raw("SUM(realisasi_semester_2) as realisasi_semester_2"),
                    DB::raw("SUM(rencana_total) as rencana_total"),
                    DB::raw("SUM(realisasi_total) as realisasi_total"),
                ])->groupBy('regional', 'kebun', 'rencana_plant')->get();
            });


            return DataTables::of($data)
                ->addColumn('rencana_semester_1', fn($row) => number_format($row->rencana_semester_1, 0, ',', '.') . ' Kg')
                ->addColumn('realisasi_semester_1', fn($row) => number_format($row->realisasi_semester_1, 0, ',', '.') . ' Kg')
                ->addColumn('percentage_semester_1', fn($row) => $row->rencana_semester_1 > 0
                    ? number_format(($row->realisasi_semester_1 / $row->rencana_semester_1) * 100, 2, ',', '.') . '%'
                    : '0%')
                ->addColumn('rencana_semester_2', fn($row) => number_format($row->rencana_semester_2, 0, ',', '.') . ' Kg')
                ->addColumn('realisasi_semester_2', fn($row) => number_format($row->realisasi_semester_2, 0, ',', '.') . ' Kg')
                ->addColumn('percentage_semester_2', fn($row) => $row->rencana_semester_2 > 0
                    ? number_format(($row->realisasi_semester_2 / $row->rencana_semester_2) * 100, 2, ',', '.') . '%'
                    : '0%')
                ->addColumn('rencana_total', fn($row) => number_format($row->rencana_total, 0, ',', '.') . ' Kg')
                ->addColumn('realisasi_total', fn($row) => number_format($row->realisasi_total, 0, ',', '.') . ' Kg')
                ->addColumn('percentage_total', fn($row) => $row->rencana_total > 0
                    ? number_format(($row->realisasi_total / $row->rencana_total) * 100, 2, ',', '.') . '%'
                    : '0%')
                ->toJson();
        }

        return view('global.datatable-rencana-realisasi-kebun', compact(
            'pageTitle',
            'auth_user',
            'assets',
            'regionals',
            'default_regional'
        ));
    }

    public function print(Request $request)
    {
        $auth_user = AuthHelper::authSession();
        $pageTitle = trans('global-message.list_form_title', ['form' => trans('Rencana Realisasi Pemupukan Data')]);

        $query = RencanaRealisasiPemupukan::query();

        // Apply regional filter from request
        if ($regional = $request->input('regional')) {
            $query->where('regional', $regional);
        }

        // Restrict by user role if not head_office
        if ($auth_user->regional !== 'head_office') {
            $query->where('regional', $auth_user->regional);
        }

        $data = $query->select([
            'regional',
            'kebun',
            'rencana_plant',
            DB::raw("SUM(rencana_semester_1) as rencana_semester_1"),
            DB::raw("SUM(realisasi_semester_1) as realisasi_semester_1"),
            DB::raw("SUM(rencana_semester_2) as rencana_semester_2"),
            DB::raw("SUM(realisasi_semester_2) as realisasi_semester_2"),
            DB::raw("SUM(rencana_total) as rencana_total"),
            DB::raw("SUM(realisasi_total) as realisasi_total"),
        ])->groupBy('regional', 'kebun', 'rencana_plant')->get();

        return view('rencana-realisasi-pemupukan-kebun.print-rencana-realisasi-kebun', compact('pageTitle', 'data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function upload()
    {}

    // public function fetchData(Request $request)
    // {
    //     // Filters from request
    //     $filters = $request->only(['regional', 'kebun', 'afdeling', 'tahun_tanam', 'jenis_pupuk']);

    //     // Query for Pemupukan
    //     $pemupukanQuery = Pemupukan::query();
    //     foreach ($filters as $field => $value) {
    //         if ($value) {
    //             $pemupukanQuery->where($field, $value);
    //         }
    //     }

    //     // Query for RencanaPemupukan
    //     $rencanaQuery = RencanaPemupukan::query();
    //     foreach ($filters as $field => $value) {
    //         if ($value) {
    //             $rencanaQuery->where($field, $value);
    //         }
    //     }

    //     // Check if the jenis_pupuk filter is applied
    //     if (!empty($filters['jenis_pupuk'])) {
    //         $pemupukanSum = $pemupukanQuery->select('jenis_pupuk')
    //             ->selectRaw('SUM(jumlah_pupuk) as total_kg')
    //             ->groupBy('jenis_pupuk')
    //             ->get();

    //         $rencanaSum = $rencanaQuery->select('jenis_pupuk')
    //             ->selectRaw("
    //                 jenis_pupuk,
    //                 SUM(CASE WHEN semester_pemupukan = 1 THEN jumlah_pupuk ELSE 0 END) as semester_1,
    //                 SUM(CASE WHEN semester_pemupukan = 2 THEN jumlah_pupuk ELSE 0 END) as semester_2
    //             ")
    //             ->groupBy('jenis_pupuk')
    //             ->get();

    //         // Transform RencanaPemupukan data for structured response
    //         $rencanaSum = $rencanaSum->map(function ($item) {
    //             return [
    //                 'jenis_pupuk' => $item->jenis_pupuk,
    //                 'semester 1' => ['total_kg' => $item->semester_1],
    //                 'semester 2' => ['total_kg' => $item->semester_2],
    //             ];
    //         });

    //         $data = [
    //             'pemupukan' => $pemupukanSum,
    //             'rencana_pemupukan' => $rencanaSum,
    //         ];
    //     } elseif (!empty($filters['afdeling'])) {
    //         // If the afdeling filter is applied
    //         $pemupukanSum = $pemupukanQuery->select('afdeling')
    //             ->selectRaw('SUM(jumlah_pupuk) as total_kg')
    //             ->groupBy('afdeling')
    //             ->get();

    //         $rencanaSum = $rencanaQuery->select('afdeling')
    //             ->selectRaw("
    //                 afdeling,
    //                 SUM(CASE WHEN semester_pemupukan = 1 THEN jumlah_pupuk ELSE 0 END) as semester_1,
    //                 SUM(CASE WHEN semester_pemupukan = 2 THEN jumlah_pupuk ELSE 0 END) as semester_2
    //             ")
    //             ->groupBy('afdeling')
    //             ->get();

    //         $rencanaSum = $rencanaSum->map(function ($item) {
    //             return [
    //                 'afdeling' => $item->afdeling,
    //                 'semester 1' => ['total_kg' => $item->semester_1],
    //                 'semester 2' => ['total_kg' => $item->semester_2],
    //             ];
    //         });

    //         $data = [
    //             'pemupukan' => $pemupukanSum,
    //             'rencana_pemupukan' => $rencanaSum,
    //         ];
    //     } elseif (!empty(array_filter($filters))) {
    //         // If other filters are applied (e.g., regional, kebun)
    //         $pemupukanSum = $pemupukanQuery->select('kebun')
    //             ->selectRaw('SUM(jumlah_pupuk) as total_kg')
    //             ->groupBy('kebun')
    //             ->get();

    //         $rencanaSum = $rencanaQuery->select('kebun')
    //             ->selectRaw("
    //                 kebun,
    //                 SUM(CASE WHEN semester_pemupukan = 1 THEN jumlah_pupuk ELSE 0 END) as semester_1,
    //                 SUM(CASE WHEN semester_pemupukan = 2 THEN jumlah_pupuk ELSE 0 END) as semester_2
    //             ")
    //             ->groupBy('kebun')
    //             ->get();

    //         $rencanaSum = $rencanaSum->map(function ($item) {
    //             return [
    //                 'kebun' => $item->kebun,
    //                 'semester 1' => ['total_kg' => $item->semester_1],
    //                 'semester 2' => ['total_kg' => $item->semester_2],
    //             ];
    //         });

    //         $data = [
    //             'pemupukan' => $pemupukanSum,
    //             'rencana_pemupukan' => $rencanaSum,
    //         ];
    //     } else {
    //         // Aggregate by regional if no filters are provided
    //         $pemupukanSum = Pemupukan::select('regional')
    //             ->selectRaw('SUM(jumlah_pupuk) as total_kg')
    //             ->groupBy('regional')
    //             ->get();

    //         $rencanaSum = RencanaPemupukan::select('regional')
    //             ->selectRaw("
    //                 regional,
    //                 SUM(CASE WHEN semester_pemupukan = 1 THEN jumlah_pupuk ELSE 0 END) as semester_1,
    //                 SUM(CASE WHEN semester_pemupukan = 2 THEN jumlah_pupuk ELSE 0 END) as semester_2
    //             ")
    //             ->groupBy('regional')
    //             ->get();

    //         $rencanaSum = $rencanaSum->map(function ($item) {
    //             return [
    //                 'regional' => $item->regional,
    //                 'semester 1' => ['total_kg' => $item->semester_1],
    //                 'semester 2' => ['total_kg' => $item->semester_2],
    //             ];
    //         });

    //         $data = [
    //             'pemupukan' => $pemupukanSum,
    //             'rencana_pemupukan' => $rencanaSum,
    //         ];
    //     }

    //     return response()->json($data);
    // }
    // public function fetchData(Request $request)
    // {
    //     $query = Pemupukan::query()
    //         ->join('rencana_pemupukan', 'pemupukan.regional', '=', 'rencana_pemupukan.regional')
    //         ->select(
    //             'pemupukan.id',
    //             'pemupukan.regional',
    //             'pemupukan.kebun',
    //             'pemupukan.afdeling',
    //             'pemupukan.blok',
    //             'pemupukan.tahun_tanam',
    //             'pemupukan.jenis_pupuk',
    //             'pemupukan.jumlah_pupuk',
    //             'rencana_pemupukan.semester_pemupukan'
    //         )
    //         ->paginate(100); // Paginate with 100 rows per page

    //     return response()->json($query);
    // }

    // public function getPemupukanData($request)
    // {
    //     // API endpoint
    //     $url = route('rencana-realisasi.fetchdata');

    //     // Send GET request to the API with query parameters
    //     $response = Http::get($url, [
    //         'regional' => $request->get('regional'),
    //         'kebun' => $request->get('kebun'),
    //         'jenis_pupuk' => $request->get('jenis_pupuk'),
    //         'afdeling' => $request->get('afdeling'),
    //         'tahun_tanam' => $request->get('tahun_tanam'),
    //     ]);

    //     // Check if the request was successful
    //     if ($response->successful()) {
    //         $data = $response->json();

    //         // Return data structured with 'pemupukan' and 'rencana_pemupukan'
    //         return [
    //             'pemupukan' => $data['pemupukan'] ?? [],
    //             'rencana_pemupukan' => $data['rencana_pemupukan'] ?? [],
    //         ];
    //     }

    //     // If the API request fails, return empty data with an error message
    //     return [
    //         'pemupukan' => [],
    //         'rencana_pemupukan' => [],
    //         'error' => 'Failed to fetch data from the API',
    //     ];
    // }
}
