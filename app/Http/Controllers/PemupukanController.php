<?php

namespace App\Http\Controllers;

use App\DataTables\PemupukanDataTable;
use App\Helpers\AuthHelper;
use App\Http\Requests\UserRequest;
use App\Models\JenisPupuk;
use App\Models\MasterData;
use App\Models\Pemupukan;
use App\Models\RencanaPemupukan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PemupukanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(PemupukanDataTable $dataTable)
    {
        $pageTitle = trans('global-message.list_form_title', ['form' => trans('Pemupukan Data')]);
        $auth_user = AuthHelper::authSession();
        $assets = ['data-table'];
        $headerAction = '<a href="' . route('input-pemupukan') . '" class="btn btn-sm btn-primary" role="button">Add Pemupukan</a>'
        . ' <a href="' . route('pemupukan.upload') . '" class="btn btn-sm btn-success" role="button">Upload Pemupukan File</a>';

        $regionals = Pemupukan::select('regional')->distinct()->pluck('regional');
        $kebuns = Pemupukan::select('kebun')->distinct()->pluck('kebun');
        $afdelings = Pemupukan::select('afdeling')->distinct()->pluck('afdeling');
        $tahunTanams = Pemupukan::select('tahun_tanam')->distinct()->pluck('tahun_tanam');
        $jenisPupuks = Pemupukan::select('jenis_pupuk')->distinct()->pluck('jenis_pupuk');

        return $dataTable->render('global.datatable', compact(
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
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Fetch data for regional dropdown and sort by ascending order
        $regions = MasterData::distinct()->orderBy('rpc', 'asc')->pluck('rpc');

        // Fetch data for jenis pupuk dropdown from JenisPupuk model
        $jenisPupuk = JenisPupuk::all();

        return view('pemupukan.input-pemupukan', compact('regions', 'jenisPupuk'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function chart()
    {
        // $roles = Role::where('status', 1)->get()->pluck('title', 'id');

        $regions = MasterData::distinct()->orderBy('rpc', 'asc')->pluck('rpc');

        $jenisPupuks = JenisPupuk::distinct()->orderBy('jenis_pupuk', 'asc')->pluck('jenis_pupuk');

        return view('pemupukan.chart-pemupukan', compact('regions', 'jenisPupuks'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $request['password'] = bcrypt($request->password);

        $request['username'] = $request->username ?? stristr($request->email, "@", true) . rand(100, 1000);

        $user = User::create($request->all());

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
        $data = User::with('userProfile', 'roles')->findOrFail($id);

        $profileImage = getSingleMedia($data, 'profile_image');

        return view('users.profile', compact('data', 'profileImage'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Fetch the data to be edited
        $data = Pemupukan::findOrFail($id);

        // Fetch dropdown data
        $regions = MasterData::distinct()->orderBy('rpc', 'asc')->pluck('rpc');
        $jenisPupuk = JenisPupuk::all();

        return view('pemupukan.edit-pemupukan', compact('data', 'regions', 'jenisPupuk'));
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
            'jumlah_pemupukan' => 'required|numeric',
            'luas_pemupukan' => 'required|numeric',
            'tanggal_pemupukan' => 'required|date',
            'cara_pemupukan' => 'required|string',
            'jumlah_tenaga_kerja' => 'nullable|integer',
        ]);

        // Get jenis pupuk data
        $jenisPupuk = JenisPupuk::findOrFail($validatedData['jenis_pupuk']);
        $namaPupuk = $jenisPupuk->nama_pupuk;

        $regional = $validatedData['regional'];
        $kebun = $validatedData['kebun'];
        $afdeling = $validatedData['afdeling'];
        $blok = $validatedData['blok'];

        // Fetch the existing Pemupukan record to get the old plant value
        $data = Pemupukan::findOrFail($id);
        $oldPlant = $data->plant; // Store the current plant value

        // Raw SQL query to fetch only the 'plant' field
        $result = DB::selectOne(
            "SELECT plant FROM master_data
         WHERE rpc = ?
           AND kode_kebun = ?
           AND afdeling = ?
           AND no_blok = ?",
            [$regional, $kebun, $afdeling, $blok]
        );

        // Set $plant: use the new value if found, otherwise keep the old value
        $plant = $result ? $result->plant : $oldPlant;

        // get by id
        $oldPemupukan = Pemupukan::findOrFail($id);

        // Debugging output (uncomment if needed)
        // dd($validatedData, $plant, $namaPupuk);

        // Update the Pemupukan record
        $data->update([
            'regional' => $validatedData['regional'] ?? $oldPemupukan->regional,
            'kebun' => $validatedData['kebun'] ?? $oldPemupukan->kebun,
            'afdeling' => $validatedData['afdeling'] ?? $oldPemupukan->afdeling,
            'blok' => $validatedData['blok'] ?? $oldPemupukan->blok,
            'tahun_tanam' => $validatedData['tahun_tanam'] ?? $oldPemupukan->tahun_tanam,
            'luas_blok' => $validatedData['luas_blok'] ?? $oldPemupukan->luas_blok,
            'jumlah_pokok' => $validatedData['jumlah_pokok'] ?? $oldPemupukan->jumlah_pokok,
            'jenis_pupuk' => $namaPupuk ?? $oldPemupukan->jenis_pupuk,
            'jumlah_pemupukan' => $validatedData['jumlah_pemupukan'] ?? $oldPemupukan->jumlah_pemupukan,
            'luas_pemupukan' => $validatedData['luas_pemupukan'] ?? $oldPemupukan->luas_pemupukan,
            'tanggal_pemupukan' => $validatedData['tanggal_pemupukan'] ?? $oldPemupukan->tanggal_pemupukan,
            'cara_pemupukan' => $validatedData['cara_pemupukan'] ?? $oldPemupukan->cara_pemupukan,
            'jumlah_tenaga_kerja' => $validatedData['jumlah_tenaga_kerja'] ?? $oldPemupukan->jumlah_tenaga_kerja,
            'plant' => $plant ?? $oldPemupukan->plant,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data updated successfully!',
        ]);
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
            Pemupukan::findOrFail($id)->delete();
            return response()->json(['message' => 'Data has been deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error occurred while deleting data.'], 500);
        }
    }

    public function getKebun(Request $request)
    {
        $regionalId = $request->input('regional_id');
        $kebun = MasterData::where('regional_id', $regionalId)->get();
        return response()->json($kebun);
    }

    public function storePemupukan(Request $request)
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

        $plant = MasterData::where('rpc', $validatedData['regional'])
            ->where('nama_kebun', $validatedData['kebun'])
            ->where('afdeling', $validatedData['afdeling'])
            ->where('no_blok', $validatedData['blok'])
            ->firstOrFail();

        // Create a new pemupukan record
        $pemupukan = Pemupukan::create([
            'id_pupuk' => $validatedData['jenis_pupuk'],
            'regional' => $validatedData['regional'],
            'kebun' => $validatedData['kebun'],
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

    public function getKebunByRegional($regional)
    {
        // Fetch kebun based on the selected regional
        $kebun = MasterData::where('rpc', $regional)->distinct()->pluck('nama_kebun');
        return response()->json($kebun);
    }

    public function getKebunByRegionalWithCode($regional)
    {
        // Fetch kebun based on the selected regional
        $kebun = MasterData::where('rpc', $regional)->distinct()->pluck('nama_kebun', 'kode_kebun');
        return response()->json($kebun);
    }

    public function getAfdelingByKebunWithCode($regional, $kebun)
    {
        // Decode URL-encoded values (e.g., %20 becomes a space)
        $regional = urldecode($regional); // 'RPC3'
        $kebun = urldecode($kebun); // 'KEBUN INTI/KKPA SEI GARO'

        // Fetch afdeling with case-insensitive matching
        $afdeling = MasterData::whereRaw('UPPER(rpc) = ?', [strtoupper($regional)])
            ->whereRaw('UPPER(kode_kebun) = ?', [strtoupper($kebun)])
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
            ->where('kode_kebun', $kebun)
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

    public function import(Request $request)
    {
        $request->validate([
            'parsed_data' => 'required|json',
        ]);

        $parsedData = json_decode($request->input('parsed_data'), true);
        if (empty($parsedData)) {
            return response()->json(['message' => 'No valid data provided for import.'], 400);
        }

        $batchData = [];
        $FertilizerColumns = [
            ['jenis_pupuk' => 'NPK 12.12.17.2', 'column_index' => 10], // Adjusted to 0-based index
            ['jenis_pupuk' => 'NPK 13.6.27.4', 'column_index' => 11],
            ['jenis_pupuk' => 'Dolomit', 'column_index' => 12],
            ['jenis_pupuk' => 'Mop', 'column_index' => 13],
            ['jenis_pupuk' => 'Urea', 'column_index' => 14],
        ];

        // Fetch jenis_pupuk map once
        $jenisPupukMap = JenisPupuk::pluck('id', 'jenis_pupuk')->all();

        // Optimize MasterData lookup
        $uniqueKeys = collect($parsedData)->map(fn($row) => [
            'rpc' => trim($row[15] ?? ''), // Column Q (index 15) for rpc
            'kode_kebun' => trim($row[2] ?? ''), // Column C for kebun
            'afdeling' => trim($row[4] ?? ''), // Column E for afd
            'no_blok' => trim($row[6] ?? ''), // Column G for no_blok
        ])->unique(fn($item) => implode('_', $item))->values()->all();

        $masterDataMap = MasterData::whereIn('rpc', array_column($uniqueKeys, 'rpc'))
            ->whereIn('kode_kebun', array_column($uniqueKeys, 'kode_kebun'))
            ->whereIn('afdeling', array_column($uniqueKeys, 'afdeling'))
            ->whereIn('no_blok', array_column($uniqueKeys, 'no_blok'))
            ->pluck('plant', \DB::raw("CONCAT(rpc, '_', kode_kebun, '_', afdeling, '_', no_blok)"))
            ->all();

        foreach ($parsedData as $row) {
            try {
                $regional = trim($row[15] ?? ''); // rpc (Q)
                $kebun = trim($row[2] ?? ''); // kebun (C)
                $afdeling = trim($row[4] ?? ''); // afd (E)
                $blok = trim($row[6] ?? ''); // no_blok (G)
                $plantKey = implode('_', [$regional, $kebun, $afdeling, $blok]);
                $plant = $masterDataMap[$plantKey] ?? trim($row[16] ?? ''); // Use MasterData or fallback to Excel (R)

                foreach ($FertilizerColumns as $fertilizer) {
                    $amount = floatval(str_replace(',', '.', $row[$fertilizer['column_index']] ?? 0));
                    if ($amount > 0) {
                        $batchData[] = [
                            'id_pupuk' => $jenisPupukMap[$fertilizer['jenis_pupuk']] ?? null,
                            'regional' => $regional,
                            'kebun' => $kebun,
                            'afdeling' => $afdeling,
                            'blok' => $blok,
                            'tahun_tanam' => (int) ($row[5] ?? 0), // tahun_tanam (F)
                            'luas_blok' => floatval(str_replace(',', '.', $row[7] ?? 0)), // luas (H)
                            'jumlah_pokok' => (int) ($row[8] ?? 0), // jlh_pokok (I)
                            'jenis_pupuk' => $fertilizer['jenis_pupuk'],
                            'jumlah_pupuk' => $amount,
                            'luas_pemupukan' => 0, // Default, adjust if needed
                            'tgl_pemupukan' => now(), // Default, consider adding to Excel
                            'cara_pemupukan' => 'Manual', // Default, consider adding to Excel
                            'jumlah_mekanisasi' => 0, // Default, consider adding to Excel
                            'plant' => $plant,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Row failed to process:', ['row' => $row, 'error' => $e->getMessage()]);
            }
        }

        if (empty($batchData)) {
            return response()->json(['message' => 'No valid rows to import.'], 400);
        }

        try {
            $chunkSize = 500; // Match your upload form’s CHUNK_SIZE
            foreach (array_chunk($batchData, $chunkSize) as $chunk) {
                Log::info('Inserting chunk with ' . count($chunk) . ' records.');
                Pemupukan::insert($chunk);
            }
            return response()->json(['message' => 'Data imported and saved successfully!']);
        } catch (\Exception $e) {
            Log::error('Error inserting data:', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to save data.', 'error' => $e->getMessage()], 500);
        }
    }

    // upload data
    public function upload()
    {
        // return response()->json(['message' => 'mantap']);
        return view('pemupukan.upload');
    }

    public function getChartData(Request $request)
    {
        // Get filters from the request
        $regional = $request->input('regional');
        $kebun = $request->input('kebun');
        $afdeling = $request->input('afdeling');
        $tahunTanam = $request->input('tahun_tanam');
        $jenisPupuk = $request->input('jenis_pupuk');

        // Query the `rencana_pemupukan` table
        $query = RencanaPemupukan::query();

        if ($regional) {
            $query->where('regional', $regional);
        }
        if ($kebun) {
            $query->where('kebun', $kebun);
        }
        if ($afdeling) {
            $query->where('afdeling', $afdeling);
        }
        if ($tahunTanam) {
            $query->where('tahun_tanam', $tahunTanam);
        }
        if ($jenisPupuk) {
            $query->where('jenis_pupuk', $jenisPupuk);
        }

        // Fetch the filtered data
        $rencanaPemupukan = $query->select('kebun', 'jumlah_pokok', 'luas_blok')->groupBy('kebun')->get();

        // Query the `pemupukan` table
        $pemupukan = Pemupukan::query();
        if ($regional) {
            $pemupukan->where('regional', $regional);
        }
        if ($kebun) {
            $pemupukan->where('kebun', $kebun);
        }
        if ($afdeling) {
            $pemupukan->where('afdeling', $afdeling);
        }
        if ($tahunTanam) {
            $pemupukan->where('tahun_tanam', $tahunTanam);
        }
        if ($jenisPupuk) {
            $pemupukan->where('jenis_pupuk', $jenisPupuk);
        }

        // Fetch the filtered data
        $pemupukan = $pemupukan->select('kebun', 'jumlah_pupuk')->groupBy('kebun')->get();

        // Prepare data for the chart
        $categories = $rencanaPemupukan->pluck('kebun');
        $jumlahPokok = $rencanaPemupukan->pluck('jumlah_pokok');
        $luasBlok = $rencanaPemupukan->pluck('luas_blok');
        $jumlahPupuk = $pemupukan->pluck('jumlah_pupuk');

        // Return the data as JSON
        return response()->json([
            'categories' => $categories,
            'jumlahPokok' => $jumlahPokok,
            'luasBlok' => $luasBlok,
            'jumlahPupuk' => $jumlahPupuk,
        ]);
    }

    public function getComparisonDataOfTheChart(Request $request, $regional, $kebun = null, $afdeling = null, $tahun_tanam = null, $jenis_pupuk = null)
    {
        // Collect filter parameters
        $filters = compact('regional', 'kebun', 'afdeling', 'tahun_tanam', 'jenis_pupuk');

        // Retrieve date range filters from the request
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate');

        // Continue with your query and logic as before
        $pemupukanData = Pemupukan::select('regional', 'kebun', DB::raw('SUM(jumlah_pupuk) as jumlah_pupuk_pemupukan'), DB::raw('SUM(luas_blok) as luas_blok_pemupukan'))
            ->when($filters['regional'], function ($query) use ($filters) {
                return $query->where('regional', $filters['regional']);
            })
            ->when($filters['kebun'], function ($query) use ($filters) {
                return $query->where('kebun', $filters['kebun']);
            })
            ->when($filters['afdeling'], function ($query) use ($filters) {
                return $query->where('afdeling', $filters['afdeling']);
            })
            ->when($filters['tahun_tanam'], function ($query) use ($filters) {
                return $query->where('tahun_tanam', $filters['tahun_tanam']);
            })
            ->when($filters['jenis_pupuk'], function ($query) use ($filters) {
                return $query->where('jenis_pupuk', $filters['jenis_pupuk']);
            })
            ->when($fromDate, function ($query) use ($fromDate) {
                return $query->whereDate('tanggal', '>=', $fromDate);
            })
            ->when($toDate, function ($query) use ($toDate) {
                return $query->whereDate('tanggal', '<=', $toDate);
            })
            ->groupBy('regional', 'kebun')
            ->get();

        // Query for rencana_pemupukan data with dynamic filters
        $rencanaPemupukanData = RencanaPemupukan::select('regional', 'kebun', DB::raw('SUM(jumlah_pupuk) as jumlah_pupuk_rencana'), DB::raw('SUM(luas_blok) as luas_blok_rencana'))
            ->when($filters['regional'], function ($query) use ($filters) {
                return $query->where('regional', $filters['regional']);
            })
            ->when($filters['kebun'], function ($query) use ($filters) {
                return $query->where('kebun', $filters['kebun']);
            })
            ->when($filters['afdeling'], function ($query) use ($filters) {
                return $query->where('afdeling', $filters['afdeling']);
            })
            ->when($filters['tahun_tanam'], function ($query) use ($filters) {
                return $query->where('tahun_tanam', $filters['tahun_tanam']);
            })
            ->when($filters['jenis_pupuk'], function ($query) use ($filters) {
                return $query->where('jenis_pupuk', $filters['jenis_pupuk']);
            })
            ->when($fromDate, function ($query) use ($fromDate) {
                return $query->whereDate('tanggal', '>=', $fromDate);
            })
            ->when($toDate, function ($query) use ($toDate) {
                return $query->whereDate('tanggal', '<=', $toDate);
            })
            ->groupBy('regional', 'kebun')
            ->get();

        // Prepare data for comparison
        $comparisonData = [];

        // Iterate over the pemupukan data and merge with rencana_pemupukan data
        foreach ($pemupukanData as $pemupukan) {
            $key = $pemupukan->regional . '-' . $pemupukan->kebun;
            $comparisonData[$key] = [
                'regional' => $pemupukan->regional,
                'kebun' => $pemupukan->kebun,
                'pemupukan' => [
                    'jumlah_pupuk' => $pemupukan->jumlah_pupuk_pemupukan,
                    'luas_blok' => $pemupukan->luas_blok_pemupukan,
                ],
                'rencana' => [
                    'jumlah_pupuk' => 0,
                    'luas_blok' => 0,
                ],
            ];
        }

        // Merge rencana_pemupukan data into the comparisonData
        foreach ($rencanaPemupukanData as $rencana) {
            $key = $rencana->regional . '-' . $rencana->kebun;
            if (isset($comparisonData[$key])) {
                $comparisonData[$key]['rencana']['jumlah_pupuk'] = $rencana->jumlah_pupuk_rencana;
                $comparisonData[$key]['rencana']['luas_blok'] = $rencana->luas_blok_rencana;
            }
        }

        // Check if comparisonData is empty and return default data
        if (empty($comparisonData)) {
            return response()->json([
                'message' => 'No data available for the selected filters',
                'data' => [],
            ]);
        }

        // Return the filtered data as a response
        return response()->json($comparisonData);
    }

    public function getPemupukanComparison(Request $request)
    {
        // Retrieve the filter parameters from the request
        $regional = $request->input('regional', null);
        $kebun = $request->input('kebun', null);
        $afdeling = $request->input('afdeling', null);
        $jenis_pupuk = $request->input('jenis_pupuk', null);
        $tahun_tanam = $request->input('tahun_tanam', null);

        // Initialize the rencanaQuery for fetching rencana pemupukan data
        $rencanaQuery = RencanaPemupukan::select(
            'regional',
            DB::raw('SUM(jumlah_pupuk) as total_jumlah_pupuk'),
            DB::raw('SUM(luas_blok) as total_luas_blok')
        )->groupBy('regional');

        // Apply filters to the rencana query
        if ($regional) {
            $rencanaQuery->where('regional', $regional);
        }

        if ($kebun) {
            $rencanaQuery->where('kebun', $kebun);
        }

        if ($afdeling) {
            $rencanaQuery->where('afdeling', $afdeling);
        }

        if ($jenis_pupuk) {
            $rencanaQuery->where('jenis_pupuk', $jenis_pupuk);
        }

        if ($tahun_tanam) {
            $rencanaQuery->where('tahun_tanam', $tahun_tanam);
        }

        // Fetch the rencana data
        $rencanaData = $rencanaQuery->get();

        // Initialize the realisasiQuery for fetching realisasi pemupukan data
        $realisasiQuery = Pemupukan::select(
            'regional',
            DB::raw('SUM(jumlah_pupuk) as total_jumlah_pupuk_realisasi'),
            DB::raw('SUM(luas_blok) as total_luas_blok_realisasi')
        )->groupBy('regional');

        // Apply filters to the realisasi query
        if ($regional) {
            $realisasiQuery->where('regional', $regional);
        }

        if ($kebun) {
            $realisasiQuery->where('kebun', $kebun);
        }

        if ($afdeling) {
            $realisasiQuery->where('afdeling', $afdeling);
        }

        if ($jenis_pupuk) {
            $realisasiQuery->where('jenis_pupuk', $jenis_pupuk);
        }

        if ($tahun_tanam) {
            $realisasiQuery->where('tahun_tanam', $tahun_tanam);
        }

        // Fetch the realisasi data
        $realisasiData = $realisasiQuery->get();

        // If no filters are set, aggregate data by regional
        if (!$regional && !$kebun && !$afdeling && !$jenis_pupuk && !$tahun_tanam) {
            $comparisonData = $rencanaData->map(function ($rencana) use ($realisasiData) {
                $regionalRealisasi = $realisasiData->firstWhere('regional', $rencana->regional);
                return [
                    'regional' => $rencana->regional,
                    'rencana' => [
                        'jumlah_pupuk' => $rencana->total_jumlah_pupuk,
                        'luas_blok' => $rencana->total_luas_blok,
                    ],
                    'realisasi' => [
                        'jumlah_pupuk' => $regionalRealisasi->total_jumlah_pupuk_realisasi ?? 0,
                        'luas_blok' => $regionalRealisasi->total_luas_blok_realisasi ?? 0,
                    ],
                    'percentage' => [
                        'jumlah_pupuk' => $rencana->total_jumlah_pupuk > 0
                        ? (($regionalRealisasi->total_jumlah_pupuk_realisasi ?? 0) / $rencana->total_jumlah_pupuk) * 100
                        : 0,
                        'luas_blok' => $rencana->total_luas_blok > 0
                        ? (($regionalRealisasi->total_luas_blok_realisasi ?? 0) / $rencana->total_luas_blok) * 100
                        : 0,
                    ],
                ];
            });

            return response()->json($comparisonData);
        }

        // Prepare comparison data for detailed filtering
        $comparisonData = [];
        foreach ($rencanaData as $rencana) {
            $comparisonData[] = [
                'regional' => $rencana->regional,
                'kebun' => $rencana->kebun,
                'semester_1' => [
                    'rencana' => [
                        'jumlah_pupuk' => $rencana->total_jumlah_pupuk,
                        'luas_blok' => $rencana->total_luas_blok,
                    ],
                    'realisasi' => [
                        'jumlah_pupuk' => 0,
                        'luas_blok' => 0,
                    ],
                    'percentage' => [
                        'jumlah_pupuk' => 0,
                        'luas_blok' => 0,
                    ],
                ],
                'semester_2' => [
                    'rencana' => [
                        'jumlah_pupuk' => 0,
                        'luas_blok' => 0,
                    ],
                    'realisasi' => [
                        'jumlah_pupuk' => 0,
                        'luas_blok' => 0,
                    ],
                    'percentage' => [
                        'jumlah_pupuk' => 0,
                        'luas_blok' => 0,
                    ],
                ],
            ];
        }

        foreach ($realisasiData as $realisasi) {
            foreach ($comparisonData as &$data) {
                if ($data['regional'] === $realisasi->regional) {
                    $semesterKey = $realisasi->month <= 6 ? 'semester_1' : 'semester_2';
                    $data[$semesterKey]['realisasi']['jumlah_pupuk'] += $realisasi->total_jumlah_pupuk_realisasi;
                    $data[$semesterKey]['realisasi']['luas_blok'] += $realisasi->total_luas_blok_realisasi;
                }
            }
        }

        // Calculate percentages
        foreach ($comparisonData as &$data) {
            foreach (['semester_1', 'semester_2'] as $semester) {
                $rencanaJumlah = $data[$semester]['rencana']['jumlah_pupuk'];
                $realisasiJumlah = $data[$semester]['realisasi']['jumlah_pupuk'];
                $data[$semester]['percentage']['jumlah_pupuk'] = $rencanaJumlah > 0
                ? ($realisasiJumlah / $rencanaJumlah) * 100
                : 0;

                $rencanaLuas = $data[$semester]['rencana']['luas_blok'];
                $realisasiLuas = $data[$semester]['realisasi']['luas_blok'];
                $data[$semester]['percentage']['luas_blok'] = $rencanaLuas > 0
                ? ($realisasiLuas / $rencanaLuas) * 100
                : 0;
            }
        }

        return response()->json($comparisonData);
    }
}
