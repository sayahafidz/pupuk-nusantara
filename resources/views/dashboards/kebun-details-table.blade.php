<div class="table-responsive">
    <table id="kebun-details-table" class="table table-bordered">
        <thead>
            <tr>
                <th class="text-center">{{ isset($isGroup) && $isGroup ? 'ENTITAS' : 'KEBUN' }}</th>
                <th class="text-center">Semester I Rencana (Kg)</th>
                <th class="text-center">Semester I Realisasi (Kg)</th>
                <th class="text-center">% Real Thdp Renc</th>
                <th class="text-center">Semester II Rencana (Kg)</th>
                <th class="text-center">Semester II Realisasi (Kg)</th>
                <th class="text-center">% Real Thdp Renc</th>
                <th class="text-center">Tahun Rencana (Kg)</th>
                <th class="text-center">Tahun Realisasi (Kg)</th>
                <th class="text-center">% Real Thdp Renc</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kebunData as $data)
                <tr class="{{ $data['kebun'] === 'Jumlah' ? 'table-primary' : '' }}">
                    <td>{{ $data['kebun'] }}</td>
                    <td class="text-end">{{ number_format($data['semester1_rencana'], 0, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($data['semester1_realisasi'], 0, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($data['semester1_percentage'], 2) }}%</td>
                    <td class="text-end">{{ number_format($data['semester2_rencana'], 0, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($data['semester2_realisasi'], 0, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($data['semester2_percentage'], 2) }}%</td>
                    <td class="text-end">{{ number_format($data['tahun_rencana'], 0, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($data['tahun_realisasi'], 0, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($data['tahun_percentage'], 2) }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('#kebun-details-table').DataTable({
            responsive: true,
            pageLength: 10,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
            }
        });
    });
</script>