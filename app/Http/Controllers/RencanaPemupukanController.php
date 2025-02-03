<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RencanaPemupukan;
use App\DataTables\RencanaPemupukanDataTable;
use App\Http\Requests\RencanaPemupukanRequest;
use App\Helpers\AuthHelper;
use App\Models\JenisPupuk;
use App\Models\MasterData;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class RencanaPemupukanController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @return \Illuminate\Http\Response
     */
    public function index(RencanaPemupukanDataTable $dataTable)
    {
        $pageTitle = trans('global-message.list_form_title', ['form' => trans('Rencana Pemupukan Data')]);
        $auth_user = AuthHelper::authSession();
        $assets = ['data-table'];
        $headerAction = '<a href="' . route('rencana-pemupukan.create') . '" class="btn btn-sm btn-primary">Add</a>'
            . ' <a href="' . route('rencana-pemupukan.upload') . '" class="btn btn-sm btn-success">Upload</a>';

        $regionals = RencanaPemupukan::select('regional')->distinct()->pluck('regional');
        $kebuns = RencanaPemupukan::select('kebun')->distinct()->pluck('kebun');
        $afdelings = RencanaPemupukan::select('afdeling')->distinct()->pluck('afdeling');
        $tahunTanams = RencanaPemupukan::select('tahun_tanam')->distinct()->pluck('tahun_tanam');
        $jenisPupuks = RencanaPemupukan::select('jenis_pupuk')->distinct()->pluck('jenis_pupuk');

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

    public function getKebunByRegional($regional)
    {
        // Fetch kebun based on the selected regional
        $kebun = MasterData::where('rpc', $regional)->distinct()->pluck('nama_kebun');
        return response()->json($kebun);
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
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            } catch (\Exception $e) {
                Log::warning('Row failed to process:', ['row' => $row, 'error' => $e->getMessage()]);
            }
        }

        if (!empty($batchData)) {
            // Split the batch data into smaller chunks
            $chunkSize = 500;  // You can adjust this size based on your needs
            $chunks = array_chunk($batchData, $chunkSize);

            try {
                foreach ($chunks as $chunk) {
                    Log::info('Inserting chunk with ' . count($chunk) . ' records.');
                    RencanaPemupukan::insert($chunk);  // Insert each chunk
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

        return redirect()->route('users.index')->withSuccess(__('message.msg_added', ['name' => __('users.store')]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = RencanaPemupukan::with('userProfile', 'roles')->findOrFail($id);

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
        $data = RencanaPemupukan::with('userProfile', 'roles')->findOrFail($id);

        $data['user_type'] = $data->roles->pluck('id')[0] ?? null;

        $roles = Role::where('status', 1)->get()->pluck('title', 'id');

        $profileImage = getSingleMedia($data, 'profile_image');

        return view('users.form', compact('data', 'id', 'roles', 'profileImage'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RencanaPemupukanRequest $request, $id)
    {
        // dd($request->all());
        $user = RencanaPemupukan::with('userProfile')->findOrFail($id);

        $role = Role::find($request->user_role);
        if (env('IS_DEMO')) {
            if ($role->name === 'admin' && $user->user_type === 'admin') {
                return redirect()->back()->with('error', 'Permission denied');
            }
        }
        $user->assignRole($role->name);

        $request['password'] = $request->password != '' ? bcrypt($request->password) : $user->password;

        // User user data...
        $user->fill($request->all())->update();

        // Save user image...
        if (isset($request->profile_image) && $request->profile_image != null) {
            $user->clearMediaCollection('profile_image');
            $user->addMediaFromRequest('profile_image')->toMediaCollection('profile_image');
        }

        // user profile data....
        $user->userProfile->fill($request->userProfile)->update();

        if (auth()->check()) {
            return redirect()->route('users.index')->withSuccess(__('message.msg_updated', ['name' => __('message.user')]));
        }
        return redirect()->back()->withSuccess(__('message.msg_updated', ['name' => 'My Profile']));
    }
}
