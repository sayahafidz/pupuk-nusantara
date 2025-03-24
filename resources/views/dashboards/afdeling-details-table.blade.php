<div class="table-responsive">
    <table id="afdeling-details-table" class="table table-bordered">
        <thead>
            <tr>
                <th class="text-center">AFDELING</th>
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
            @forelse($afdelingData as $data)
                <tr class="{{ $data['afdeling'] === 'Tidak ada data afdeling' ? 'table-warning' : '' }}">
                    <td>{{ $data['afdeling'] }}</td>
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
            @empty
                <tr>
                    <td colspan="10" class="text-center">Tidak ada data afdeling untuk kebun ini</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('#afdeling-details-table').DataTable({
            responsive: true,
            pageLength: 10,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
            }
        });
    });
</script>