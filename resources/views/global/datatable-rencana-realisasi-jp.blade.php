<x-app-layout :assets="$assets ?? []">

    <div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">{{ $pageTitle ?? 'List' }}</h4>
                        </div>
                    </div>
                    <div class="card-body px-0">
                        <div class="row mb-3 px-3">
                            <div class="col-md-2">
                                <select id="filter-regional" class="form-control"
                                    {{ $auth_user->regional !== 'head_office' ? 'disabled' : '' }}>
                                    <option value="">All Regional</option>
                                    @foreach ($regionals as $regional)
                                        <option value="{{ $regional }}"
                                            {{ $default_regional == $regional ? 'selected' : '' }}>
                                            {{ $regional }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select id="filter-kebun" class="form-control" <option value="">All Kebun</option>
                                    @foreach ($kebuns as $kebun)
                                        <option value="{{ $kebun }}"
                                            {{ $default_kebun == $kebun ? 'selected' : '' }}>
                                            {{ $kebun }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select id="filter-afdeling" class="form-control">
                                    <option value="">All Afdeling</option>
                                    @foreach ($afdelings as $afdeling)
                                        <option value="{{ $afdeling }}">{{ $afdeling }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select id="filter-tahun-tanam" class="form-control">
                                    <option value="">All Tahun Tanam</option>
                                    @foreach ($tahun_tanams as $tahun_tanam)
                                        <option value="{{ $tahun_tanam }}">{{ $tahun_tanam }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select id="filter-jenis-pupuk" class="form-control">
                                    <option value="">All Jenis Pupuk</option>
                                    @foreach ($jenis_pupuks as $jenis_pupuk)
                                        <option value="{{ $jenis_pupuk }}">{{ $jenis_pupuk }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="dataTable" class="table text-center table-striped w-100"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.print.min.js"></script>
        <script>
            $(document).ready(function() {
                // Initialize DataTable
                let table = $('#dataTable').DataTable({
                    processing: true,
                    serverSide: true,
                    autoWidth: false,
                    ajax: {
                        url: "{{ route('ren-rel-pem-jp.index') }}", // Adjust route name if different
                        data: function(d) {
                            d.regional = $('#filter-regional').val();
                            d.kebun = $('#filter-kebun').val();
                            d.afdeling = $('#filter-afdeling').val();
                            d.tahun_tanam = $('#filter-tahun-tanam').val();
                            d.jenis_pupuk = $('#filter-jenis-pupuk').val();
                        },
                        error: function(xhr, error, thrown) {
                            console.log('AJAX error:', xhr.responseText, error, thrown);
                        }
                    },
                    columns: [{
                            data: 'regional',
                            name: 'regional',
                            title: 'Regional'
                        },
                        {
                            data: 'kebun',
                            name: 'kebun',
                            title: 'Kebun'
                        },
                        {
                            data: 'afdeling',
                            name: 'afdeling',
                            title: 'Afdeling'
                        },
                        {
                            data: 'tahun_tanam',
                            name: 'tahun_tanam',
                            title: 'Tahun Tanam'
                        },
                        {
                            data: 'jenis_pupuk',
                            name: 'jenis_pupuk',
                            title: 'Jenis Pupuk'
                        },
                        {
                            data: 'rencana_semester_1',
                            name: 'rencana_semester_1',
                            title: 'Rencana Semester 1'
                        },
                        {
                            data: 'realisasi_semester_1',
                            name: 'realisasi_semester_1',
                            title: 'Realisasi Semester 1'
                        },
                        {
                            data: 'percentage_semester_1',
                            name: 'percentage_semester_1',
                            title: 'Percentage Semester 1'
                        },
                        {
                            data: 'rencana_semester_2',
                            name: 'rencana_semester_2',
                            title: 'Rencana Semester 2'
                        },
                        {
                            data: 'realisasi_semester_2',
                            name: 'realisasi_semester_2',
                            title: 'Realisasi Semester 2'
                        },
                        {
                            data: 'percentage_semester_2',
                            name: 'percentage_semester_2',
                            title: 'Percentage Semester 2'
                        },
                        {
                            data: 'rencana_total',
                            name: 'rencana_total',
                            title: 'Rencana Total'
                        },
                        {
                            data: 'realisasi_total',
                            name: 'realisasi_total',
                            title: 'Realisasi Total'
                        },
                        {
                            data: 'percentage_total',
                            name: 'percentage_total',
                            title: 'Percentage Total'
                        }
                    ],
                    dom: '<"row align-items-center"<"col-md-2" l><"col-md-6" B><"col-md-4"f>><"table-responsive my-3" rt><"row align-items-center" <"col-md-6" i><"col-md-6" p>>',
                    buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                    language: {
                        paginate: {
                            previous: 'Previous',
                            next: 'Next'
                        }
                    }
                });

                // Handle filter changes
                $('#filter-regional, #filter-kebun, #filter-afdeling, #filter-tahun-tanam, #filter-jenis-pupuk').on(
                    'change',
                    function() {
                        console.log('Regional:', $('#filter-regional').val(),
                            'Kebun:', $('#filter-kebun').val(),
                            'Afdeling:', $('#filter-afdeling').val(),
                            'Tahun Tanam:', $('#filter-tahun-tanam').val(),
                            'Jenis Pupuk:', $('#filter-jenis-pupuk').val());
                        table.ajax.reload();
                    });

                console.log('DataTable initialized');
            });
        </script>
    @endpush
</x-app-layout>
