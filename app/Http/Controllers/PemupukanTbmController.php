<?php

namespace App\Http\Controllers;

use App\Helpers\AuthHelper;
use App\Models\JenisPupuk;
use App\Models\MasterDataTbm;
use App\Models\PemupukanTbm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\DataTables;

class PemupukanTbmController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pageTitle = trans('global-message.list_form_title', ['form' => trans('Pemupukan Data')]);
        $auth_user = AuthHelper::authSession();
        $assets = ['data-table'];
        $headerAction = '<a href="' . route('input-pemupukan') . '" class="btn btn-sm btn-primary" role="button">Add Pemupukan</a>'
        . ' <a href="' . route('pemupukan.upload') . '" class="btn btn-sm btn-success" role="button">Upload Pemupukan File</a>';

        // Cache dropdown values for 24 hours
        $regionals = Cache::remember('pemupukan_regionals', 60, fn() =>
            MasterDataTbm::select('regional')->distinct()->pluck('regional')
        );
        $kebuns = Cache::remember('pemupukan_kebuns', 60, fn() =>
            PemupukanTbm::select('kebun')->distinct()->pluck('kebun')
        );
        $afdelings = Cache::remember('pemupukan_afdelings', 60, fn() =>
            PemupukanTbm::select('afdeling')->distinct()->pluck('afdeling')
        );
        $tahunTanams = Cache::remember('pemupukan_tahun_tanams', 60, fn() =>
            PemupukanTbm::select('tahun_tanam')->distinct()->pluck('tahun_tanam')
        );
        $jenisPupuks = Cache::remember('pemupukan_jenis_pupuks', 60, fn() =>
            PemupukanTbm::select('jenis_pupuk')->distinct()->pluck('jenis_pupuk')
        );

        // Define defaults outside AJAX block
        if ($auth_user->regional !== 'head_office') {
            $default_regional = $auth_user->regional;
            $default_kebun = $auth_user->kebun;
        } else {
            $default_regional = $request->input('regional');
            $default_kebun = $request->input('kebun');
        }

        if ($request->ajax()) {
            // Generate a unique cache key based on the request parameters
            $cacheKey = 'rencana_pemupukan_data_tbm_' . md5(json_encode($request->all()));

            // Retrieve data from cache or query the database
            $data = Cache::remember($cacheKey, 60, function () use ($request, $auth_user, $default_regional) {
                $query = PemupukanTbm::query()
                    ->select([
                        'id',
                        'regional',
                        'kebun',
                        'afdeling',
                        'blok',
                        'tahun_tanam',
                        'jenis_pupuk',
                        'jumlah_pupuk',
                        'tgl_pemupukan',
                        'plant',
                    ]);

                // Apply role-based filtering
                if ($auth_user->regional !== 'head_office') {
                    $query->where('regional', $default_regional);
                }

                // Apply filters from DataTables request
                $request->whenFilled('regional', fn($regional) => $query->where('regional', $regional));
                $request->whenFilled('kebun', fn($kebun) => $query->where('plant', $kebun));
                $request->whenFilled('afdeling', fn($afdeling) => $query->where('afdeling', $afdeling));
                $request->whenFilled('tahun_tanam', fn($tahun_tanam) => $query->where('tahun_tanam', $tahun_tanam));
                $request->whenFilled('jenis_pupuk', fn($jenis_pupuk) => $query->where('jenis_pupuk', $jenis_pupuk));
                $request->whenFilled('tgl_pemupukan_start', fn($start) => $query->where('tgl_pemupukan', '>=', $start));
                $request->whenFilled('tgl_pemupukan_end', fn($end) => $query->where('tgl_pemupukan', '<=', $end));
                return $query->get();
            });
            return DataTables::of($data)
                ->setRowId('id')
                ->editColumn('jumlah_pupuk', fn($row) => number_format($row->jumlah_pupuk, 0, ',', '.') . ' Kg')
                ->editColumn('tgl_pemupukan', fn($row) => $row->tgl_pemupukan ? date('d-m-Y', strtotime($row->tgl_pemupukan)) : '-')
                ->addColumn('action', fn($row) => '
                        <a href="' . route('pemupukan.edit', $row->id) . '" class="btn btn-sm btn-primary">Edit</a>
                        <a href="#" class="btn btn-sm btn-danger" onclick="deletePemupukan(' . $row->id . ')">Delete</a>
                    ')
                ->rawColumns(['action'])
                ->toJson();
        }

        return view('global.datatable-pemupukan-tbm', compact(
            'pageTitle',
            'auth_user',
            'assets',
            'headerAction',
            'regionals',
            'kebuns',
            'afdelings',
            'tahunTanams',
            'jenisPupuks',
            'default_regional',
            'default_kebun'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Fetch data for regional dropdown and sort by ascending order
        $regions = MasterDataTbm::distinct()->orderBy('regional', 'asc')->pluck('regional');

        // Fetch data for jenis pupuk dropdown from JenisPupuk model
        $jenisPupuk = JenisPupuk::all();

        return view('pemupukan-tbm.input-pemupukan', compact('regions', 'jenisPupuk'));
    }

    public function getKebunByRegional($regional)
    {
        // Fetch kebun based on the selected regional
        $kebun = MasterDataTbm::where('regional', $regional)->distinct()->pluck('nama_kebun');
        return response()->json($kebun);
    }

    public function getAfdelingByKebun($regional, $kebun)
    {
        // Fetch afdeling based on the selected regional and kebun
        $afdeling = MasterDataTbm::where('regional', $regional)
            ->where('nama_kebun', $kebun)
            ->distinct()
            ->pluck('afdeling');
        return response()->json($afdeling);
    }

    public function getBlokByAfdeling($regional, $kebun, $afdeling)
    {
        // Fetch blok based on the selected regional, kebun, and afdeling
        $blok = MasterDataTbm::where('regional', $regional)
            ->where('nama_kebun', $kebun)
            ->where('afdeling', $afdeling)
            ->distinct()
            ->pluck('blok');
        return response()->json($blok);
    }

    public function getDetailByBlok($regional, $kebun, $afdeling, $blok)
    {
        // Fetch the detail data for the selected blok
        $detail = MasterDataTbm::where('regional', $regional)
            ->where('nama_kebun', $kebun)
            ->where('afdeling', $afdeling)
            ->where('blok', $blok)
            ->first();
        return response()->json($detail);
    }
    public function getDetailByTahunTanam($regional, $kebun, $afdeling)
    {
        // Fetch the unique detail data for the selected blok
        $detail = MasterDataTbm::where('regional', $regional)
            ->where('nama_kebun', $kebun)
            ->where('afdeling', $afdeling)
            ->distinct()
            ->pluck('tahun_tanam');
        return response()->json($detail);
    }

    public function storePemupukanTbm(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'regional' => 'required|string|max:255',
            'kebun' => 'required|string|max:255',
            'afdeling' => 'required|string|max:255',
            'blok' => 'required|string|max:255',
            'tahun_tanam' => 'required|integer',
            'luas_blok' => 'required|numeric',
            'jumlah_pokok' => 'required|integer',
            'jenis_pupuk' => 'required|integer',
            'jumlah_pemupukan' => 'required|numeric',
            'luas_pemupukan' => 'required|numeric',
            'tanggal_pemupukan' => 'required|date',
            'cara_pemupukan' => 'required|string|max:255',
            'jumlah_mekanisasi' => 'required|string|max:255',
        ]);

        // get jenis pupuk data where id = jenis_pupuk
        $jenisPupuk = JenisPupuk::findOrFail($validatedData['jenis_pupuk']);
        $namaPupuk = $jenisPupuk->nama_pupuk;

        $plant = MasterDataTbm::where('regional', $validatedData['regional'])
            ->where('nama_kebun', $validatedData['kebun'])
            ->where('afdeling', $validatedData['afdeling'])
            ->where('blok', $validatedData['blok'])
            ->firstOrFail();

        // dd($plant);

        // Create a new pemupukan record
        $pemupukan = PemupukanTbm::create([
            'id_pupuk' => $validatedData['jenis_pupuk'],
            'id_master_data_tbm' => $plant->id,
            'bulan_tanam' => $plant->bulan_tanam,
            'bahan_tanam' => $plant->bahan_tanam,
            'regional' => $validatedData['regional'],
            'kebun' => $plant->kode_kebun,
            'afdeling' => $validatedData['afdeling'],
            'blok' => $validatedData['blok'],
            'tahun_tanam' => $validatedData['tahun_tanam'],
            'luas_blok' => $validatedData['luas_blok'],
            'jumlah_pokok' => $validatedData['jumlah_pokok'],
            'jenis_pupuk' => $namaPupuk,
            'jumlah_pupuk' => $validatedData['jumlah_pemupukan'],
            'luas_pemupukan' => $validatedData['luas_pemupukan'],
            'tgl_pemupukan' => $validatedData['tanggal_pemupukan'],
            'cara_pemupukan' => $validatedData['cara_pemupukan'],
            'jumlah_mekanisasi' => $validatedData['jumlah_mekanisasi'],
            'plant' => $plant->plant,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data Pemupukan berhasil disimpan.',
            'data' => $pemupukan,
        ]);
    }

}
