<?php

namespace App\Http\Controllers;

use App\DataTables\SettingDataTable;
use App\Http\Requests\SettingRequest;
use App\Models\Settings;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Helpers\AuthHelper;


class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param SettingDataTable $dataTable
     * @return \Illuminate\Http\Response
     */
    public function index(SettingDataTable $dataTable)
    {
        $pageTitle = trans('global-message.list_form_title', ['form' => trans('Setting')]);
        $auth_user = AuthHelper::authSession(); // Added to match example convention
        $assets = ['data-table'];
        $headerAction = '<a href="' . route('setting.create') . '" class="btn btn-sm btn-primary" role="button">Add Setting</a>';

        return $dataTable->render('global.datatable', compact('pageTitle', 'auth_user', 'assets', 'headerAction'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::orderBy('first_name', 'asc')->pluck('first_name', 'id');
        return view('setting.form', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SettingRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(SettingRequest $request)
    {
        try {
            // Validate the request data
            $data = $request->validated();

            // Create a new record in the database
            Settings::create($data);

            // Redirect to index with a success message
            return redirect()->route('setting.index')->withSuccess(__('Berhasil menambahkan Setting.'));
        } catch (\Exception $e) {
            // Log the error details
            Log::error('Error storing Setting: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'input' => $request->all(),
            ]);

            // Redirect back with an error message
            return redirect()->back()->withError(__('Terjadi kesalahan saat memproses permintaan. Silakan coba lagi.'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Settings::findOrFail($id);
        return view('setting.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Settings::findOrFail($id);
        return view('setting.form', compact('data', 'id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SettingRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(SettingRequest $request, $id)
    {
        try {
            $data = $request->validated();
            $setting = Settings::findOrFail($id);
            $setting->update($data);

            return redirect()->route('setting.index')->withSuccess(__('Berhasil mengupdate Setting.'));
        } catch (\Exception $e) {
            Log::error('Error updating Setting: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'input' => $request->all(),
            ]);
            return redirect()->back()->withError(__('Terjadi kesalahan saat mengupdate Setting. Silakan coba lagi.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $setting = Settings::findOrFail($id);
            $setting->delete();

            return response()->json([
                'success' => true,
                'message' => 'Record deleted successfully!',
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting Setting: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to delete record.',
            ], 500);
        }
    }
}
