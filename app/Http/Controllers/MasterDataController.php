<?php

namespace App\Http\Controllers;

use App\DataTables\MasterDataTable;
use App\Helpers\AuthHelper;
use App\Http\Requests\MasterDataRequest;
use App\Models\MasterData;
use Illuminate\Http\Request;
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
        $assets = [];
        return view('master-data.form', compact('assets'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\MasterDataRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MasterDataRequest $request)
    {
        MasterData::create($request->all());

        return redirect()->route('master-data.index')->withSuccess(__('Data Berhasil Di Tambahkan', ['name' => 'Master Data']));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = MasterData::findOrFail($id);

        return view('master-data.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = MasterData::findOrFail($id);
        $assets = [];

        return view('master-data.form', compact('data', 'id', 'assets'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\MasterDataRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(MasterDataRequest $request, $id)
    {
        $masterData = MasterData::findOrFail($id);
        $masterData->update($request->all());

        return redirect()->route('master-data.index')->withSuccess(__('Data Berhasil Di Update', ['name' => 'Master Data']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $masterData = MasterData::findOrFail($id);
        $status = 'errors';
        $message = __('global-message.delete_form', ['form' => __('Master Data')]);

        if ($masterData) {
            $masterData->delete();
            $status = 'success';
            $message = __('global-message.delete_form', ['form' => __('Master Data')]);
        }

        if (request()->ajax()) {
            return response()->json(['status' => true, 'message' => $message, 'datatable_reload' => 'dataTable_wrapper']);
        }

        return redirect()->back()->with($status, $message);
    }

    /**
     * Show the form for uploading master data.
     *
     * @return \Illuminate\Http\Response
     */
    public function upload()
    {
        return view('master-data.upload');
    }

    /**
     * Import master data from parsed data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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
            'rpc',
            'plant',
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
                Log::info('Attempting to insert data:', ['data' => $batchData]);
                MasterData::insert($batchData);
                return response()->json(['message' => 'Data imported and saved successfully!']);
            } catch (\Exception $e) {
                Log::error('Error inserting data:', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                return response()->json(['message' => 'Failed to save data.', 'error' => $e->getMessage()], 500);
            }
        }

        return response()->json(['message' => 'No valid data to import'], 400);
    }
}
