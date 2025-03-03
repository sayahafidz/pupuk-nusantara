<?php

namespace App\Http\Controllers;

use App\DataTables\WhatsappDataTable;
use App\Http\Requests\WhatsappRequest;
use App\Models\User;
use App\Models\Whatsapp;
use Illuminate\Support\Facades\Http;
use App\Helpers\AuthHelper;

class WhatsAppController extends Controller
{
    public function sendData()
    {
        // Step 1: Fetch data from the first URL
        $dataUrl = 'https://picatanaman-data-pupuk-api.eg3bru.easypanel.host/data';
        $response = Http::get($dataUrl);

        if ($response->failed()) {
            return response()->json(['error' => 'Failed to fetch data from ' . $dataUrl], 500);
        }

        $data = $response->json();

        // Tanggal hari ini
        $date = date('d F Y');

        // Initialize totals
        $totalRealisasiTahun = 0;
        $totalRencanaTahun = 0;
        $totalPercentSemester1 = 0;
        $totalPercentSemester2 = 0;
        $totalPercentTahun = 0;

        $totalRegions = count($data);

        // Step 2: Calculate totals and averages
        foreach ($data as $item) {
            $totalRealisasiTahun += $item['Realisasi (Kg) Tahun'];
            $totalRencanaTahun += $item['Rencana (Kg) Tahun'];
            $totalPercentSemester1 += $item['% Real Thdp Renc Semester 1'];
            $totalPercentSemester2 += $item['% Real Thdp Renc Semester 2'];
            $totalPercentTahun += $item['% Real Thdp Renc Tahun'];
        }

        // Calculate average percentages
        $avgPercentSemester1 = $totalRegions > 0 ? $totalPercentSemester1 / $totalRegions : 0;
        $avgPercentSemester2 = $totalRegions > 0 ? $totalPercentSemester2 / $totalRegions : 0;
        $avgPercentTahun = $totalRegions > 0 ? $totalPercentTahun / $totalRegions : 0;

        // Format the totals
        $formattedText = "*Data Realisasi Pemupukan : {$date}*\n\n";
        $formattedText .= "_*Total (PalmCo):*_\n";
        $formattedText .= "% Real Thdp Renc Semester 1: " . number_format($avgPercentSemester1, 2, ',', '.') . "%\n";
        $formattedText .= "% Real Thdp Renc Semester 2: " . number_format($avgPercentSemester2, 2, ',', '.') . "%\n";
        $formattedText .= "% Real Thdp Renc Tahun: " . number_format($avgPercentTahun, 2, ',', '.') . "%\n";
        $formattedText .= "Realisasi (Kg) Tahun: " . number_format($totalRealisasiTahun, 0, ',', '.') . " Kg\n";
        $formattedText .= "Rencana (Kg) Tahun: " . number_format($totalRencanaTahun, 0, ',', '.') . " Kg\n";
        $formattedText .= "========================\n";

        // Step 3: Format the individual region data
        foreach ($data as $item) {
            $formattedText .= "_*Region: {$item['Region']}*_\n";
            $formattedText .= "% Real Thdp Renc Semester 1: {$item['% Real Thdp Renc Semester 1']}%\n";
            $formattedText .= "% Real Thdp Renc Semester 2: {$item['% Real Thdp Renc Semester 2']}%\n";
            $formattedText .= "% Real Thdp Renc Tahun: {$item['% Real Thdp Renc Tahun']}%\n";
            $formattedText .= "Realisasi (Kg) Tahun: " . number_format($item['Realisasi (Kg) Tahun'], 0, ',', '.') . " Kg\n";
            $formattedText .= "Rencana (Kg) Tahun: " . number_format($item['Rencana (Kg) Tahun'], 0, ',', '.') . " Kg\n";
            $formattedText .= "========================\n";
        }

        // Step 4: Fetch chat IDs from the database
        $chatIds = Whatsapp::pluck('phone')->toArray();

        // Step 5: Send data to multiple chat IDs
        $sendUrl = 'https://whatsapp.picatanaman.com/api/sendText';

        foreach ($chatIds as $chatId) {
            $payload = [
                'chatId' => $chatId,
                'text' => $formattedText,
                'session' => 'default',
            ];

            $sendResponse = Http::post($sendUrl, $payload);

            if ($sendResponse->failed()) {
                return response()->json([
                    'error' => 'Failed to send data to ' . $chatId,
                    'details' => $sendResponse->body(),
                ], 500);
            }
        }

        return response()->json(['success' => 'Data sent successfully to multiple chat IDs']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     *
     */
    public function index(WhatsappDataTable $dataTable)
    {
        $pageTitle = trans('global-message.list_form_title', ['form' => trans('Whatsapp Setting')]);
        $auth_user = AuthHelper::authSession(); // Added to match example convention, even if not used directly
        $assets = ['data-table'];
        $headerAction = '<a href="' . route('whatsapp.create') . '" class="btn btn-sm btn-primary" role="button">Add Whatsapp Data</a>'
            . ' <a href="#" class="btn btn-sm btn-success" role="button" onclick="sendWhatsappData()">Send Whatsapp Data</a>';

        return $dataTable->render('global.datatable', compact('pageTitle', 'auth_user', 'assets', 'headerAction'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     *
     */
    public function create()
    {

        // Fetch users to populate the user dropdown
        $users = User::orderBy('first_name', 'asc')->pluck('first_name', 'id'); // Assuming 'name' and 'id' fields exist in the User model

        return view('whatsapp.form', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     */
    public function store(WhatsappRequest $request)
    {
        $whatsapp = Whatsapp::create($request->all());

        return redirect()->route('whatsapp.index')->withSuccess(__('Data Berhasil Di Tambahkan', ['name' => __('Whatsapp Setting')]));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Whatsapp  $whatsapp
     * @return \Illuminate\Http\Response
     *
     */
    public function show($id)
    {
        $whatsapp = Whatsapp::findOrFail($id);

        return view('whatsapp.show', compact('whatsapp'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Whatsapp  $whatsapp
     * @return \Illuminate\Http\Response
     *
     */
    public function edit($id)
    {
        $whatsapp = Whatsapp::findOrFail($id);

        // Fetch users to populate the user dropdown
        $users = User::orderBy('first_name', 'asc')->pluck('first_name', 'id');

        return view('whatsapp.form', compact('whatsapp', 'users', 'id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Whatsapp  $whatsapp
     * @return \Illuminate\Http\Response
     *
     */
    public function update(WhatsappRequest $request, $id)
    {
        $whatsapp = Whatsapp::findOrFail($id);
        $whatsapp->update($request->all());

        return redirect()->route('whatsapp.index')->withSuccess(__('Data Berhasil Di Update', ['name' => __('Whatsapp Setting')]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Whatsapp  $whatsapp
     * @return \Illuminate\Http\Response
     *
     */
    public function destroy($id)
    {
        $whatsapp = Whatsapp::findOrFail($id);
        $whatsapp->delete();

        return redirect()->route('whatsapp.index')->withSuccess(__('message.msg_deleted', ['name' => __('Whatsapp Setting')]));
    }

    public function whatsappSetting()
    {
        $whatsapp = Whatsapp::first();

        return view('whatsapp.setting', compact('whatsapp'));
    }
}
