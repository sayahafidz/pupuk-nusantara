<?php

namespace App\Http\Controllers;

use App\DataTables\RencanaRealisasiPemupukanDataTable;
use App\Helpers\AuthHelper;
use App\Models\Pemupukan;
use App\Models\RencanaPemupukan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RencanaRealisasiPemupukanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(RencanaRealisasiPemupukanDataTable $dataTable)
    {
        $pageTitle = trans('global-message.list_form_title', ['form' => trans('Rencana Realisasi Pemupukan Data')]);
        $auth_user = AuthHelper::authSession();
        $assets = ['data-table'];
        $headerAction = '<a href="' . route('rencana-realisasi-pemupukan.create') . '" class="btn btn-sm btn-primary">Add</a>'
            . ' <a href="' . route('rencana-realisasi-pemupukan.upload') . '" class="btn btn-sm btn-success">Upload</a>';

        $regionals = RencanaPemupukan::select('regional')->distinct()->pluck('regional');
        $kebuns = RencanaPemupukan::select('kebun')->distinct()->pluck('kebun');
        $afdelings = RencanaPemupukan::select('afdeling')->distinct()->pluck('afdeling');
        $tahunTanams = RencanaPemupukan::select('tahun_tanam')->distinct()->pluck('tahun_tanam');
        $jenisPupuks = RencanaPemupukan::select('jenis_pupuk')->distinct()->pluck('jenis_pupuk');

        return $dataTable->render('global.datatable-rencana-realisasi', compact(
            'pageTitle',
            'auth_user',
            'assets',
            'headerAction',
            'regionals',
            'kebuns',
            'afdelings',
            'tahunTanams',
            'jenisPupuks'
        ));
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

    public function upload() {}

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
    public function fetchData(Request $request)
    {
        $query = Pemupukan::query()
            ->join('rencana_pemupukan', 'pemupukan.regional', '=', 'rencana_pemupukan.regional')
            ->select(
                'pemupukan.id',
                'pemupukan.regional',
                'pemupukan.kebun',
                'pemupukan.afdeling',
                'pemupukan.blok',
                'pemupukan.tahun_tanam',
                'pemupukan.jenis_pupuk',
                'pemupukan.jumlah_pupuk',
                'rencana_pemupukan.semester_pemupukan'
            )
            ->paginate(100); // Paginate with 100 rows per page

        return response()->json($query);
    }


    public function getPemupukanData($request)
    {
        // API endpoint
        $url = route('rencana-realisasi.fetchdata');

        // Send GET request to the API with query parameters
        $response = Http::get($url, [
            'regional' => $request->get('regional'),
            'kebun' => $request->get('kebun'),
            'jenis_pupuk' => $request->get('jenis_pupuk'),
            'afdeling' => $request->get('afdeling'),
            'tahun_tanam' => $request->get('tahun_tanam'),
        ]);

        // Check if the request was successful
        if ($response->successful()) {
            $data = $response->json();

            // Return data structured with 'pemupukan' and 'rencana_pemupukan'
            return [
                'pemupukan' => $data['pemupukan'] ?? [],
                'rencana_pemupukan' => $data['rencana_pemupukan'] ?? [],
            ];
        }

        // If the API request fails, return empty data with an error message
        return [
            'pemupukan' => [],
            'rencana_pemupukan' => [],
            'error' => 'Failed to fetch data from the API',
        ];
    }
}
