<?php

namespace App\Http\Controllers;

use App\Models\PemupukanTbm;
use App\Models\RencanaPemupukanTbm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardTbmController extends Controller
{
    public function indexTBM(Request $request)
    {
        $tahun = $request->input('tahun', date('Y'));
        $bulanTanam = $request->input('bulan_tanam');
        $tahunTanam = $request->input('tahun_tanam');
        $bahanTanam = $request->input('bahan_tanam');
        $jenisPupuk = $request->input('jenis_pupuk');

        $tahunList = DB::table(function ($query) {
            $query->select('tahun_pemupukan as tahun')
                ->from('rencana_pemupukan_tbm')
                ->whereNotNull('tahun_pemupukan')
                ->distinct()
                ->union(function ($query) {
                    $query->selectRaw('YEAR(tgl_pemupukan) as tahun')
                        ->from('pemupukan_tbm')
                        ->whereNotNull('tgl_pemupukan')
                        ->distinct();
                });
        }, 'combined')->orderBy('tahun', 'desc')->pluck('tahun');

        $bahanTanamList = DB::table(function ($query) {
            $query->select('bahan_tanam')
                ->from('rencana_pemupukan_tbm')
                ->whereNotNull('bahan_tanam')
                ->distinct()
                ->union(function ($query) {
                    $query->select('bahan_tanam')
                        ->from('pemupukan_tbm')
                        ->whereNotNull('bahan_tanam')
                        ->distinct();
                });
        }, 'combined')->pluck('bahan_tanam');

        $jenisPupukList = DB::table(function ($query) {
            $query->select('jenis_pupuk')
                ->from('rencana_pemupukan_tbm')
                ->whereNotNull('jenis_pupuk')
                ->distinct()
                ->union(function ($query) {
                    $query->select('jenis_pupuk')
                        ->from('pemupukan_tbm')
                        ->whereNotNull('jenis_pupuk')
                        ->distinct();
                });
        }, 'combined')->pluck('jenis_pupuk');

        $tahunTanamList = DB::table(function ($query) {
            $query->select('tahun_tanam')
                ->from('rencana_pemupukan_tbm')
                ->whereNotNull('tahun_tanam')
                ->distinct()
                ->union(function ($query) {
                    $query->select('tahun_tanam')
                        ->from('pemupukan_tbm')
                        ->whereNotNull('tahun_tanam')
                        ->distinct();
                });
        }, 'combined')->orderBy('tahun_tanam', 'desc')->pluck('tahun_tanam');

        $groups = [
            'Palm Co Regional 1 + KSO' => ['RPC1', 'DSMTU', 'DATIM', 'DJABA'],
            'Palm Co Regional 2 + KSO' => ['RPC2', 'RPC2N2', 'RPC2N14'],
            'Total Palm Co' => ['RPC1', 'RPC2', 'RPC3', 'RPC4', 'RPC5'],
            'Total Regional KSO' => ['DSMTU', 'DATIM', 'DJABA', 'RPC2N2', 'RPC2N14', 'REG6', 'REG7'],
            'HOLDING' => []
        ];

        $urutanRegional = [
            'RPC1',
            'DSMTU',
            'DATIM',
            'DJABA',
            'RPC2',
            'RPC2N2',
            'RPC2N14',
            'RPC3',
            'RPC4',
            'RPC5',
            'REG6',
            'REG7'
        ];

        $insertPositions = [
            'Palm Co Regional 1 + KSO' => 'DJABA',
            'Palm Co Regional 2 + KSO' => 'RPC2N14'
        ];

        $groupData = [];
        foreach (array_keys($groups) as $groupName) {
            $groupData[$groupName] = $this->createEmptyDataTemplate();
        }

        $hasil = [];

        foreach ($urutanRegional as $regional) {
            $exists = RencanaPemupukanTbm::where('regional', $regional)->exists() ||
                PemupukanTbm::where('regional', $regional)->exists();

            if (!$exists) {
                continue;
            }

            $data = $this->fetchRegionalData($regional, $tahun, $bulanTanam, $tahunTanam, $bahanTanam, $jenisPupuk);

            $hasil[] = [
                'regional' => $regional,
                'kebun' => $data['regional']
            ];

            foreach ($groups as $groupName => $regionals) {
                if ($groupName === 'HOLDING' || in_array($regional, $regionals)) {
                    $this->accumulateData($groupData[$groupName], $data['values']);
                }
            }
        }

        foreach ($groupData as &$data) {
            $this->calculatePercentages($data);
        }

        $hasilFinal = [];

        foreach ($hasil as $item) {
            $hasilFinal[] = $item;

            foreach ($insertPositions as $groupName => $afterRegional) {
                if ($item['regional'] === $afterRegional) {
                    $hasilFinal[] = [
                        'regional' => $groupName,
                        'kebun' => $groupData[$groupName]
                    ];
                }
            }
        }

        foreach (['Total Palm Co', 'Total Regional KSO', 'HOLDING'] as $groupName) {
            $hasilFinal[] = [
                'regional' => $groupName,
                'kebun' => $groupData[$groupName]
            ];
        }

        $data = [
            'hasil' => $hasilFinal,
            'tahun' => (int)$tahun,
            'bulan_tanam' => $bulanTanam,
            'tahun_tanam' => $tahunTanam,
            'bahan_tanam' => $bahanTanam,
            'jenis_pupuk' => $jenisPupuk,
            'bahan_tanam_list' => $bahanTanamList,
            'jenis_pupuk_list' => $jenisPupukList,
            'tahun_tanam_list' => $tahunTanamList,
            'tahun_list' => $tahunList
        ];

        return view('dashboards.dashboard-tbm', compact('data'));
    }

    private function fetchRegionalData($regional, $tahun, $bulanTanam = null, $tahunTanam = null, $bahanTanam = null, $jenisPupuk = null)
    {
        $rencanaTunggalQuery = RencanaPemupukanTbm::where('regional', $regional)
            ->where('tahun_pemupukan', $tahun);

        if ($jenisPupuk) {
            $rencanaTunggalQuery->where('jenis_pupuk', $jenisPupuk);

            $isMajemuk = (stripos($jenisPupuk, 'NPK') !== false);
            if ($isMajemuk) {
                $rencanaTunggalQuery->where('jenis_pupuk', 'NO_DATA');
            }
        } else {
            $rencanaTunggalQuery->where(function ($query) {
                $query->whereRaw("jenis_pupuk NOT LIKE '%NPK%'")->orWhereNull('jenis_pupuk');
            });
        }

        if ($bulanTanam) {
            $rencanaTunggalQuery->where('bulan_tanam', $bulanTanam);
        }

        if ($tahunTanam) {
            $rencanaTunggalQuery->where('tahun_tanam', $tahunTanam);
        }

        if ($bahanTanam) {
            $rencanaTunggalQuery->where('bahan_tanam', $bahanTanam);
        }

        $rencanaTunggal = $rencanaTunggalQuery->selectRaw('
            SUM(CASE WHEN semester_pemupukan = 1 THEN jumlah_pupuk ELSE 0 END) as rencana_semester_1,
            SUM(CASE WHEN semester_pemupukan = 2 THEN jumlah_pupuk ELSE 0 END) as rencana_semester_2,
            SUM(jumlah_pupuk) as rencana_tahun
        ')->first();

        $realisasiTunggalQuery = PemupukanTbm::where('regional', $regional)
            ->whereYear('tgl_pemupukan', $tahun);

        if ($jenisPupuk) {
            $realisasiTunggalQuery->where('jenis_pupuk', $jenisPupuk);

            $isMajemuk = (stripos($jenisPupuk, 'NPK') !== false);
            if ($isMajemuk) {
                $realisasiTunggalQuery->where('jenis_pupuk', 'NO_DATA');
            }
        } else {
            $realisasiTunggalQuery->where(function ($query) {
                $query->whereRaw("jenis_pupuk NOT LIKE '%NPK%'")->orWhereNull('jenis_pupuk');
            });
        }

        if ($bulanTanam) {
            $realisasiTunggalQuery->where('bulan_tanam', $bulanTanam);
        }

        if ($tahunTanam) {
            $realisasiTunggalQuery->where('tahun_tanam', $tahunTanam);
        }

        if ($bahanTanam) {
            $realisasiTunggalQuery->where('bahan_tanam', $bahanTanam);
        }

        $realisasiTunggal = $realisasiTunggalQuery->selectRaw('
            SUM(CASE WHEN MONTH(tgl_pemupukan) BETWEEN 1 AND 6 THEN jumlah_pupuk ELSE 0 END) as realisasi_semester_1,
            SUM(CASE WHEN MONTH(tgl_pemupukan) BETWEEN 7 AND 12 THEN jumlah_pupuk ELSE 0 END) as realisasi_semester_2,
            SUM(jumlah_pupuk) as realisasi_tahun
        ')->first();

        $rencanaMajemukQuery = RencanaPemupukanTbm::where('regional', $regional)
            ->where('tahun_pemupukan', $tahun);

        if ($jenisPupuk) {
            $rencanaMajemukQuery->where('jenis_pupuk', $jenisPupuk);

            $isMajemuk = (stripos($jenisPupuk, 'NPK') !== false);
            if (!$isMajemuk) {
                $rencanaMajemukQuery->where('jenis_pupuk', 'NO_DATA');
            }
        } else {
            $rencanaMajemukQuery->whereRaw("jenis_pupuk LIKE '%NPK%'");
        }

        if ($bulanTanam) {
            $rencanaMajemukQuery->where('bulan_tanam', $bulanTanam);
        }

        if ($tahunTanam) {
            $rencanaMajemukQuery->where('tahun_tanam', $tahunTanam);
        }

        if ($bahanTanam) {
            $rencanaMajemukQuery->where('bahan_tanam', $bahanTanam);
        }

        $rencanaMajemuk = $rencanaMajemukQuery->selectRaw('
            SUM(CASE WHEN semester_pemupukan = 1 THEN jumlah_pupuk ELSE 0 END) as rencana_semester_1,
            SUM(CASE WHEN semester_pemupukan = 2 THEN jumlah_pupuk ELSE 0 END) as rencana_semester_2,
            SUM(jumlah_pupuk) as rencana_tahun
        ')->first();

        $realisasiMajemukQuery = PemupukanTbm::where('regional', $regional)
            ->whereYear('tgl_pemupukan', $tahun);

        if ($jenisPupuk) {
            $realisasiMajemukQuery->where('jenis_pupuk', $jenisPupuk);

            $isMajemuk = (stripos($jenisPupuk, 'NPK') !== false);
            if (!$isMajemuk) {
                $realisasiMajemukQuery->where('jenis_pupuk', 'NO_DATA');
            }
        } else {
            $realisasiMajemukQuery->whereRaw("jenis_pupuk LIKE '%NPK%'");
        }

        if ($bulanTanam) {
            $realisasiMajemukQuery->where('bulan_tanam', $bulanTanam);
        }

        if ($tahunTanam) {
            $realisasiMajemukQuery->where('tahun_tanam', $tahunTanam);
        }

        if ($bahanTanam) {
            $realisasiMajemukQuery->where('bahan_tanam', $bahanTanam);
        }

        $realisasiMajemuk = $realisasiMajemukQuery->selectRaw('
            SUM(CASE WHEN MONTH(tgl_pemupukan) BETWEEN 1 AND 6 THEN jumlah_pupuk ELSE 0 END) as realisasi_semester_1,
            SUM(CASE WHEN MONTH(tgl_pemupukan) BETWEEN 7 AND 12 THEN jumlah_pupuk ELSE 0 END) as realisasi_semester_2,
            SUM(jumlah_pupuk) as realisasi_tahun
        ')->first();

        $values = [
            'rencana_semester_1_tunggal' => (int)($rencanaTunggal->rencana_semester_1 ?? 0),
            'realisasi_semester_1_tunggal' => (int)($realisasiTunggal->realisasi_semester_1 ?? 0),
            'rencana_semester_2_tunggal' => (int)($rencanaTunggal->rencana_semester_2 ?? 0),
            'realisasi_semester_2_tunggal' => (int)($realisasiTunggal->realisasi_semester_2 ?? 0),
            'rencana_tahun_tunggal' => (int)($rencanaTunggal->rencana_tahun ?? 0),
            'realisasi_tahun_tunggal' => (int)($realisasiTunggal->realisasi_tahun ?? 0),

            'rencana_semester_1_majemuk' => (int)($rencanaMajemuk->rencana_semester_1 ?? 0),
            'realisasi_semester_1_majemuk' => (int)($realisasiMajemuk->realisasi_semester_1 ?? 0),
            'rencana_semester_2_majemuk' => (int)($rencanaMajemuk->rencana_semester_2 ?? 0),
            'realisasi_semester_2_majemuk' => (int)($realisasiMajemuk->realisasi_semester_2 ?? 0),
            'rencana_tahun_majemuk' => (int)($rencanaMajemuk->rencana_tahun ?? 0),
            'realisasi_tahun_majemuk' => (int)($realisasiMajemuk->realisasi_tahun ?? 0)
        ];

        $values['rencana_semester_1_total'] = $values['rencana_semester_1_tunggal'] + $values['rencana_semester_1_majemuk'];
        $values['realisasi_semester_1_total'] = $values['realisasi_semester_1_tunggal'] + $values['realisasi_semester_1_majemuk'];
        $values['rencana_semester_2_total'] = $values['rencana_semester_2_tunggal'] + $values['rencana_semester_2_majemuk'];
        $values['realisasi_semester_2_total'] = $values['realisasi_semester_2_tunggal'] + $values['realisasi_semester_2_majemuk'];
        $values['rencana_tahun_total'] = $values['rencana_tahun_tunggal'] + $values['rencana_tahun_majemuk'];
        $values['realisasi_tahun_total'] = $values['realisasi_tahun_tunggal'] + $values['realisasi_tahun_majemuk'];

        $data = $this->createEmptyDataTemplate();

        $data['pupuk_tunggal']['semester_1']['rencana'] = $values['rencana_semester_1_tunggal'];
        $data['pupuk_tunggal']['semester_1']['realisasi'] = $values['realisasi_semester_1_tunggal'];
        $data['pupuk_tunggal']['semester_2']['rencana'] = $values['rencana_semester_2_tunggal'];
        $data['pupuk_tunggal']['semester_2']['realisasi'] = $values['realisasi_semester_2_tunggal'];
        $data['pupuk_tunggal']['tahun']['rencana'] = $values['rencana_tahun_tunggal'];
        $data['pupuk_tunggal']['tahun']['realisasi'] = $values['realisasi_tahun_tunggal'];

        $data['pupuk_majemuk']['semester_1']['rencana'] = $values['rencana_semester_1_majemuk'];
        $data['pupuk_majemuk']['semester_1']['realisasi'] = $values['realisasi_semester_1_majemuk'];
        $data['pupuk_majemuk']['semester_2']['rencana'] = $values['rencana_semester_2_majemuk'];
        $data['pupuk_majemuk']['semester_2']['realisasi'] = $values['realisasi_semester_2_majemuk'];
        $data['pupuk_majemuk']['tahun']['rencana'] = $values['rencana_tahun_majemuk'];
        $data['pupuk_majemuk']['tahun']['realisasi'] = $values['realisasi_tahun_majemuk'];

        $data['jumlah']['semester_1']['rencana'] = $values['rencana_semester_1_total'];
        $data['jumlah']['semester_1']['realisasi'] = $values['realisasi_semester_1_total'];
        $data['jumlah']['semester_2']['rencana'] = $values['rencana_semester_2_total'];
        $data['jumlah']['semester_2']['realisasi'] = $values['realisasi_semester_2_total'];
        $data['jumlah']['tahun']['rencana'] = $values['rencana_tahun_total'];
        $data['jumlah']['tahun']['realisasi'] = $values['realisasi_tahun_total'];

        $this->calculatePercentages($data);

        return [
            'values' => $values,
            'regional' => $data
        ];
    }

    private function createEmptyDataTemplate()
    {
        return [
            'pupuk_tunggal' => [
                'semester_1' => ['rencana' => 0, 'realisasi' => 0, 'persentase' => 0],
                'semester_2' => ['rencana' => 0, 'realisasi' => 0, 'persentase' => 0],
                'tahun' => ['rencana' => 0, 'realisasi' => 0, 'persentase' => 0]
            ],
            'pupuk_majemuk' => [
                'semester_1' => ['rencana' => 0, 'realisasi' => 0, 'persentase' => 0],
                'semester_2' => ['rencana' => 0, 'realisasi' => 0, 'persentase' => 0],
                'tahun' => ['rencana' => 0, 'realisasi' => 0, 'persentase' => 0]
            ],
            'jumlah' => [
                'semester_1' => ['rencana' => 0, 'realisasi' => 0, 'persentase' => 0],
                'semester_2' => ['rencana' => 0, 'realisasi' => 0, 'persentase' => 0],
                'tahun' => ['rencana' => 0, 'realisasi' => 0, 'persentase' => 0]
            ]
        ];
    }

    private function accumulateData(&$target, $values)
    {
        $target['pupuk_tunggal']['semester_1']['rencana'] += $values['rencana_semester_1_tunggal'];
        $target['pupuk_tunggal']['semester_1']['realisasi'] += $values['realisasi_semester_1_tunggal'];
        $target['pupuk_tunggal']['semester_2']['rencana'] += $values['rencana_semester_2_tunggal'];
        $target['pupuk_tunggal']['semester_2']['realisasi'] += $values['realisasi_semester_2_tunggal'];
        $target['pupuk_tunggal']['tahun']['rencana'] += $values['rencana_tahun_tunggal'];
        $target['pupuk_tunggal']['tahun']['realisasi'] += $values['realisasi_tahun_tunggal'];

        $target['pupuk_majemuk']['semester_1']['rencana'] += $values['rencana_semester_1_majemuk'];
        $target['pupuk_majemuk']['semester_1']['realisasi'] += $values['realisasi_semester_1_majemuk'];
        $target['pupuk_majemuk']['semester_2']['rencana'] += $values['rencana_semester_2_majemuk'];
        $target['pupuk_majemuk']['semester_2']['realisasi'] += $values['realisasi_semester_2_majemuk'];
        $target['pupuk_majemuk']['tahun']['rencana'] += $values['rencana_tahun_majemuk'];
        $target['pupuk_majemuk']['tahun']['realisasi'] += $values['realisasi_tahun_majemuk'];

        $target['jumlah']['semester_1']['rencana'] += $values['rencana_semester_1_total'];
        $target['jumlah']['semester_1']['realisasi'] += $values['realisasi_semester_1_total'];
        $target['jumlah']['semester_2']['rencana'] += $values['rencana_semester_2_total'];
        $target['jumlah']['semester_2']['realisasi'] += $values['realisasi_semester_2_total'];
        $target['jumlah']['tahun']['rencana'] += $values['rencana_tahun_total'];
        $target['jumlah']['tahun']['realisasi'] += $values['realisasi_tahun_total'];
    }

    private function calculatePercentages(&$data)
    {
        foreach (['pupuk_tunggal', 'pupuk_majemuk', 'jumlah'] as $type) {
            foreach (['semester_1', 'semester_2', 'tahun'] as $period) {
                $data[$type][$period]['persentase'] = $data[$type][$period]['rencana'] > 0
                    ? round(($data[$type][$period]['realisasi'] / $data[$type][$period]['rencana']) * 100, 2)
                    : 0;
            }
        }
    }

    public function getDetailTBM(Request $request, $regional, $jenis)
    {
        $tahun = $request->query('tahun', date('Y'));
        $bulanTanam = $request->query('bulan_tanam');
        $tahunTanam = $request->query('tahun_tanam');
        $bahanTanam = $request->query('bahan_tanam');
        $jenisPupuk = $request->query('jenis_pupuk');

        // Check if regional is a special group
        $specialGroups = [
            'Palm Co Regional 1 + KSO' => ['RPC1', 'DSMTU', 'DATIM', 'DJABA'],
            'Palm Co Regional 2 + KSO' => ['RPC2', 'RPC2N2', 'RPC2N14'],
            'Total Palm Co' => ['RPC1', 'RPC2', 'RPC3', 'RPC4', 'RPC5'],
            'Total Regional KSO' => ['DSMTU', 'DATIM', 'DJABA', 'RPC2N2', 'RPC2N14', 'REG6', 'REG7'],
            'HOLDING' => []
        ];

        $regionals = [];
        if (array_key_exists($regional, $specialGroups)) {
            $regionals = $specialGroups[$regional];
            if ($regional === 'HOLDING') {
                $regionals = array_merge(
                    $specialGroups['Total Palm Co'],
                    $specialGroups['Total Regional KSO']
                );
            }
        } else {
            $regionals = [$regional];
        }

        // Get the estates for each regional
        $hasil = [];

        foreach ($regionals as $reg) {
            // Get all estates (kebun) from master_data_tbm where regional = $reg
            $estates = DB::table('master_data_tbm')
                ->where('regional', $reg)
                ->select('kode_kebun', 'nama_kebun') // Get both kebun code and nama_kebun
                ->distinct()
                ->get();

            foreach ($estates as $estate) {
                $estateData = $this->fetchEstateData($reg, $estate->kode_kebun, $jenis, $tahun, $bulanTanam, $tahunTanam, $bahanTanam, $jenisPupuk);

                // Add the nama_kebun from master_data_tbm
                $estateData['nama_kebun'] = $estate->nama_kebun;

                $hasil[] = $estateData;
            }
        }

        return response()->json($hasil);
    }

    private function fetchEstateData($regional, $estate, $jenis, $tahun, $bulanTanam = null, $tahunTanam = null, $bahanTanam = null, $jenisPupuk = null)
    {
        // Build query for rencana based on jenis
        $rencanaQuery = RencanaPemupukanTbm::where('regional', $regional)
            ->where('kebun', $estate)
            ->where('tahun_pemupukan', $tahun);

        if ($jenis === 'tunggal') {
            if ($jenisPupuk) {
                $rencanaQuery->where('jenis_pupuk', $jenisPupuk);
                $isMajemuk = (stripos($jenisPupuk, 'NPK') !== false);
                if ($isMajemuk) {
                    $rencanaQuery->where('jenis_pupuk', 'NO_DATA');
                }
            } else {
                $rencanaQuery->where(function ($query) {
                    $query->whereRaw("jenis_pupuk NOT LIKE '%NPK%'")->orWhereNull('jenis_pupuk');
                });
            }
        } else { // majemuk
            if ($jenisPupuk) {
                $rencanaQuery->where('jenis_pupuk', $jenisPupuk);
                $isMajemuk = (stripos($jenisPupuk, 'NPK') !== false);
                if (!$isMajemuk) {
                    $rencanaQuery->where('jenis_pupuk', 'NO_DATA');
                }
            } else {
                $rencanaQuery->whereRaw("jenis_pupuk LIKE '%NPK%'");
            }
        }

        if ($bulanTanam) {
            $rencanaQuery->where('bulan_tanam', $bulanTanam);
        }

        if ($tahunTanam) {
            $rencanaQuery->where('tahun_tanam', $tahunTanam);
        }

        if ($bahanTanam) {
            $rencanaQuery->where('bahan_tanam', $bahanTanam);
        }

        $rencana = $rencanaQuery->selectRaw('
        SUM(CASE WHEN semester_pemupukan = 1 THEN jumlah_pupuk ELSE 0 END) as rencana_semester_1,
        SUM(CASE WHEN semester_pemupukan = 2 THEN jumlah_pupuk ELSE 0 END) as rencana_semester_2,
        SUM(jumlah_pupuk) as rencana_tahun
    ')->first();

        // Build query for realisasi based on jenis
        $realisasiQuery = PemupukanTbm::where('regional', $regional)
            ->where('kebun', $estate)
            ->whereYear('tgl_pemupukan', $tahun);

        if ($jenis === 'tunggal') {
            if ($jenisPupuk) {
                $realisasiQuery->where('jenis_pupuk', $jenisPupuk);
                $isMajemuk = (stripos($jenisPupuk, 'NPK') !== false);
                if ($isMajemuk) {
                    $realisasiQuery->where('jenis_pupuk', 'NO_DATA');
                }
            } else {
                $realisasiQuery->where(function ($query) {
                    $query->whereRaw("jenis_pupuk NOT LIKE '%NPK%'")->orWhereNull('jenis_pupuk');
                });
            }
        } else { // majemuk
            if ($jenisPupuk) {
                $realisasiQuery->where('jenis_pupuk', $jenisPupuk);
                $isMajemuk = (stripos($jenisPupuk, 'NPK') !== false);
                if (!$isMajemuk) {
                    $realisasiQuery->where('jenis_pupuk', 'NO_DATA');
                }
            } else {
                $realisasiQuery->whereRaw("jenis_pupuk LIKE '%NPK%'");
            }
        }

        if ($bulanTanam) {
            $realisasiQuery->where('bulan_tanam', $bulanTanam);
        }

        if ($tahunTanam) {
            $realisasiQuery->where('tahun_tanam', $tahunTanam);
        }

        if ($bahanTanam) {
            $realisasiQuery->where('bahan_tanam', $bahanTanam);
        }

        $realisasi = $realisasiQuery->selectRaw('
        SUM(CASE WHEN MONTH(tgl_pemupukan) BETWEEN 1 AND 6 THEN jumlah_pupuk ELSE 0 END) as realisasi_semester_1,
        SUM(CASE WHEN MONTH(tgl_pemupukan) BETWEEN 7 AND 12 THEN jumlah_pupuk ELSE 0 END) as realisasi_semester_2,
        SUM(jumlah_pupuk) as realisasi_tahun
    ')->first();

        $semester_1_rencana = (int)($rencana->rencana_semester_1 ?? 0);
        $semester_1_realisasi = (int)($realisasi->realisasi_semester_1 ?? 0);
        $semester_1_persentase = $semester_1_rencana > 0
            ? round(($semester_1_realisasi / $semester_1_rencana) * 100, 2)
            : 0;

        $semester_2_rencana = (int)($rencana->rencana_semester_2 ?? 0);
        $semester_2_realisasi = (int)($realisasi->realisasi_semester_2 ?? 0);
        $semester_2_persentase = $semester_2_rencana > 0
            ? round(($semester_2_realisasi / $semester_2_rencana) * 100, 2)
            : 0;

        $tahun_rencana = (int)($rencana->rencana_tahun ?? 0);
        $tahun_realisasi = (int)($realisasi->realisasi_tahun ?? 0);
        $tahun_persentase = $tahun_rencana > 0
            ? round(($tahun_realisasi / $tahun_rencana) * 100, 2)
            : 0;

        return [
            'kebun' => $estate,
            'semester_1_rencana' => $semester_1_rencana,
            'semester_1_realisasi' => $semester_1_realisasi,
            'semester_1_persentase' => $semester_1_persentase,
            'semester_2_rencana' => $semester_2_rencana,
            'semester_2_realisasi' => $semester_2_realisasi,
            'semester_2_persentase' => $semester_2_persentase,
            'tahun_rencana' => $tahun_rencana,
            'tahun_realisasi' => $tahun_realisasi,
            'tahun_persentase' => $tahun_persentase
        ];
    }

    public function getAfdelingDetailTBM(Request $request, $regional, $kebun, $jenis)
    {
        $tahun = $request->query('tahun', date('Y'));
        $bulanTanam = $request->query('bulan_tanam');
        $tahunTanam = $request->query('tahun_tanam');
        $bahanTanam = $request->query('bahan_tanam');
        $jenisPupuk = $request->query('jenis_pupuk');

        if ($kebun === 'undefined' || $kebun === 'null') {
            return response()->json([
                'error' => 'Invalid kebun code',
                'message' => 'Kode kebun tidak valid atau tidak ditemukan'
            ], 400);
        }

        $afdelings = DB::table('master_data_tbm')
            ->where('regional', $regional)
            ->where('kode_kebun', $kebun)
            ->select('afdeling')
            ->distinct()
            ->get();


        $hasil = [];

        foreach ($afdelings as $afdeling) {
            $afdelingData = $this->fetchAfdelingData($regional, $kebun, $afdeling->afdeling, $jenis, $tahun, $bulanTanam, $tahunTanam, $bahanTanam, $jenisPupuk);
            $hasil[] = $afdelingData;
        }

        return response()->json($hasil);
    }

    private function fetchAfdelingData($regional, $kebun, $afdeling, $jenis, $tahun, $bulanTanam = null, $tahunTanam = null, $bahanTanam = null, $jenisPupuk = null)
    {
        $rencanaQuery = RencanaPemupukanTbm::where('regional', $regional)
            ->where('kebun', $kebun)
            ->where('afdeling', $afdeling)
            ->where('tahun_pemupukan', $tahun);

        if ($jenis === 'tunggal') {
            if ($jenisPupuk) {
                $rencanaQuery->where('jenis_pupuk', $jenisPupuk);
                $isMajemuk = (stripos($jenisPupuk, 'NPK') !== false);
                if ($isMajemuk) {
                    $rencanaQuery->where('jenis_pupuk', 'NO_DATA');
                }
            } else {
                $rencanaQuery->where(function ($query) {
                    $query->whereRaw("jenis_pupuk NOT LIKE '%NPK%'")->orWhereNull('jenis_pupuk');
                });
            }
        } else {
            if ($jenisPupuk) {
                $rencanaQuery->where('jenis_pupuk', $jenisPupuk);
                $isMajemuk = (stripos($jenisPupuk, 'NPK') !== false);
                if (!$isMajemuk) {
                    $rencanaQuery->where('jenis_pupuk', 'NO_DATA');
                }
            } else {
                $rencanaQuery->whereRaw("jenis_pupuk LIKE '%NPK%'");
            }
        }

        if ($bulanTanam) {
            $rencanaQuery->where('bulan_tanam', $bulanTanam);
        }

        if ($tahunTanam) {
            $rencanaQuery->where('tahun_tanam', $tahunTanam);
        }

        if ($bahanTanam) {
            $rencanaQuery->where('bahan_tanam', $bahanTanam);
        }

        $rencana = $rencanaQuery->selectRaw('
            SUM(CASE WHEN semester_pemupukan = 1 THEN jumlah_pupuk ELSE 0 END) as rencana_semester_1,
            SUM(CASE WHEN semester_pemupukan = 2 THEN jumlah_pupuk ELSE 0 END) as rencana_semester_2,
            SUM(jumlah_pupuk) as rencana_tahun
        ')->first();

        $realisasiQuery = PemupukanTbm::where('regional', $regional)
            ->where('kebun', $kebun)
            ->where('afdeling', $afdeling)
            ->whereYear('tgl_pemupukan', $tahun);

        if ($jenis === 'tunggal') {
            if ($jenisPupuk) {
                $realisasiQuery->where('jenis_pupuk', $jenisPupuk);
                $isMajemuk = (stripos($jenisPupuk, 'NPK') !== false);
                if ($isMajemuk) {
                    $realisasiQuery->where('jenis_pupuk', 'NO_DATA');
                }
            } else {
                $realisasiQuery->where(function ($query) {
                    $query->whereRaw("jenis_pupuk NOT LIKE '%NPK%'")->orWhereNull('jenis_pupuk');
                });
            }
        } else {
            if ($jenisPupuk) {
                $realisasiQuery->where('jenis_pupuk', $jenisPupuk);
                $isMajemuk = (stripos($jenisPupuk, 'NPK') !== false);
                if (!$isMajemuk) {
                    $realisasiQuery->where('jenis_pupuk', 'NO_DATA');
                }
            } else {
                $realisasiQuery->whereRaw("jenis_pupuk LIKE '%NPK%'");
            }
        }

        if ($bulanTanam) {
            $realisasiQuery->where('bulan_tanam', $bulanTanam);
        }

        if ($tahunTanam) {
            $realisasiQuery->where('tahun_tanam', $tahunTanam);
        }

        if ($bahanTanam) {
            $realisasiQuery->where('bahan_tanam', $bahanTanam);
        }

        $realisasi = $realisasiQuery->selectRaw('
            SUM(CASE WHEN MONTH(tgl_pemupukan) BETWEEN 1 AND 6 THEN jumlah_pupuk ELSE 0 END) as realisasi_semester_1,
            SUM(CASE WHEN MONTH(tgl_pemupukan) BETWEEN 7 AND 12 THEN jumlah_pupuk ELSE 0 END) as realisasi_semester_2,
            SUM(jumlah_pupuk) as realisasi_tahun
        ')->first();

        $semester_1_rencana = (int)($rencana->rencana_semester_1 ?? 0);
        $semester_1_realisasi = (int)($realisasi->realisasi_semester_1 ?? 0);
        $semester_1_persentase = $semester_1_rencana > 0
            ? round(($semester_1_realisasi / $semester_1_rencana) * 100, 2)
            : 0;

        $semester_2_rencana = (int)($rencana->rencana_semester_2 ?? 0);
        $semester_2_realisasi = (int)($realisasi->realisasi_semester_2 ?? 0);
        $semester_2_persentase = $semester_2_rencana > 0
            ? round(($semester_2_realisasi / $semester_2_rencana) * 100, 2)
            : 0;

        $tahun_rencana = (int)($rencana->rencana_tahun ?? 0);
        $tahun_realisasi = (int)($realisasi->realisasi_tahun ?? 0);
        $tahun_persentase = $tahun_rencana > 0
            ? round(($tahun_realisasi / $tahun_rencana) * 100, 2)
            : 0;

        return [
            'afdeling' => $afdeling,
            'semester_1_rencana' => $semester_1_rencana,
            'semester_1_realisasi' => $semester_1_realisasi,
            'semester_1_persentase' => $semester_1_persentase,
            'semester_2_rencana' => $semester_2_rencana,
            'semester_2_realisasi' => $semester_2_realisasi,
            'semester_2_persentase' => $semester_2_persentase,
            'tahun_rencana' => $tahun_rencana,
            'tahun_realisasi' => $tahun_realisasi,
            'tahun_persentase' => $tahun_persentase
        ];
    }
}
