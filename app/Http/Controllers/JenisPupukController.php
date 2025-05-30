<?php

namespace App\Http\Controllers;

use App\DataTables\JenisPupukDataTable;
use App\Models\JenisPupuk;
use App\Http\Requests\JenisPupukRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JenisPupukController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param JenisPupukDataTable $dataTable
     * @return \Illuminate\Http\Response
     */
    public function index(JenisPupukDataTable $dataTable)
    {
        $pageTitle = trans('global-message.list_form_title', ['form' => trans('Jenis Pupuk')]);
        $assets = ['data-table'];
        $headerAction = '<a href="' . route('jenis-pupuk.create') . '" class="btn btn-sm btn-primary" role="button">Add Jenis Pupuk</a>'
            . ' <a href="' . route('upload-data.upload') . '" class="btn btn-sm btn-success" role="button">Upload Jenis Pupuk File</a>';
        return $dataTable->render('global.datatable', compact('pageTitle', 'assets', 'headerAction'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('jenis-pupuk.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param JenisPupukRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(JenisPupukRequest $request)
    {
        try {
            // Validate the request data
            $data = $request->validated();

            // Create a new record in the database
            JenisPupuk::create($data);

            // Redirect to index with a success message
            return redirect()->route('jenis-pupuk.index')->withSuccess(__('Berhasil menambahkan Jenis Pupuk.'));
        } catch (\Exception $e) {
            // Log the error details
            Log::error('Error storing Jenis Pupuk: ' . $e->getMessage(), [
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
        $data = JenisPupuk::findOrFail($id);
        return view('jenis-pupuk.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = JenisPupuk::findOrFail($id);
        return view('jenis-pupuk.form', compact('data', 'id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param JenisPupukRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(JenisPupukRequest $request, $id)
    {
        try {
            $data = $request->validated();
            $jenisPupuk = JenisPupuk::findOrFail($id);
            $jenisPupuk->update($data);

            return redirect()->route('jenis-pupuk.index')->withSuccess(__('Berhasil Mengupdate Jenis Pupuk Data', ['name' => __('Jenis Pupuk')]));
        } catch (\Exception $e) {
            Log::error('Error updating Jenis Pupuk: ' . $e->getMessage());
            return redirect()->back()->withError(__('Gagal Mengupdate Jenis Pupuk Data', ['name' => __('Jenis Pupuk')]));
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
            $jenisPupuk = JenisPupuk::findOrFail($id);
            $jenisPupuk->delete();

            return response()->json([
                'success' => true,
                'message' => 'Record deleted successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to delete record.',
            ], 500);
        }
    }


    public function getJenisPupuk()
    {
        // Fetch the detail data for the selected blok
        $detail = JenisPupuk::pluck('nama_pupuk');
        return response()->json($detail);
    }
}
