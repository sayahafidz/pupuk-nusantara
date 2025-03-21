<div class="table-responsive">
    <table id="kebun-details-table" class="table table-bordered">
        <thead>
            <tr>
                <th rowspan="2" class="text-center align-middle">ENTITAS</th>
                <th rowspan="2" class="text-center align-middle">KEBUN</th>
                <th colspan="3" class="text-center">Semester I</th>
                <th colspan="3" class="text-center">Semester II</th>
                <th colspan="3" class="text-center">Tahun 2025</th>
            </tr>
            <tr>
                <th class="text-center">Rencana (Kg)</th>
                <th class="text-center">Realisasi (Kg)</th>
                <th class="text-center">% Real Thdp Renc</th>
                <th class="text-center">Rencana (Kg)</th>
                <th class="text-center">Realisasi (Kg)</th>
                <th class="text-center">% Real Thdp Renc</th>
                <th class="text-center">Rencana (Kg)</th>
                <th class="text-center">Realisasi (Kg)</th>
                <th class="text-center">% Real Thdp Renc</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kebunData as $data)
                <tr class="{{ $data['kebun'] === 'Jumlah' ? 'table-primary font-weight-bold' : '' }}">
                    <td>{{ $data['entitas'] }}</td>
                    <td>{{ $data['kebun'] }}</td>
                    <td class="text-end">{{ number_format($data['semester1_rencana'], 0, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($data['semester1_realisasi'], 0, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($data['semester1_percentage'], 2) }}</td>
                    <td class="text-end">{{ number_format($data['semester2_rencana'], 0, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($data['semester2_realisasi'], 0, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($data['semester2_percentage'], 2) }}</td>
                    <td class="text-end">{{ number_format($data['tahun_rencana'], 0, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($data['tahun_realisasi'], 0, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($data['tahun_percentage'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>