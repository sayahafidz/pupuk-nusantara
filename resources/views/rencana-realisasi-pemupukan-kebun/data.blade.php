<x-app-layout :assets="$assets ?? []">
    <div class="row">
        <div class="col-sm-12">
            <h3>{{ $pageTitle }}</h3>
            <div class="mb-3">
                <label for="regional-filter">Regional:</label>
                <select id="regional-filter" class="form-control">
                    <option value="">All</option>
                    @foreach ($regionals as $regional)
                        <option value="{{ $regional }}">{{ $regional }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="kebun-filter">Kebun:</label>
                <select id="kebun-filter" class="form-control">
                    <option value="">All</option>
                    @foreach ($kebuns as $kebun)
                        <option value="{{ $kebun }}">{{ $kebun }}</option>
                    @endforeach
                </select>
            </div>
            <table id="dataTable" class="table table-striped">
                <thead>
                    <tr>
                        <th>Regional</th>
                        <th>Kebun</th>
                        <th>Semester 1 Rencana Jumlah Pupuk</th>
                        <th>Semester 1 Rencana Luas Blok</th>
                        <th>Semester 1 Realisasi Jumlah Pupuk</th>
                        <th>Semester 1 Realisasi Luas Blok</th>
                        <th>Semester 2 Rencana Jumlah Pupuk</th>
                        <th>Semester 2 Rencana Luas Blok</th>
                        <th>Semester 2 Realisasi Jumlah Pupuk</th>
                        <th>Semester 2 Realisasi Luas Blok</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be populated by DataTables -->
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                var table = $('#dataTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: 'http://127.0.0.1:8000/data-realisasi-pemupukan',
                        data: function(d) {
                            d.regional = $('#regional-filter').val() || "All";
                            d.kebun = $('#kebun-filter').val() || "";
                        },
                        dataSrc: function(json) {
                            var data = [];
                            // Process and flatten the nested data structure
                            json.forEach(function(item) {
                                data.push({
                                    regional: item.regional,
                                    kebun: "N/A", // Placeholder, as kebun is not present in data
                                    semester_1_rencana_jumlah_pupuk: item.semester_1.rencana
                                        .jumlah_pupuk,
                                    semester_1_rencana_luas_blok: item.semester_1.rencana
                                        .luas_blok,
                                    semester_1_realisasi_jumlah_pupuk: item.semester_1
                                        .realisasi.jumlah_pupuk,
                                    semester_1_realisasi_luas_blok: item.semester_1
                                        .realisasi.luas_blok,
                                    semester_2_rencana_jumlah_pupuk: item.semester_2.rencana
                                        .jumlah_pupuk,
                                    semester_2_rencana_luas_blok: item.semester_2.rencana
                                        .luas_blok,
                                    semester_2_realisasi_jumlah_pupuk: item.semester_2
                                        .realisasi.jumlah_pupuk,
                                    semester_2_realisasi_luas_blok: item.semester_2
                                        .realisasi.luas_blok
                                });
                            });
                            return data; // Flattened data
                        }
                    },
                    columns: [{
                            data: 'regional'
                        },
                        {
                            data: 'kebun'
                        },
                        {
                            data: 'semester_1_rencana_jumlah_pupuk'
                        },
                        {
                            data: 'semester_1_rencana_luas_blok'
                        },
                        {
                            data: 'semester_1_realisasi_jumlah_pupuk'
                        },
                        {
                            data: 'semester_1_realisasi_luas_blok'
                        },
                        {
                            data: 'semester_2_rencana_jumlah_pupuk'
                        },
                        {
                            data: 'semester_2_rencana_luas_blok'
                        },
                        {
                            data: 'semester_2_realisasi_jumlah_pupuk'
                        },
                        {
                            data: 'semester_2_realisasi_luas_blok'
                        }
                    ]
                });

                // Reload the table data when filters are changed
                $('#regional-filter, #kebun-filter').change(function() {
                    table.ajax.reload();
                });
            });
        </script>
    @endpush
</x-app-layout>
