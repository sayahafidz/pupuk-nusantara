<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataTables\MasterDataTable;
use App\Models\MasterData;
use App\Helpers\AuthHelper;
use Spatie\Permission\Models\Role;
use App\Http\Requests\MasterDataRequest;
// import log
use Illuminate\Support\Facades\Log;

class MasterDataController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(MasterDataTable $dataTable)
    {
        $pageTitle = trans('global-message.list_form_title', ['form' => trans('Master Data')]);
        $auth_user = AuthHelper::authSession();
        $assets = ['data-table'];
        $headerAction = '<a href="' . route('master-data.create') . '" class="btn btn-sm btn-primary" role="button">Add Master Data</a>'
            . ' <a href="' . route('upload-data.upload') . '" class="btn btn-sm btn-success" role="button">Upload Master Data File</a>';
        return $dataTable->render('global.datatable', compact('pageTitle', 'auth_user', 'assets', 'headerAction'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::where('status', 1)->get()->pluck('title', 'id');

        return view('users.form', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MasterDataRequest $request)
    {
        $request['password'] = bcrypt($request->password);

        $request['username'] = $request->username ?? stristr($request->email, "@", true) . rand(100, 1000);

        $user = MasterData::create($request->all());

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
        $data = MasterData::with('userProfile', 'roles')->findOrFail($id);

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
        $data = MasterData::with('userProfile', 'roles')->findOrFail($id);

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
    public function update(MasterDataRequest $request, $id)
    {
        // dd($request->all());
        $user = MasterData::with('userProfile')->findOrFail($id);

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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = MasterData::findOrFail($id);
        $status = 'errors';
        $message = __('global-message.delete_form', ['form' => __('users.title')]);

        if ($user != '') {
            $user->delete();
            $status = 'success';
            $message = __('global-message.delete_form', ['form' => __('users.title')]);
        }

        if (request()->ajax()) {
            return response()->json(['status' => true, 'message' => $message, 'datatable_reload' => 'dataTable_wrapper']);
        }

        return redirect()->back()->with($status, $message);
    }


    // upload data
    public function upload()
    {
        // return response()->json(['message' => 'mantap']);
        return view('master-data.upload');
    }

    public function import(Request $request)
    {
        $validated = $request->validate([
            'parsed_data' => 'required|json',
        ]);

        $parsedData = json_decode($request->input('parsed_data'), true);

        $headers = [
            'kondisi',
            'status_umur',
            'kode_kebun',
            'nama_kebun',
            'kkl_kebun',
            'afdeling',
            'tahun_tanam',
            'no_blok',
            'luas',
            'jlh_pokok',
            'pkk_ha',
            'RPC',
        ];

        $batchData = [];

        foreach ($parsedData as $row) {
            if (count($row) >= count($headers)) {
                $data = array_combine($headers, array_slice($row, 0, count($headers)));
                $batchData[] = $data;
            } else {
                Log::warning('Row does not match expected header count:', ['row' => $row]);
            }
        }

        if (!empty($batchData)) {
            try {
                MasterData::insert($batchData);
            } catch (\Exception $e) {
                Log::error('Error inserting data:', ['error' => $e->getMessage()]);
                return response()->json(['message' => 'Failed to save data.'], 500);
            }
        }

        return response()->json(['message' => 'Data imported and saved successfully!']);
    }

    
}
