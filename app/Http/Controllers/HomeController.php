<?php

namespace App\Http\Controllers;

use App\Models\JenisPupuk;
use App\Models\MasterData;
use App\Models\Pemupukan;
use App\Models\RencanaPemupukan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /*
     * Dashboard Pages Routs
     */
    public function index(Request $request)
    {
        $assets = ['chart', 'animation'];

        $user = $request->user();
        $user_type = $user->user_type;
        $regional = $user->regional;

        if ($user_type == 'admin' && $regional == 'head_office') {
            // count all data rencana_pemupukan
            $rencana_pemupukan = RencanaPemupukan::count();
            // count all data pemupukan
            $pemupukan = Pemupukan::count();
            // count all data jenis_pupuk
            $jenis_pupuk = JenisPupuk::count();

            // count total jumlah_pupuk pemupukan
            $jumlah_pupuk = Pemupukan::sum('jumlah_pupuk');

            // count total jumlah_pupuk renacana_pemupukan
            $jumlah_pupuk_rencana = RencanaPemupukan::sum('jumlah_pupuk');

            // count total users
            $users = User::count();
        } elseif ($user_type == 'admin' && $regional != 'head_office') {
            // count all data rencana_pemupukan by regional
            $rencana_pemupukan = RencanaPemupukan::where('regional', $regional)->count();
            // count all data pemupukan by regional
            $pemupukan = Pemupukan::where('regional', $regional)->count();
            // count all data jenis_pupuk
            $jenis_pupuk = JenisPupuk::count();

            // count total jumlah_pupuk pemupukan by regional
            $jumlah_pupuk = Pemupukan::where('regional', $regional)->sum('jumlah_pupuk');

            // count total jumlah_pupuk renacana_pemupukan by regional
            $jumlah_pupuk_rencana = RencanaPemupukan::where('regional', $regional)->sum('jumlah_pupuk');

            // count total users by regional
            $users = User::where('regional', $regional)->count();
        } else {
            // handle other user types if necessary
            $rencana_pemupukan = 0;
            $pemupukan = 0;
            $jenis_pupuk = 0;
            $jumlah_pupuk = 0;
            $jumlah_pupuk_rencana = 0;
            $users = 0;
        }

        // get percentage of jumlah_pupuk pemupukan
        $percentage_pemupukan = ($jumlah_pupuk_rencana > 0) ? ($jumlah_pupuk / $jumlah_pupuk_rencana) * 100 : 0;
        
        //
        $tableData = $this->tableRencanaDanPemupukan($request);

        return view('dashboards.dashboard', compact('assets', 'rencana_pemupukan', 'pemupukan', 'jenis_pupuk', 'jumlah_pupuk', 'jumlah_pupuk_rencana', 'percentage_pemupukan', 'users', 'user', 'tableData'));
    }

    /**
     * Tabel rencana dan realisasi pemupukan dengan caching dan pengurutan kustom
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function tableRencanaDanPemupukan(Request $request)
    {
        // Cache key dengan timestamp hari
        $cacheKey = 'table_rencana_pemupukan_ordered_' . date('Y-m-d');
        
        // Cek apakah data ada di cache
        return Cache::remember($cacheKey, now()->addMinutes(30), function () {
            // Definisikan pengelompokan dan urutan RPC yang diinginkan
            $summaryEntities = [
                'Palm Co Regional 1 + KSO' => ['RPC1', 'DSMTU', 'DATIM', 'DJABA'],
                'Palm Co Regional 2 + KSO' => ['RPC2', 'RPC2N2', 'RPC2N14'],
            ];
            
            // Entitas individual yang tidak dalam grup
            $individualEntities = ['RPC3', 'RPC4', 'RPC5', 'REG6', 'REG7'];
            
            // Get distinct RPC dari master_data
            $entitas = DB::table('master_data')
                ->select('rpc')
                ->distinct()
                ->whereNotNull('rpc')
                ->pluck('rpc')
                ->toArray();
            
            // Kategorikan entitas berdasarkan jenisnya untuk total akhir
            $palmCoEntities = array_filter($entitas, function($rpc) {
                return strpos($rpc, 'RPC') === 0;
            });
            
            $regionalEntities = array_filter($entitas, function($rpc) {
                return strpos($rpc, 'REG') === 0 || strpos($rpc, 'DSM') === 0 || 
                    strpos($rpc, 'DAT') === 0 || strpos($rpc, 'DJA') === 0;
            });
            
            // Definisi final summary
            $finalSummary = [
                'Total Palm Co' => $palmCoEntities,
                'Total Regional KSO' => $regionalEntities,
                'HOLDING' => $entitas // Semua entitas
            ];
            
            // Ambil semua data rencana dan realisasi sekaligus
            $rencanaTunggalData = $this->getAllRencanaPemupukanData($entitas, false);
            $rencanaMajemukData = $this->getAllRencanaPemupukanData($entitas, true);
            
            $realisasiTunggalData = $this->getAllRealisasiPemupukanData($entitas, false);
            $realisasiMajemukData = $this->getAllRealisasiPemupukanData($entitas, true);
            
            $tableData = [];
            
            // Proses setiap grup entitas
            foreach ($summaryEntities as $groupName => $groupEntities) {
                // Tambahkan data untuk setiap entitas dalam grup
                foreach ($groupEntities as $rpc) {
                    // Menyiapkan data pupuk tunggal
                    $pupukTunggalData = $this->preparePupukData(
                        $rpc, 
                        'Pupuk Tunggal', 
                        $rencanaTunggalData[$rpc] ?? [],
                        $realisasiTunggalData[$rpc] ?? []
                    );
                    
                    // Menyiapkan data pupuk majemuk
                    $pupukMajemukData = $this->preparePupukData(
                        $rpc, 
                        'Pupuk Majemuk',
                        $rencanaMajemukData[$rpc] ?? [],
                        $realisasiMajemukData[$rpc] ?? []
                    );
                    
                    // Menyiapkan data jumlah
                    $jumlahData = $this->calculateTotalData($pupukTunggalData, $pupukMajemukData);
                    
                    // Menambahkan ke data tabel
                    $tableData[] = $pupukTunggalData;
                    $tableData[] = $pupukMajemukData;
                    $tableData[] = $jumlahData;
                }
                
                // Tambahkan total grup dengan format yang seragam
                $this->addFormattedGroupTotal($tableData, $groupName, $groupEntities, 
                    $rencanaTunggalData, $rencanaMajemukData, $realisasiTunggalData, $realisasiMajemukData);
            }
            
            // Tambahkan entity individual tanpa dikelompokkan
            foreach ($individualEntities as $rpc) {
                // Menyiapkan data pupuk tunggal
                $pupukTunggalData = $this->preparePupukData(
                    $rpc, 
                    'Pupuk Tunggal', 
                    $rencanaTunggalData[$rpc] ?? [],
                    $realisasiTunggalData[$rpc] ?? []
                );
                
                // Menyiapkan data pupuk majemuk
                $pupukMajemukData = $this->preparePupukData(
                    $rpc, 
                    'Pupuk Majemuk',
                    $rencanaMajemukData[$rpc] ?? [],
                    $realisasiMajemukData[$rpc] ?? []
                );
                
                // Menyiapkan data jumlah
                $jumlahData = $this->calculateTotalData($pupukTunggalData, $pupukMajemukData);
                
                // Menambahkan ke data tabel
                $tableData[] = $pupukTunggalData;
                $tableData[] = $pupukMajemukData;
                $tableData[] = $jumlahData;
            }
            
            // Tambahkan total-total akhir
            foreach ($finalSummary as $groupName => $groupEntities) {
                $this->addFormattedGroupTotal($tableData, $groupName, $groupEntities, 
                    $rencanaTunggalData, $rencanaMajemukData, $realisasiTunggalData, $realisasiMajemukData);
            }
            
            return $tableData;
        });
    }

    /**
     * Menambahkan total grup berdasarkan kategori dengan format konsisten
     * 
     * @param array &$tableData
     * @param string $groupName
     * @param array $entities
     * @param array $rencanaTunggalData
     * @param array $rencanaMajemukData
     * @param array $realisasiTunggalData
     * @param array $realisasiMajemukData
     */
    private function addFormattedGroupTotal(&$tableData, $groupName, $entities, $rencanaTunggalData, $rencanaMajemukData, 
                                $realisasiTunggalData, $realisasiMajemukData)
    {
        // 1. Hitung total untuk pupuk tunggal
        $totalTunggal = $this->calculateCategoryTotal(
            $entities, 
            $rencanaTunggalData, 
            $realisasiTunggalData
        );
        
        // 2. Hitung total untuk pupuk majemuk
        $totalMajemuk = $this->calculateCategoryTotal(
            $entities, 
            $rencanaMajemukData, 
            $realisasiMajemukData
        );
        
        // 3. Hitung total gabungan (jumlah)
        $totalJumlah = [
            'semester1_rencana' => $totalTunggal['semester1_rencana'] + $totalMajemuk['semester1_rencana'],
            'semester1_realisasi' => $totalTunggal['semester1_realisasi'] + $totalMajemuk['semester1_realisasi'],
            'semester2_rencana' => $totalTunggal['semester2_rencana'] + $totalMajemuk['semester2_rencana'],
            'semester2_realisasi' => $totalTunggal['semester2_realisasi'] + $totalMajemuk['semester2_realisasi'],
        ];
        
        // Hitung tahun dan persentase
        $totalJumlah['tahun_rencana'] = $totalJumlah['semester1_rencana'] + $totalJumlah['semester2_rencana'];
        $totalJumlah['tahun_realisasi'] = $totalJumlah['semester1_realisasi'] + $totalJumlah['semester2_realisasi'];
        
        // Hitung persentase
        $totalJumlah['semester1_percentage'] = ($totalJumlah['semester1_rencana'] > 0) ? 
            ($totalJumlah['semester1_realisasi'] / $totalJumlah['semester1_rencana']) * 100 : 0;
        
        $totalJumlah['semester2_percentage'] = ($totalJumlah['semester2_rencana'] > 0) ? 
            ($totalJumlah['semester2_realisasi'] / $totalJumlah['semester2_rencana']) * 100 : 0;
        
        $totalJumlah['tahun_percentage'] = ($totalJumlah['tahun_rencana'] > 0) ? 
            ($totalJumlah['tahun_realisasi'] / $totalJumlah['tahun_rencana']) * 100 : 0;
        
        // Tambahkan ke tabel data dengan format yang konsisten
        $tableData[] = [
            'entitas' => $groupName,
            'kebun' => 'Pupuk Tunggal',
            'semester1_rencana' => $totalTunggal['semester1_rencana'],
            'semester1_realisasi' => $totalTunggal['semester1_realisasi'],
            'semester1_percentage' => $totalTunggal['semester1_percentage'],
            'semester2_rencana' => $totalTunggal['semester2_rencana'],
            'semester2_realisasi' => $totalTunggal['semester2_realisasi'],
            'semester2_percentage' => $totalTunggal['semester2_percentage'],
            'tahun_rencana' => $totalTunggal['tahun_rencana'],
            'tahun_realisasi' => $totalTunggal['tahun_realisasi'],
            'tahun_percentage' => $totalTunggal['tahun_percentage'],
            'is_group_total' => true
        ];
        
        $tableData[] = [
            'entitas' => $groupName,
            'kebun' => 'Pupuk Majemuk',
            'semester1_rencana' => $totalMajemuk['semester1_rencana'],
            'semester1_realisasi' => $totalMajemuk['semester1_realisasi'],
            'semester1_percentage' => $totalMajemuk['semester1_percentage'],
            'semester2_rencana' => $totalMajemuk['semester2_rencana'],
            'semester2_realisasi' => $totalMajemuk['semester2_realisasi'],
            'semester2_percentage' => $totalMajemuk['semester2_percentage'],
            'tahun_rencana' => $totalMajemuk['tahun_rencana'],
            'tahun_realisasi' => $totalMajemuk['tahun_realisasi'],
            'tahun_percentage' => $totalMajemuk['tahun_percentage'],
            'is_group_total' => true
        ];
        
        $tableData[] = [
            'entitas' => $groupName,
            'kebun' => 'Jumlah',
            'semester1_rencana' => $totalJumlah['semester1_rencana'],
            'semester1_realisasi' => $totalJumlah['semester1_realisasi'],
            'semester1_percentage' => $totalJumlah['semester1_percentage'],
            'semester2_rencana' => $totalJumlah['semester2_rencana'],
            'semester2_realisasi' => $totalJumlah['semester2_realisasi'],
            'semester2_percentage' => $totalJumlah['semester2_percentage'],
            'tahun_rencana' => $totalJumlah['tahun_rencana'],
            'tahun_realisasi' => $totalJumlah['tahun_realisasi'],
            'tahun_percentage' => $totalJumlah['tahun_percentage'],
            'is_group_total' => true
        ];
    }

    /**
     * Menghitung total untuk kategori tertentu
     * 
     * @param array $entities
     * @param array $rencanaData
     * @param array $realisasiData
     * @return array
     */
    private function calculateCategoryTotal($entities, $rencanaData, $realisasiData)
    {
        $semester1Rencana = 0;
        $semester1Realisasi = 0;
        $semester2Rencana = 0;
        $semester2Realisasi = 0;
        
        foreach ($entities as $entity) {
            // Skip jika data entitas tidak ada
            if (!isset($rencanaData[$entity]) && !isset($realisasiData[$entity])) {
                continue;
            }
            
            // Tambahkan data rencana
            $semester1Rencana += ($rencanaData[$entity]['semester1'] ?? 0);
            $semester2Rencana += ($rencanaData[$entity]['semester2'] ?? 0);
            
            // Tambahkan data realisasi
            $semester1Realisasi += ($realisasiData[$entity]['semester1'] ?? 0);
            $semester2Realisasi += ($realisasiData[$entity]['semester2'] ?? 0);
        }
        
        // Hitung total tahunan
        $tahunRencana = $semester1Rencana + $semester2Rencana;
        $tahunRealisasi = $semester1Realisasi + $semester2Realisasi;
        
        // Hitung persentase
        $semester1Percentage = ($semester1Rencana > 0) ? 
            ($semester1Realisasi / $semester1Rencana) * 100 : 0;
        $semester2Percentage = ($semester2Rencana > 0) ? 
            ($semester2Realisasi / $semester2Rencana) * 100 : 0;
        $tahunPercentage = ($tahunRencana > 0) ? 
            ($tahunRealisasi / $tahunRencana) * 100 : 0;
        
        return [
            'semester1_rencana' => $semester1Rencana,
            'semester1_realisasi' => $semester1Realisasi,
            'semester1_percentage' => $semester1Percentage,
            'semester2_rencana' => $semester2Rencana,
            'semester2_realisasi' => $semester2Realisasi,
            'semester2_percentage' => $semester2Percentage,
            'tahun_rencana' => $tahunRencana,
            'tahun_realisasi' => $tahunRealisasi,
            'tahun_percentage' => $tahunPercentage,
        ];
    }

    /**
     * Mengambil semua data rencana pemupukan untuk mengurangi jumlah query
     * 
     * @param array $entitas
     * @param bool $isMajemuk
     * @return array
     */
    private function getAllRencanaPemupukanData($entitas, $isMajemuk)
    {
        $jenisPupukCondition = $isMajemuk ? "jenis_pupuk LIKE '%NPK%'" : "jenis_pupuk NOT LIKE '%NPK%'";
        
        $rencanaData = DB::table('rencana_pemupukan')
            ->select('regional', 'semester_pemupukan', DB::raw('SUM(jumlah_pupuk) as total'))
            ->whereIn('regional', $entitas)
            ->whereRaw($jenisPupukCondition)
            ->groupBy('regional', 'semester_pemupukan')
            ->get();
        
        // Menyusun data dalam format [rpc][semester] = nilai
        $result = [];
        foreach ($rencanaData as $item) {
            if (!isset($result[$item->regional])) {
                $result[$item->regional] = [
                    'semester1' => 0,
                    'semester2' => 0
                ];
            }
            
            $semesterKey = 'semester' . $item->semester_pemupukan;
            $result[$item->regional][$semesterKey] = $item->total;
        }
        
        return $result;
    }

    /**
     * Mengambil semua data realisasi pemupukan untuk mengurangi jumlah query
     * 
     * @param array $entitas
     * @param bool $isMajemuk
     * @return array
     */
    private function getAllRealisasiPemupukanData($entitas, $isMajemuk)
    {
        $jenisPupukCondition = $isMajemuk ? "jenis_pupuk LIKE '%NPK%'" : "jenis_pupuk NOT LIKE '%NPK%'";
        
        // Query untuk semester 1
        $realisasiSemester1 = DB::table('pemupukan')
            ->select('regional', DB::raw('SUM(jumlah_pupuk) as total'))
            ->whereIn('regional', $entitas)
            ->whereRaw('MONTH(tgl_pemupukan) BETWEEN 1 AND 6')
            ->whereRaw($jenisPupukCondition)
            ->groupBy('regional')
            ->get();
        
        // Query untuk semester 2
        $realisasiSemester2 = DB::table('pemupukan')
            ->select('regional', DB::raw('SUM(jumlah_pupuk) as total'))
            ->whereIn('regional', $entitas)
            ->whereRaw('MONTH(tgl_pemupukan) BETWEEN 7 AND 12')
            ->whereRaw($jenisPupukCondition)
            ->groupBy('regional')
            ->get();
        
        // Menyusun data
        $result = [];
        foreach ($entitas as $rpc) {
            $result[$rpc] = [
                'semester1' => 0,
                'semester2' => 0
            ];
        }
        
        foreach ($realisasiSemester1 as $item) {
            $result[$item->regional]['semester1'] = $item->total;
        }
        
        foreach ($realisasiSemester2 as $item) {
            $result[$item->regional]['semester2'] = $item->total;
        }
        
        return $result;
    }

    /**
     * Menyiapkan data pupuk (struktur dan kalkulasi)
     * 
     * @param string $rpc
     * @param string $kebun
     * @param array $rencanaData
     * @param array $realisasiData
     * @return array
     */
    private function preparePupukData($rpc, $kebun, $rencanaData, $realisasiData)
    {
        $semester1Rencana = $rencanaData['semester1'] ?? 0;
        $semester2Rencana = $rencanaData['semester2'] ?? 0;
        $semester1Realisasi = $realisasiData['semester1'] ?? 0;
        $semester2Realisasi = $realisasiData['semester2'] ?? 0;
        
        $tahunRencana = $semester1Rencana + $semester2Rencana;
        $tahunRealisasi = $semester1Realisasi + $semester2Realisasi;
        
        return [
            'entitas' => $rpc,
            'kebun' => $kebun,
            'semester1_rencana' => $semester1Rencana,
            'semester1_realisasi' => $semester1Realisasi,
            'semester1_percentage' => ($semester1Rencana > 0) ? 
                ($semester1Realisasi / $semester1Rencana) * 100 : 0,
            'semester2_rencana' => $semester2Rencana,
            'semester2_realisasi' => $semester2Realisasi,
            'semester2_percentage' => ($semester2Rencana > 0) ? 
                ($semester2Realisasi / $semester2Rencana) * 100 : 0,
            'tahun_rencana' => $tahunRencana,
            'tahun_realisasi' => $tahunRealisasi,
            'tahun_percentage' => ($tahunRencana > 0) ? 
                ($tahunRealisasi / $tahunRencana) * 100 : 0,
        ];
    }

    /**
     * Menghitung data total dari pupuk tunggal dan majemuk
     * 
     * @param array $pupukTunggalData
     * @param array $pupukMajemukData
     * @return array
     */
    private function calculateTotalData($pupukTunggalData, $pupukMajemukData)
    {
        $semester1Rencana = $pupukTunggalData['semester1_rencana'] + $pupukMajemukData['semester1_rencana'];
        $semester1Realisasi = $pupukTunggalData['semester1_realisasi'] + $pupukMajemukData['semester1_realisasi'];
        $semester2Rencana = $pupukTunggalData['semester2_rencana'] + $pupukMajemukData['semester2_rencana'];
        $semester2Realisasi = $pupukTunggalData['semester2_realisasi'] + $pupukMajemukData['semester2_realisasi'];
        $tahunRencana = $pupukTunggalData['tahun_rencana'] + $pupukMajemukData['tahun_rencana'];
        $tahunRealisasi = $pupukTunggalData['tahun_realisasi'] + $pupukMajemukData['tahun_realisasi'];
        
        return [
            'entitas' => $pupukTunggalData['entitas'],
            'kebun' => 'Jumlah',
            'semester1_rencana' => $semester1Rencana,
            'semester1_realisasi' => $semester1Realisasi,
            'semester1_percentage' => ($semester1Rencana > 0) ? 
                ($semester1Realisasi / $semester1Rencana) * 100 : 0,
            'semester2_rencana' => $semester2Rencana,
            'semester2_realisasi' => $semester2Realisasi,
            'semester2_percentage' => ($semester2Rencana > 0) ? 
                ($semester2Realisasi / $semester2Rencana) * 100 : 0,
            'tahun_rencana' => $tahunRencana,
            'tahun_realisasi' => $tahunRealisasi,
            'tahun_percentage' => ($tahunRencana > 0) ? 
                ($tahunRealisasi / $tahunRencana) * 100 : 0,
        ];
    }

    /**
     * Mendapatkan detail kebun berdasarkan entitas dan jenis pupuk dengan caching
     * Mendukung juga entitas kelompok
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getKebunDetails(Request $request)
    {
        $entitas = $request->input('entitas');
        $jenisPupuk = $request->input('jenis_pupuk');
        
        $cacheKey = 'kebun_details_' . md5($entitas . '_' . $jenisPupuk . '_' . date('Y-m-d'));
        
        $kebunData = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($entitas, $jenisPupuk) {
            // Daftar grup entitas
            $groupEntities = [
                'Palm Co Regional 1 + KSO' => ['RPC1', 'DSMTU', 'DATIM', 'DJABA'],
                'Palm Co Regional 2 + KSO' => ['RPC2', 'RPC2N2', 'RPC2N14'],
                'Total Palm Co' => [],
                'Total Regional KSO' => [],
                'HOLDING' => []
            ];
            
            // Kategorikan entitas untuk Total Palm Co dan Total Regional KSO
            $allEntities = DB::table('master_data')->select('rpc')->distinct()->pluck('rpc')->toArray();
            
            $palmCoEntities = array_filter($allEntities, function($rpc) {
                return strpos($rpc, 'RPC') === 0;
            });
            
            $regionalEntities = array_filter($allEntities, function($rpc) {
                return strpos($rpc, 'REG') === 0 || strpos($rpc, 'DSM') === 0 || 
                    strpos($rpc, 'DAT') === 0 || strpos($rpc, 'DJA') === 0;
            });
            
            $groupEntities['Total Palm Co'] = $palmCoEntities;
            $groupEntities['Total Regional KSO'] = $regionalEntities;
            $groupEntities['HOLDING'] = $allEntities;
            
            // Cek apakah entitas adalah grup
            $isGroup = isset($groupEntities[$entitas]);
            $entities = $isGroup ? $groupEntities[$entitas] : [$entitas];
            
            // Tentukan apakah kita mencari NPK (pupuk majemuk) atau tidak (pupuk tunggal)
            $isPupukMajemuk = ($jenisPupuk === 'Pupuk Majemuk');
            $jenisPupukCondition = $isPupukMajemuk ? "jenis_pupuk LIKE '%NPK%'" : "jenis_pupuk NOT LIKE '%NPK%'";
            
            $kebunData = [];
            
            // Jika ini adalah grup, tampilkan data agregat per entitas
            if ($isGroup) {
                foreach ($entities as $entity) {
                    // Get all kebun associated with this entity
                    $kebunList = DB::table('master_data')
                        ->where('rpc', $entity)
                        ->select('kode_kebun', 'nama_kebun')
                        ->distinct()
                        ->get();
                    
                    // Ambil data rencana untuk entity ini
                    $rencanaData = $this->getKebunRencanaData($entity, $jenisPupukCondition);
                    
                    // Ambil data realisasi untuk entity ini
                    $realisasiData = $this->getKebunRealisasiData($entity, $jenisPupukCondition);
                    
                    // Hitung total per entity (bukan per kebun)
                    $totalRencanaSemester1 = array_sum($rencanaData['semester1']);
                    $totalRencanaSemester2 = array_sum($rencanaData['semester2']);
                    $totalRealisasiSemester1 = array_sum($realisasiData['semester1']);
                    $totalRealisasiSemester2 = array_sum($realisasiData['semester2']);
                    
                    // Hitung persentase
                    $semester1Percentage = ($totalRencanaSemester1 > 0) ? 
                        ($totalRealisasiSemester1 / $totalRencanaSemester1) * 100 : 0;
                        
                    $semester2Percentage = ($totalRencanaSemester2 > 0) ? 
                        ($totalRealisasiSemester2 / $totalRencanaSemester2) * 100 : 0;
                    
                    $tahunRencana = $totalRencanaSemester1 + $totalRencanaSemester2;
                    $tahunRealisasi = $totalRealisasiSemester1 + $totalRealisasiSemester2;
                    $tahunPercentage = ($tahunRencana > 0) ? 
                        ($tahunRealisasi / $tahunRencana) * 100 : 0;
                    
                    $kebunData[] = [
                        'entitas' => $entitas,
                        'kode_kebun' => $entity,
                        'kebun' => $entity,
                        'semester1_rencana' => $totalRencanaSemester1,
                        'semester1_realisasi' => $totalRealisasiSemester1,
                        'semester1_percentage' => $semester1Percentage,
                        'semester2_rencana' => $totalRencanaSemester2,
                        'semester2_realisasi' => $totalRealisasiSemester2,
                        'semester2_percentage' => $semester2Percentage,
                        'tahun_rencana' => $tahunRencana,
                        'tahun_realisasi' => $tahunRealisasi,
                        'tahun_percentage' => $tahunPercentage,
                    ];
                }
                
            } else {
                // Ini adalah entitas tunggal, tampilkan detail per kebun seperti biasa
                $kebunList = DB::table('master_data')
                    ->where('rpc', $entitas)
                    ->select('kode_kebun', 'nama_kebun')
                    ->distinct()
                    ->get();
                
                // Ambil data rencana pemupukan sekali untuk semua kebun
                $rencanaData = $this->getKebunRencanaData($entitas, $jenisPupukCondition);
                
                // Ambil data realisasi pemupukan sekali untuk semua kebun
                $realisasiData = $this->getKebunRealisasiData($entitas, $jenisPupukCondition);
                
                foreach ($kebunList as $kebun) {
                    $kodeKebun = $kebun->kode_kebun;
                    $namaKebun = $kebun->nama_kebun;
                    
                    // Ambil data rencana dari hasil query sebelumnya
                    $rencanaSemester1 = $rencanaData['semester1'][$kodeKebun] ?? 0;
                    $rencanaSemester2 = $rencanaData['semester2'][$kodeKebun] ?? 0;
                    
                    // Ambil data realisasi dari hasil query sebelumnya
                    $realisasiSemester1 = $realisasiData['semester1'][$kodeKebun] ?? 0;
                    $realisasiSemester2 = $realisasiData['semester2'][$kodeKebun] ?? 0;
                    
                    // Hitung persentase
                    $semester1Percentage = ($rencanaSemester1 > 0) ? 
                        ($realisasiSemester1 / $rencanaSemester1) * 100 : 0;
                        
                    $semester2Percentage = ($rencanaSemester2 > 0) ? 
                        ($realisasiSemester2 / $rencanaSemester2) * 100 : 0;
                    
                    $tahunRencana = $rencanaSemester1 + $rencanaSemester2;
                    $tahunRealisasi = $realisasiSemester1 + $realisasiSemester2;
                    $tahunPercentage = ($tahunRencana > 0) ? 
                        ($tahunRealisasi / $tahunRencana) * 100 : 0;
                    
                    $kebunData[] = [
                        'entitas' => $entitas,
                        'kode_kebun' => $kodeKebun,
                        'kebun' => $namaKebun,
                        'semester1_rencana' => $rencanaSemester1,
                        'semester1_realisasi' => $realisasiSemester1,
                        'semester1_percentage' => $semester1Percentage,
                        'semester2_rencana' => $rencanaSemester2,
                        'semester2_realisasi' => $realisasiSemester2,
                        'semester2_percentage' => $semester2Percentage,
                        'tahun_rencana' => $tahunRencana,
                        'tahun_realisasi' => $tahunRealisasi,
                        'tahun_percentage' => $tahunPercentage,
                    ];
                }
            }
            
            return $kebunData;
        });
        
        // Return view dengan data
        return view('dashboards.kebun-details-table', [
            'kebunData' => $kebunData,
            'jenisPupuk' => $jenisPupuk,
            'entitas' => $entitas,
            'isGroup' => isset($groupEntities[$entitas])
        ]);
    }
    
    /**
     * Mendapatkan data rencana untuk semua kebun dengan sekali query
     * 
     * @param string $entitas
     * @param string $jenisPupukCondition
     * @return array
     */
    private function getKebunRencanaData($entitas, $jenisPupukCondition)
    {
        // Query untuk semester 1
        $rencanaSemester1 = DB::table('rencana_pemupukan')
            ->select('kebun', DB::raw('SUM(jumlah_pupuk) as total'))
            ->where('regional', $entitas)
            ->where('semester_pemupukan', 1)
            ->whereRaw($jenisPupukCondition)
            ->groupBy('kebun')
            ->pluck('total', 'kebun')
            ->toArray();
        
        // Query untuk semester 2
        $rencanaSemester2 = DB::table('rencana_pemupukan')
            ->select('kebun', DB::raw('SUM(jumlah_pupuk) as total'))
            ->where('regional', $entitas)
            ->where('semester_pemupukan', 2)
            ->whereRaw($jenisPupukCondition)
            ->groupBy('kebun')
            ->pluck('total', 'kebun')
            ->toArray();
        
        return [
            'semester1' => $rencanaSemester1,
            'semester2' => $rencanaSemester2
        ];
    }
    
    /**
     * Mendapatkan data realisasi untuk semua kebun dengan sekali query
     * 
     * @param string $entitas
     * @param string $jenisPupukCondition
     * @return array
     */
    private function getKebunRealisasiData($entitas, $jenisPupukCondition)
    {
        // Query untuk semester 1
        $realisasiSemester1 = DB::table('pemupukan')
            ->select('kebun', DB::raw('SUM(jumlah_pupuk) as total'))
            ->where('regional', $entitas)
            ->whereRaw('MONTH(tgl_pemupukan) BETWEEN 1 AND 6')
            ->whereRaw($jenisPupukCondition)
            ->groupBy('kebun')
            ->pluck('total', 'kebun')
            ->toArray();
        
        // Query untuk semester 2
        $realisasiSemester2 = DB::table('pemupukan')
            ->select('kebun', DB::raw('SUM(jumlah_pupuk) as total'))
            ->where('regional', $entitas)
            ->whereRaw('MONTH(tgl_pemupukan) BETWEEN 7 AND 12')
            ->whereRaw($jenisPupukCondition)
            ->groupBy('kebun')
            ->pluck('total', 'kebun')
            ->toArray();
        
        return [
            'semester1' => $realisasiSemester1,
            'semester2' => $realisasiSemester2
        ];
    }

    /**
     * Mendapatkan detail afdeling berdasarkan kebun dan jenis pupuk dengan caching
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getAfdelingDetails(Request $request)
    {
        $kebun = $request->input('kebun');
        $jenisPupuk = $request->input('jenis_pupuk');
        $entitas = $request->input('entitas');
        
        // Hapus cache untuk memaksa refresh data saat debugging
        $cacheKey = 'afdeling_details_' . md5($entitas . '_' . $kebun . '_' . $jenisPupuk . '_' . date('Y-m-d'));
        Cache::forget($cacheKey);
        
        $afdelingData = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($kebun, $jenisPupuk, $entitas) {
            // Tentukan apakah kita mencari NPK (pupuk majemuk) atau tidak (pupuk tunggal)
            $isPupukMajemuk = ($jenisPupuk === 'Pupuk Majemuk');
            $jenisPupukCondition = $isPupukMajemuk ? "jenis_pupuk LIKE '%NPK%'" : "jenis_pupuk NOT LIKE '%NPK%'";
            
            // Coba dapatkan kode kebun dari master_data
            $kodeKebun = null;
            $kebunRecord = DB::table('master_data')
                ->where('nama_kebun', $kebun)
                ->orWhere('kode_kebun', $kebun)
                ->select('kode_kebun')
                ->first();
                
            if ($kebunRecord) {
                $kodeKebun = $kebunRecord->kode_kebun;
            } else {
                // Jika tidak ditemukan, gunakan kebun asli sebagai kode
                $kodeKebun = $kebun;
            }
            
            // Ambil semua afdeling untuk kebun ini dari berbagai tabel
            $afdelingFromMaster = DB::table('master_data')
                ->where('kode_kebun', $kodeKebun)
                ->orWhere('nama_kebun', $kebun)
                ->select('afdeling')
                ->distinct()
                ->whereNotNull('afdeling')
                ->where('afdeling', '!=', '')
                ->get();
                
            // Cek di tabel rencana_pemupukan
            $afdelingFromRencana = DB::table('rencana_pemupukan')
                ->where(function($query) use ($kodeKebun, $kebun, $entitas) {
                    $query->where('kebun', $kodeKebun)
                          ->orWhere('kebun', $kebun);
                          
                    if ($entitas) {
                        $query->where('regional', $entitas);
                    }
                })
                ->select('afdeling')
                ->distinct()
                ->whereNotNull('afdeling')
                ->where('afdeling', '!=', '')
                ->get();
                
            // Cek di tabel pemupukan
            $afdelingFromRealisasi = DB::table('pemupukan')
                ->where(function($query) use ($kodeKebun, $kebun, $entitas) {
                    $query->where('kebun', $kodeKebun)
                          ->orWhere('kebun', $kebun);
                          
                    if ($entitas) {
                        $query->where('regional', $entitas);
                    }
                })
                ->select('afdeling')
                ->distinct()
                ->whereNotNull('afdeling')
                ->where('afdeling', '!=', '')
                ->get();
                
            // Gabungkan hasil dari semua tabel
            $mergedAfdelings = collect([]);
            
            foreach ($afdelingFromMaster as $afd) {
                if (!$mergedAfdelings->contains('afdeling', $afd->afdeling)) {
                    $mergedAfdelings->push($afd);
                }
            }
            
            foreach ($afdelingFromRencana as $afd) {
                if (!$mergedAfdelings->contains('afdeling', $afd->afdeling)) {
                    $mergedAfdelings->push($afd);
                }
            }
            
            foreach ($afdelingFromRealisasi as $afd) {
                if (!$mergedAfdelings->contains('afdeling', $afd->afdeling)) {
                    $mergedAfdelings->push($afd);
                }
            }
            
            $afdelingList = $mergedAfdelings->sortBy('afdeling')->values();
            
            // Ambil data rencana dan realisasi untuk semua afdeling di kebun ini
            $rencanaData = $this->getAfdelingRencanaData($kodeKebun, $kebun, $entitas, $jenisPupukCondition);
            $realisasiData = $this->getAfdelingRealisasiData($kodeKebun, $kebun, $entitas, $jenisPupukCondition);
            
            $afdelingData = [];
            
            // Jika tidak ada data afdeling, tambahkan afdeling dummy untuk informasi
            if ($afdelingList->isEmpty()) {
                // Tambahkan informasi tidak ada data
                $afdelingData[] = [
                    'kebun' => $kebun,
                    'afdeling' => 'Tidak ada data afdeling',
                    'semester1_rencana' => 0,
                    'semester1_realisasi' => 0,
                    'semester1_percentage' => 0,
                    'semester2_rencana' => 0,
                    'semester2_realisasi' => 0,
                    'semester2_percentage' => 0,
                    'tahun_rencana' => 0,
                    'tahun_realisasi' => 0,
                    'tahun_percentage' => 0,
                ];
                
            } else {
                // Proses data afdeling yang ada
                foreach ($afdelingList as $afdeling) {
                    $kodeAfdeling = $afdeling->afdeling;
                    
                    // Ambil data rencana dari hasil query sebelumnya
                    $rencanaSemester1 = $rencanaData['semester1'][$kodeAfdeling] ?? 0;
                    $rencanaSemester2 = $rencanaData['semester2'][$kodeAfdeling] ?? 0;
                    
                    // Ambil data realisasi dari hasil query sebelumnya
                    $realisasiSemester1 = $realisasiData['semester1'][$kodeAfdeling] ?? 0;
                    $realisasiSemester2 = $realisasiData['semester2'][$kodeAfdeling] ?? 0;
                    
                    // Hitung persentase
                    $semester1Percentage = ($rencanaSemester1 > 0) ? 
                        ($realisasiSemester1 / $rencanaSemester1) * 100 : 0;
                        
                    $semester2Percentage = ($rencanaSemester2 > 0) ? 
                        ($realisasiSemester2 / $rencanaSemester2) * 100 : 0;
                    
                    $tahunRencana = $rencanaSemester1 + $rencanaSemester2;
                    $tahunRealisasi = $realisasiSemester1 + $realisasiSemester2;
                    $tahunPercentage = ($tahunRencana > 0) ? 
                        ($tahunRealisasi / $tahunRencana) * 100 : 0;
                    
                    $afdelingData[] = [
                        'kebun' => $kebun,
                        'afdeling' => $kodeAfdeling,
                        'semester1_rencana' => $rencanaSemester1,
                        'semester1_realisasi' => $realisasiSemester1,
                        'semester1_percentage' => $semester1Percentage,
                        'semester2_rencana' => $rencanaSemester2,
                        'semester2_realisasi' => $realisasiSemester2,
                        'semester2_percentage' => $semester2Percentage,
                        'tahun_rencana' => $tahunRencana,
                        'tahun_realisasi' => $tahunRealisasi,
                        'tahun_percentage' => $tahunPercentage,
                    ];
                }
            }
            
            return $afdelingData;
        });
        
        // Return view dengan data
        return view('dashboards.afdeling-details-table', [
            'afdelingData' => $afdelingData,
            'jenisPupuk' => $jenisPupuk,
            'kebun' => $kebun,
            'entitas' => $entitas
        ]);
    }

    /**
     * Mendapatkan data rencana untuk semua afdeling dengan sekali query
     * 
     * @param string $kodeKebun
     * @param string $namaKebun
     * @param string $entitas
     * @param string $jenisPupukCondition
     * @return array
     */
    private function getAfdelingRencanaData($kodeKebun, $namaKebun, $entitas, $jenisPupukCondition)
    {
        // Query untuk semester 1
        $queryBuilder = DB::table('rencana_pemupukan')
            ->select('afdeling', DB::raw('SUM(jumlah_pupuk) as total'))
            ->where(function($query) use ($kodeKebun, $namaKebun) {
                $query->where('kebun', $kodeKebun)
                    ->orWhere('kebun', $namaKebun);
            })
            ->where('semester_pemupukan', 1)
            ->whereRaw($jenisPupukCondition);
            
        // Tambahkan filter regional jika ada
        if ($entitas) {
            $queryBuilder->where('regional', $entitas);
        }
        
        $rencanaSemester1 = $queryBuilder->groupBy('afdeling')
            ->pluck('total', 'afdeling')
            ->toArray();
        
        // Query untuk semester 2
        $queryBuilder = DB::table('rencana_pemupukan')
            ->select('afdeling', DB::raw('SUM(jumlah_pupuk) as total'))
            ->where(function($query) use ($kodeKebun, $namaKebun) {
                $query->where('kebun', $kodeKebun)
                    ->orWhere('kebun', $namaKebun);
            })
            ->where('semester_pemupukan', 2)
            ->whereRaw($jenisPupukCondition);
            
        // Tambahkan filter regional jika ada
        if ($entitas) {
            $queryBuilder->where('regional', $entitas);
        }
        
        $rencanaSemester2 = $queryBuilder->groupBy('afdeling')
            ->pluck('total', 'afdeling')
            ->toArray();
        
        return [
            'semester1' => $rencanaSemester1,
            'semester2' => $rencanaSemester2
        ];
    }

    /**
     * Mendapatkan data realisasi untuk semua afdeling dengan sekali query
     * 
     * @param string $kodeKebun
     * @param string $namaKebun
     * @param string $entitas
     * @param string $jenisPupukCondition
     * @return array
     */
    private function getAfdelingRealisasiData($kodeKebun, $namaKebun, $entitas, $jenisPupukCondition)
    {
        // Query untuk semester 1
        $queryBuilder = DB::table('pemupukan')
            ->select('afdeling', DB::raw('SUM(jumlah_pupuk) as total'))
            ->where(function($query) use ($kodeKebun, $namaKebun) {
                $query->where('kebun', $kodeKebun)
                    ->orWhere('kebun', $namaKebun);
            })
            ->whereRaw('MONTH(tgl_pemupukan) BETWEEN 1 AND 6')
            ->whereRaw($jenisPupukCondition);
            
        // Tambahkan filter regional jika ada
        if ($entitas) {
            $queryBuilder->where('regional', $entitas);
        }
        
        $realisasiSemester1 = $queryBuilder->groupBy('afdeling')
            ->pluck('total', 'afdeling')
            ->toArray();
        
        // Query untuk semester 2
        $queryBuilder = DB::table('pemupukan')
            ->select('afdeling', DB::raw('SUM(jumlah_pupuk) as total'))
            ->where(function($query) use ($kodeKebun, $namaKebun) {
                $query->where('kebun', $kodeKebun)
                    ->orWhere('kebun', $namaKebun);
            })
            ->whereRaw('MONTH(tgl_pemupukan) BETWEEN 7 AND 12')
            ->whereRaw($jenisPupukCondition);
            
        // Tambahkan filter regional jika ada
        if ($entitas) {
            $queryBuilder->where('regional', $entitas);
        }
        
        $realisasiSemester2 = $queryBuilder->groupBy('afdeling')
            ->pluck('total', 'afdeling')
            ->toArray();
        
        return [
            'semester1' => $realisasiSemester1,
            'semester2' => $realisasiSemester2
        ];
    }

    /*
     * Menu Style Routs
     */
    public function horizontal(Request $request)
    {
        $assets = ['chart', 'animation'];
        return view('menu-style.horizontal', compact('assets'));
    }
    public function dualhorizontal(Request $request)
    {
        $assets = ['chart', 'animation'];
        return view('menu-style.dual-horizontal', compact('assets'));
    }
    public function dualcompact(Request $request)
    {
        $assets = ['chart', 'animation'];
        return view('menu-style.dual-compact', compact('assets'));
    }
    public function boxed(Request $request)
    {
        $assets = ['chart', 'animation'];
        return view('menu-style.boxed', compact('assets'));
    }
    public function boxedfancy(Request $request)
    {
        $assets = ['chart', 'animation'];
        return view('menu-style.boxed-fancy', compact('assets'));
    }

    /*
     * Pages Routs
     */
    public function billing(Request $request)
    {
        return view('special-pages.billing');
    }

    public function calender(Request $request)
    {
        $assets = ['calender'];
        return view('special-pages.calender', compact('assets'));
    }

    public function kanban(Request $request)
    {
        return view('special-pages.kanban');
    }

    public function pricing(Request $request)
    {
        return view('special-pages.pricing');
    }

    public function rtlsupport(Request $request)
    {
        return view('special-pages.rtl-support');
    }

    public function timeline(Request $request)
    {
        return view('special-pages.timeline');
    }

    /*
     * Widget Routs
     */
    public function widgetbasic(Request $request)
    {
        return view('widget.widget-basic');
    }
    public function widgetchart(Request $request)
    {
        $assets = ['chart'];
        return view('widget.widget-chart', compact('assets'));
    }
    public function widgetcard(Request $request)
    {
        return view('widget.widget-card');
    }

    /*
     * Maps Routs
     */
    public function google(Request $request)
    {
        return view('maps.google');
    }
    public function vector(Request $request)
    {
        return view('maps.vector');
    }

    /*
     * Auth Routs
     */
    public function signin(Request $request)
    {
        return view('auth.login');
    }
    public function signup(Request $request)
    {
        return view('auth.register');
    }
    public function confirmmail(Request $request)
    {
        return view('auth.confirm-mail');
    }
    public function lockscreen(Request $request)
    {
        return view('auth.lockscreen');
    }
    public function recoverpw(Request $request)
    {
        return view('auth.recoverpw');
    }
    public function userprivacysetting(Request $request)
    {
        return view('auth.user-privacy-setting');
    }

    /*
     * Error Page Routs
     */

    public function error404(Request $request)
    {
        return view('errors.error404');
    }

    public function error500(Request $request)
    {
        return view('errors.error500');
    }
    public function maintenance(Request $request)
    {
        return view('errors.maintenance');
    }

    /*
     * uisheet Page Routs
     */
    public function uisheet(Request $request)
    {
        return view('uisheet');
    }

    /*
     * Form Page Routs
     */
    public function element(Request $request)
    {
        return view('forms.element');
    }

    public function wizard(Request $request)
    {
        return view('forms.wizard');
    }

    public function validation(Request $request)
    {
        return view('forms.validation');
    }

    /*
     * Table Page Routs
     */
    public function bootstraptable(Request $request)
    {
        return view('table.bootstraptable');
    }

    public function datatable(Request $request)
    {
        return view('table.datatable');
    }

    /*
     * Icons Page Routs
     */

    public function solid(Request $request)
    {
        return view('icons.solid');
    }

    public function outline(Request $request)
    {
        return view('icons.outline');
    }

    public function dualtone(Request $request)
    {
        return view('icons.dualtone');
    }

    public function colored(Request $request)
    {
        return view('icons.colored');
    }

    /*
     * Extra Page Routs
     */
    public function privacypolicy(Request $request)
    {
        return view('privacy-policy');
    }
    public function termsofuse(Request $request)
    {
        return view('terms-of-use');
    }

    /*
     * Landing Page Routs
     */
    public function landingIndex(Request $request)
    {
        return view('landing-pages.pages.index');
    }
    public function landingBlog(Request $request)
    {
        return view('landing-pages.pages.blog');
    }
    public function landingAbout(Request $request)
    {
        return view('landing-pages.pages.about');
    }
    public function landingBlogDetail(Request $request)
    {
        return view('landing-pages.pages.blog-detail');
    }
    public function landingContact(Request $request)
    {
        return view('landing-pages.pages.contact-us');
    }
    public function landingEcommerce(Request $request)
    {
        return view('landing-pages.pages.ecommerce-landing-page');
    }
    public function landingFaq(Request $request)
    {
        return view('landing-pages.pages.faq');
    }
    public function landingFeature(Request $request)
    {
        return view('landing-pages.pages.feature');
    }
    public function landingPricing(Request $request)
    {
        return view('landing-pages.pages.pricing');
    }
    public function landingSaas(Request $request)
    {
        return view('landing-pages.pages.saas-marketing-landing-page');
    }
    public function landingShop(Request $request)
    {
        return view('landing-pages.pages.shop');
    }
    public function landingShopDetail(Request $request)
    {
        return view('landing-pages.pages.shop_detail');
    }
    public function landingSoftware(Request $request)
    {
        return view('landing-pages.pages.software-landing-page');
    }
    public function landingStartup(Request $request)
    {
        return view('landing-pages.pages.startup-landing-page');
    }
}
