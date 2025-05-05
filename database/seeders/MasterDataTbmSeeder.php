<?php

namespace Database\Seeders;

use App\Models\MasterDataTbm;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterDataTbmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvPath = database_path('seed_files/master_data_tbm.csv');

        if (!file_exists($csvPath)) {
            echo "CSV file not found at: $csvPath\n";
            return;
        }

        // Read the entire file content
        $content = file_get_contents($csvPath);

        // Split into lines
        $lines = explode("\n", $content);

        // Skip header line
        array_shift($lines);

        $count = 0;

        foreach ($lines as $line) {
            if (empty(trim($line))) {
                continue; // Skip empty lines
            }

            // Remove the outer quotes around the entire line
            $line = trim($line, '"');

            // Split by comma for the first level
            $parts = explode(',', $line);

            // Extract the values, cleaning up extra quotes
            $regional = $parts[0] ?? '';
            $kodeKebun = isset($parts[1]) ? trim($parts[1], '"') : '';
            $namaKebun = $parts[2] ?? '';
            $afdeling = $parts[3] ?? '';
            $blok = $parts[4] ?? '';

            // Clean up any remaining double quotes
            $regional = str_replace('"', '', $regional);
            $kodeKebun = str_replace('"', '', $kodeKebun);
            $namaKebun = str_replace('"', '', $namaKebun);
            $afdeling = str_replace('"', '', $afdeling);
            $blok = str_replace('"', '', $blok);

            // Trim any whitespace
            $regional = trim($regional);
            $kodeKebun = trim($kodeKebun);
            $namaKebun = trim($namaKebun);
            $afdeling = trim($afdeling);
            $blok = trim($blok);

            // Debug the first few records
            if ($count < 3) {
                echo "Processing: regional='$regional', nama_kebun='$namaKebun', afdeling='$afdeling', blok='$blok'\n";
            }

            // Only proceed if we have essential data
            if (!empty($regional) && !empty($namaKebun) && !empty($afdeling) && !empty($blok)) {
                try {
                    // Create model with data
                    MasterDataTbm::create([
                        'regional' => $regional,
                        'kode_kebun' => $kodeKebun,
                        'nama_kebun' => $namaKebun,
                        'afdeling' => $afdeling,
                        'blok' => $blok,
                        'tahun_tanam' => rand(2010, 2023),
                        'bulan_tanam' => rand(1, 12),
                        'luas_ha' => number_format(rand(1, 50) + (rand(0, 100) / 100), 2),
                        'jumlah_pohon' => rand(50, 5000),
                        'bahan_tanam' => ['Bibit Unggul', 'Bibit Lokal', 'Bibit Sertifikasi', 'Klon Prima'][array_rand(['Bibit Unggul', 'Bibit Lokal', 'Bibit Sertifikasi', 'Klon Prima'])]
                    ]);

                    $count++;

                    if ($count % 1000 === 0) {
                        echo "Processed $count records\n";
                    }
                } catch (\Exception $e) {
                    echo "Error on line: $line\n";
                    echo "Error: " . $e->getMessage() . "\n";
                }
            } else {
                echo "Skipping record with incomplete data: $line\n";
            }
        }

        // Verify data was inserted correctly
        $testRecord = MasterDataTbm::where('regional', 'DATIM')
                                 ->where('nama_kebun', 'KEBUN JULOK RAYEUK SELATAN')
                                 ->first();

        if ($testRecord) {
            echo "Verification successful! Sample record found:\n";
            echo "ID: {$testRecord->id}, Regional: {$testRecord->regional}, Kebun: {$testRecord->nama_kebun}, Afdeling: {$testRecord->afdeling}, Blok: {$testRecord->blok}\n";
        } else {
            echo "WARNING: Could not verify data insertion. No sample record found.\n";
        }

        echo "Total $count records imported successfully\n";
    }
}
