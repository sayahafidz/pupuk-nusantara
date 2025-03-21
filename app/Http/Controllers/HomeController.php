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
     * Tabel rencana dan realisasi pemupukan dengan caching
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function tableRencanaDanPemupukan(Request $request)
    {
        // Cache key dengan timestamp hari
        $cacheKey = 'table_rencana_pemupukan_' . date('Y-m-d');
        
        // Cek apakah data ada di cache
        return Cache::remember($cacheKey, now()->addMinutes(30), function () {
            // Get distinct RPC from master_data (satu query)
            $entitas = DB::table('master_data')->select('rpc')->distinct()->pluck('rpc')->toArray();
            
            // Ambil semua data rencana dan realisasi sekaligus untuk mengurangi query berulang
            $rencanaTunggalData = $this->getAllRencanaPemupukanData($entitas, false);
            $rencanaMajemukData = $this->getAllRencanaPemupukanData($entitas, true);
            
            $realisasiTunggalData = $this->getAllRealisasiPemupukanData($entitas, false);
            $realisasiMajemukData = $this->getAllRealisasiPemupukanData($entitas, true);
            
            $tableData = [];
            
            foreach ($entitas as $rpc) {
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
            
            return $tableData;
        });
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
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getKebunDetails(Request $request)
    {
        $entitas = $request->input('entitas');
        $jenisPupuk = $request->input('jenis_pupuk');
        
        // Cache key berdasarkan entitas dan jenis pupuk
        $cacheKey = 'kebun_details_' . md5($entitas . '_' . $jenisPupuk . '_' . date('Y-m-d'));
        
        // Cek cache dan proses data
        $kebunData = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($entitas, $jenisPupuk) {
            // Get all kebun associated with this RPC with both code and name
            $kebunList = DB::table('master_data')
                ->where('rpc', $entitas)
                ->select('kode_kebun', 'nama_kebun')
                ->distinct()
                ->get();
            
            // Tentukan apakah kita mencari NPK (pupuk majemuk) atau tidak (pupuk tunggal)
            $isPupukMajemuk = ($jenisPupuk === 'Pupuk Majemuk');
            $jenisPupukCondition = $isPupukMajemuk ? "jenis_pupuk LIKE '%NPK%'" : "jenis_pupuk NOT LIKE '%NPK%'";
            
            // Ambil data rencana pemupukan sekali untuk semua kebun (2 query untuk 2 semester)
            $rencanaData = $this->getKebunRencanaData($entitas, $jenisPupukCondition);
            
            // Ambil data realisasi pemupukan sekali untuk semua kebun (2 query untuk 2 semester)
            $realisasiData = $this->getKebunRealisasiData($entitas, $jenisPupukCondition);
            
            $kebunData = [];
            
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
            
            // Hitung totals untuk semua kebun
            $kebunData[] = $this->calculateKebunTotals($kebunData, $entitas);
            
            return $kebunData;
        });
        
        // Return view dengan data
        return view('dashboards.kebun-details-table', [
            'kebunData' => $kebunData,
            'jenisPupuk' => $jenisPupuk,
            'entitas' => $entitas
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
     * Menghitung total untuk semua kebun
     * 
     * @param array $kebunData
     * @param string $entitas
     * @return array
     */
    private function calculateKebunTotals($kebunData, $entitas)
    {
        $totalData = [
            'entitas' => $entitas,
            'kode_kebun' => '',
            'kebun' => 'Jumlah',
            'semester1_rencana' => array_sum(array_column($kebunData, 'semester1_rencana')),
            'semester1_realisasi' => array_sum(array_column($kebunData, 'semester1_realisasi')),
            'semester1_percentage' => 0,
            'semester2_rencana' => array_sum(array_column($kebunData, 'semester2_rencana')),
            'semester2_realisasi' => array_sum(array_column($kebunData, 'semester2_realisasi')),
            'semester2_percentage' => 0,
            'tahun_rencana' => array_sum(array_column($kebunData, 'tahun_rencana')),
            'tahun_realisasi' => array_sum(array_column($kebunData, 'tahun_realisasi')),
            'tahun_percentage' => 0,
        ];
        
        // Hitung persentase untuk totals
        $totalData['semester1_percentage'] = ($totalData['semester1_rencana'] > 0) ? 
            ($totalData['semester1_realisasi'] / $totalData['semester1_rencana']) * 100 : 0;
        $totalData['semester2_percentage'] = ($totalData['semester2_rencana'] > 0) ? 
            ($totalData['semester2_realisasi'] / $totalData['semester2_rencana']) * 100 : 0;
        $totalData['tahun_percentage'] = ($totalData['tahun_rencana'] > 0) ? 
            ($totalData['tahun_realisasi'] / $totalData['tahun_rencana']) * 100 : 0;
        
        return $totalData;
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
