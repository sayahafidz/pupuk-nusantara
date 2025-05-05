<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PemupukanTbmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regions = [
            'DATIM', 'DJABA', 'DSMTU', 'REG6', 'REG7', 'RPC1', 'RPC2',
            'RPC2N14', 'RPC2N2', 'RPC3', 'RPC4', 'RPC5'
        ];

        foreach ($regions as $region) {
            $masterDataRows = DB::table('master_data_tbm')
                ->where('regional', $region)
                ->inRandomOrder()
                ->limit(500)
                ->get();

            if ($masterDataRows->count() > 0) {
                $pupukRows = DB::table('jenis_pupuk')->get();

                foreach ($masterDataRows as $masterData) {
                    $pupuk = $pupukRows->random();

                    $jumlahPupuk = rand(50, 2000);
                    $luasPemupukan = round(rand(5, 100) / 10, 1);

                    $currentYear = date('Y');
                    $randomMonth = rand(1, 12);
                    $maxDay = Carbon::createFromDate($currentYear, $randomMonth, 1)->daysInMonth;
                    $randomDay = rand(1, $maxDay);
                    $tglPemupukan = Carbon::createFromDate($currentYear, $randomMonth, $randomDay)->format('Y-m-d');

                    DB::table('pemupukan_tbm')->insert([
                        'id_pupuk' => $pupuk->id,
                        'id_master_data_tbm' => $masterData->id,
                        'regional' => $masterData->regional,
                        'kebun' => $masterData->kode_kebun,
                        'afdeling' => $masterData->afdeling,
                        'blok' => $masterData->blok,
                        'tahun_tanam' => $masterData->tahun_tanam,
                        'bulan_tanam' => $masterData->bulan_tanam,
                        'luas_blok' => $masterData->luas_ha,
                        'jumlah_pokok' => $masterData->jumlah_pohon,
                        'jenis_pupuk' => $pupuk->jenis_pupuk,
                        'jumlah_pupuk' => $jumlahPupuk,
                        'luas_pemupukan' => $luasPemupukan,
                        'bahan_tanam' => $masterData->bahan_tanam,
                        'tgl_pemupukan' => $tglPemupukan,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
