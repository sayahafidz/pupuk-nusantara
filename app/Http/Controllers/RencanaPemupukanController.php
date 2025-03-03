<?php

namespace App\Http\Controllers;

use App\Helpers\AuthHelper;
use App\Http\Requests\RencanaPemupukanRequest;
use App\Models\JenisPupuk;
use App\Models\MasterData;
use App\Models\RencanaPemupukan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class RencanaPemupukanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pageTitle = trans('global-message.list_form_title', ['form' => trans('Rencana Pemupukan Data')]);
        $auth_user = AuthHelper::authSession();
        $assets = ['data-table'];
        $headerAction = '<a href="' . route('rencana-pemupukan.create') . '" class="btn btn-sm btn-primary" role="button">Add</a>'
        . ' <a href="' . route('rencana-pemupukan.upload') . '" class="btn btn-sm btn-success" role="button">Upload</a>';

        // Cache dropdown values
        $regionals = Cache::remember('rencana_pemupukan_regionals', 60, fn() =>
            RencanaPemupukan::select('regional')->distinct()->pluck('regional')->all()
        );
        $jenisPupuks = Cache::remember('rencana_pemupukan_jenis_pupuks', 60, fn() =>
            RencanaPemupukan::select('jenis_pupuk')->distinct()->pluck('jenis_pupuk')->all()
        );
        $semesterPemupukans = Cache::remember('rencana_pemupukan_semester_pemupukan', 60, fn() =>
            RencanaPemupukan::select('semester_pemupukan')->distinct()->pluck('semester_pemupukan')->all()
        );

        // Default filters
        $default_regional = $auth_user->regional !== 'head_office' ? $auth_user->regional : $request->input('regional');
        $default_plant = $auth_user->regional !== 'head_office' ? $auth_user->kebun : $request->input('plant');

        if ($request->ajax()) {
            // Generate a unique cache key based on the request parameters
            $cacheKey = 'rencana_pemupukan_data_' . md5(json_encode($request->all()));

            // Retrieve data from cache or query the database
            $data = Cache::remember($cacheKey, 60, function () use ($request, $auth_user, $default_regional) {
                $query = RencanaPemupukan::query()
                    ->select([
                        'id',
                        'regional',
                        'plant',
                        'kebun',
                        'afdeling',
                        'blok',
                        'tahun_tanam',
                        'jenis_pupuk',
                        'jumlah_pupuk',
                        'semester_pemupukan',
                    ]);

                // Role-based filtering
                if ($auth_user->regional !== 'head_office') {
                    $query->where('regional', $default_regional);
                }

                // Apply filters
                $request->whenFilled('regional', fn($regional) => $query->where('regional', $regional));
                $request->whenFilled('plant', fn($plant) => $query->where('plant', $plant));
                $request->whenFilled('afdeling', fn($afdeling) => $query->where('afdeling', $afdeling));
                $request->whenFilled('tahun_tanam', fn($tahun_tanam) => $query->where('tahun_tanam', $tahun_tanam));
                $request->whenFilled('jenis_pupuk', fn($jenis_pupuk) => $query->where('jenis_pupuk', $jenis_pupuk));
                $request->whenFilled('semester_pemupukan', fn($semester) => $query->where('semester_pemupukan', $semester));

                return $query->get();
            });

            return DataTables::of($data)
                ->setRowId('id')
                ->editColumn('jumlah_pupuk', fn($row) => number_format($row->jumlah_pupuk, 0, ',', '.') . ' Kg')
                ->addColumn('action', fn($row) => '
                <a href="' . route('rencana-pemupukan.edit', $row->id) . '" class="btn btn-sm btn-primary">Edit</a>
                <a href="#" class="btn btn-sm btn-danger" onclick="deleteRencanaPemupukan(' . $row->id . ')">Delete</a>
            ')
                ->rawColumns(['action'])
                ->toJson();
        }

        return view('global.datatable-rencana-pemupukan', compact(
            'pageTitle',
            'auth_user',
            'assets',
            'headerAction',
            'regionals',
            'jenisPupuks',
            'semesterPemupukans',
            'default_regional',
            'default_plant'
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
        $regions = RencanaPemupukan::distinct()->orderBy('regional', 'asc')->pluck('regional');

        // Fetch data for jenis pupuk dropdown from JenisPupuk model
        $jenisPupuk = JenisPupuk::all();

        return view('rencana-pemupukan.input-pemupukan', compact('regions', 'jenisPupuk'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function chart()
    {
        // $roles = Role::where('status', 1)->get()->pluck('title', 'id');

        return view('rencana-pemupukan.chart-pemupukan');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            RencanaPemupukan::findOrFail($id)->delete();
            return response()->json(['message' => 'Data has been deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error occurred while deleting data.'], 500);
        }
    }

    // public function getKebunByRegional($regional)
    // {
    //     // Fetch kebun based on the selected regional
    //     $kebun = MasterData::where('rpc', $regional)->distinct()->pluck('nama_kebun');
    //     return response()->json($kebun);
    // }

    // public function getAfdelingByKebun($regional, $kebun)
    // {
    //     // Fetch afdeling based on the selected regional and kebun
    //     $afdeling = MasterData::where('rpc', $regional)
    //         ->where('nama_kebun', $kebun)
    //         ->distinct()
    //         ->pluck('afdeling');
    //     return response()->json($afdeling);
    // }

    // public function getBlokByAfdeling($regional, $kebun, $afdeling)
    // {
    //     // Fetch blok based on the selected regional, kebun, and afdeling
    //     $blok = MasterData::where('rpc', $regional)
    //         ->where('nama_kebun', $kebun)
    //         ->where('afdeling', $afdeling)
    //         ->distinct()
    //         ->pluck('no_blok');
    //     return response()->json($blok);
    // }

    // public function getDetailByBlok($regional, $kebun, $afdeling, $blok)
    // {
    //     // Fetch the detail data for the selected blok
    //     $detail = MasterData::where('rpc', $regional)
    //         ->where('nama_kebun', $kebun)
    //         ->where('afdeling', $afdeling)
    //         ->where('no_blok', $blok)
    //         ->first();
    //     return response()->json($detail);
    // }

    public function import(Request $request)
    {
        $validated = $request->validate([
            'parsed_data' => 'required|json',
        ]);

        $parsedData = json_decode($request->input('parsed_data'), true);

        $batchData = [];

        // Fetch all jenis_pupuk data once to reduce queries
        $jenisPupukMap = JenisPupuk::pluck('id', 'jenis_pupuk');

        foreach ($parsedData as $row) {
            try {

                // Assuming that the data is already pre-processed and grouped by fertilizer columns
                $batchData[] = [
                    'id_pupuk' => $jenisPupukMap[$row['jenis_pupuk']] ?? null, // Get id_pupuk
                    'regional' => trim($row['regional']),
                    'kebun' => trim($row['kebun']),
                    'afdeling' => trim($row['afdeling']),
                    'blok' => trim($row['blok']),
                    'tahun_tanam' => intval($row['tahun_tanam']),
                    'luas_blok' => floatval($row['luas_blok']),
                    'jumlah_pokok' => intval($row['jumlah_pokok']),
                    'jenis_pupuk' => $row['jenis_pupuk'],
                    'jumlah_pupuk' => $row['jumlah_pupuk'],
                    'luas_pemupukan' => 0, // Adjust or calculate based on your requirements
                    'semester_pemupukan' => $row['semester_pemupukan'],
                    'plant' => $row['plant'], // Set plant value later
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            } catch (\Exception $e) {
                Log::warning('Row failed to process:', ['row' => $row, 'error' => $e->getMessage()]);
            }
        }

        if (!empty($batchData)) {
            // Split the batch data into smaller chunks
            $chunkSize = 500; // You can adjust this size based on your needs
            $chunks = array_chunk($batchData, $chunkSize);

            try {
                foreach ($chunks as $chunk) {
                    Log::info('Inserting chunk with ' . count($chunk) . ' records.');
                    RencanaPemupukan::insert($chunk); // Insert each chunk
                }
            } catch (\Exception $e) {
                Log::error('Error inserting data:', ['error' => $e->getMessage()]);
                return response()->json(['message' => 'Failed to save data.', 'error' => $e->getMessage()], 500);
            }
        }

        return response()->json(['message' => 'Data imported and saved successfully!']);
    }

    // upload data
    public function upload()
    {
        // return response()->json(['message' => 'mantap']);
        return view('rencana-pemupukan.upload');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RencanaPemupukanRequest $request)
    {
        $request['password'] = bcrypt($request->password);

        $request['username'] = $request->username ?? stristr($request->email, "@", true) . rand(100, 1000);

        $user = RencanaPemupukan::create($request->all());

        storeMediaFile($user, $request->profile_image, 'profile_image');

        $user->assignRole('user');

        // Save user Profile data...
        $user->userProfile()->create($request->userProfile);

        return redirect()->route('users.index')->withSuccess(__('Data Berhasil Di Tambahkan', ['name' => __('users.store')]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = RencanaPemupukan::findOrFail($id);

        return view('rencana-pemupukan.form', compact('data', 'profileImage'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = RencanaPemupukan::findOrFail($id);
        $regions = RencanaPemupukan::distinct()->orderBy('regional', 'asc')->pluck('regional');
        $jenisPupuk = JenisPupuk::all();

        return view('rencana-pemupukan.form', compact('data', 'regions', 'jenisPupuk'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'regional' => 'required|string',
            'kebun' => 'required|string',
            'afdeling' => 'required|string',
            'blok' => 'required|string',
            'tahun_tanam' => 'nullable|string',
            'luas_blok' => 'nullable|numeric',
            'jumlah_pokok' => 'nullable|integer',
            'jenis_pupuk' => 'required|integer',
            'jumlah_pupuk' => 'required|numeric',
            'luas_pemupukan' => 'required|numeric',
            'semester_pemupukan' => 'required|in:1,2',
        ]);

        // Get jenis pupuk data
        $jenisPupuk = JenisPupuk::findOrFail($validatedData['jenis_pupuk']);
        $namaPupuk = $jenisPupuk->nama_pupuk;

        $regional = $validatedData['regional'];
        $kebun = $validatedData['kebun'];
        $afdeling = $validatedData['afdeling'];
        $blok = $validatedData['blok'];

        // Fetch the existing RencanaPemupukan record
        $data = RencanaPemupukan::findOrFail($id);
        $oldPlant = $data->plant; // Store the current plant value

        // Fetch the plant value from MasterData
        $masterData = MasterData::where('rpc', $regional)
            ->where('kode_kebun', $kebun)
            ->where('afdeling', $afdeling)
            ->where('no_blok', $blok)
            ->first();

        // Set $plant: use the new value if found, otherwise keep the old value
        $plant = $masterData ? $masterData->plant : $oldPlant;

        // Update the RencanaPemupukan record
        $data->update([
            'regional' => $validatedData['regional'],
            'kebun' => $validatedData['kebun'],
            'afdeling' => $validatedData['afdeling'],
            'blok' => $validatedData['blok'],
            'tahun_tanam' => $validatedData['tahun_tanam'],
            'luas_blok' => $validatedData['luas_blok'],
            'jumlah_pokok' => $validatedData['jumlah_pokok'],
            'jenis_pupuk' => $namaPupuk,
            'jumlah_pupuk' => $validatedData['jumlah_pupuk'],
            'luas_pemupukan' => $validatedData['luas_pemupukan'],
            'semester_pemupukan' => $validatedData['semester_pemupukan'],
            'plant' => $plant,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data updated successfully!',
        ]);
    }

    public function getKebunByRegional($regional)
    {
        // Fetch kebun based on the selected regional
        $kebun = MasterData::where('rpc', $regional)->distinct()->pluck('nama_kebun');
        return response()->json($kebun);
    }

    public function getKebunByRegionalWithCode($regional)
    {
        // Fetch kebun based on the selected regional
        $kebun = MasterData::where('rpc', $regional)->distinct()->pluck('nama_kebun', 'plant');
        return response()->json($kebun);
    }

    public function getAfdelingByKebunWithCode($regional, $plant)
    {
        // Decode URL-encoded values (e.g., %20 becomes a space)
        $regional = urldecode($regional); // 'RPC3'
        $plant = urldecode($plant); // '1E02'

        // Fetch afdeling with case-insensitive matching
        $afdeling = MasterData::whereRaw('UPPER(rpc) = ?', [strtoupper($regional)])
            ->whereRaw('UPPER(plant) = ?', [strtoupper($plant)])
            ->distinct()
            ->pluck('afdeling');

        // Check if any results were found
        if ($afdeling->isEmpty()) {
            return response()->json(['message' => 'No afdelings found for the given regional and kebun'], 404);
        }

        return response()->json($afdeling);
    }

    public function getDetailByTahunTanamWithCode($regional, $kebun, $afdeling)
    {
        // Fetch the unique detail data for the selected blok
        $detail = MasterData::where('rpc', $regional)
            ->where('plant', $kebun)
            ->where('afdeling', $afdeling)
            ->distinct()
            ->pluck('tahun_tanam');
        return response()->json($detail);
    }

    public function getAfdelingByKebun($regional, $kebun)
    {
        // Fetch afdeling based on the selected regional and kebun
        $afdeling = MasterData::where('rpc', $regional)
            ->where('nama_kebun', $kebun)
            ->distinct()
            ->pluck('afdeling');
        return response()->json($afdeling);
    }

    public function getBlokByAfdeling($regional, $kebun, $afdeling)
    {
        // Fetch blok based on the selected regional, kebun, and afdeling
        $blok = MasterData::where('rpc', $regional)
            ->where('nama_kebun', $kebun)
            ->where('afdeling', $afdeling)
            ->distinct()
            ->pluck('no_blok');
        return response()->json($blok);
    }

    public function getDetailByBlok($regional, $kebun, $afdeling, $blok)
    {
        // Fetch the detail data for the selected blok
        $detail = MasterData::where('rpc', $regional)
            ->where('nama_kebun', $kebun)
            ->where('afdeling', $afdeling)
            ->where('no_blok', $blok)
            ->first();
        return response()->json($detail);
    }
    public function getDetailByTahunTanam($regional, $kebun, $afdeling)
    {
        // Fetch the unique detail data for the selected blok
        $detail = MasterData::where('rpc', $regional)
            ->where('nama_kebun', $kebun)
            ->where('afdeling', $afdeling)
            ->distinct()
            ->pluck('tahun_tanam');
        return response()->json($detail);
    }

}
