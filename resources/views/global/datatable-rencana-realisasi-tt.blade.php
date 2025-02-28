<x-app-layout :assets="$assets ?? []">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h4 class="card-title">{{ $pageTitle ?? 'List' }}</h4>
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
                            <select id="filter-kebun" class="form-control"
                                {{ $auth_user->regional !== 'head_office' ? 'disabled' : '' }}>
                                <option value="">All Kebun</option>
                                <!-- Kebun options will be populated dynamically -->
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select id="filter-afdeling" class="form-control">
                                <option value="">All Afdeling</option>
                                <!-- Afdeling options will be populated dynamically -->
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
                    </div>
                    <div class="table-responsive">
                        <table id="dataTable" class="table text-center table-striped w-100"></table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
        <script>
            $(document).ready(function() {
                // Load Kebun options based on Regional
                function loadKebun(regional) {
                    if (!regional) {
                        $('#filter-kebun').html('<option value="">All Kebun</option>');
                        $('#filter-afdeling').html('<option value="">All Afdeling</option>');
                        return;
                    }
                    fetch(`/api/kebun-code/${encodeURIComponent(regional)}`)
                        .then(response => response.json())
                        .then(data => {
                            let kebunOptions = '<option value="">All Kebun</option>';
                            const defaultKebun = '{{ $default_plant ?? '' }}';
                            for (let code in data) {
                                if (data.hasOwnProperty(code)) {
                                    const isSelected = code === defaultKebun ? ' selected' : '';
                                    kebunOptions += `<option value="${code}"${isSelected}>${data[code]}</option>`;
                                }
                            }
                            $('#filter-kebun').html(kebunOptions);
                            if (defaultKebun) {
                                $('#filter-kebun').val(defaultKebun);
                                loadAfdeling(regional, defaultKebun);
                            }
                        })
                        .catch(error => {
                            console.error('Error loading kebun:', error);
                            $('#filter-kebun').html('<option value="">All Kebun</option>');
                            $('#filter-afdeling').html('<option value="">All Afdeling</option>');
                        });
                }

                // Load Afdeling options based on Regional and Kebun
                function loadAfdeling(regional, kebun) {
                    if (!regional || !kebun) {
                        $('#filter-afdeling').html('<option value="">All Afdeling</option>');
                        return;
                    }
                    fetch(`/api/afdeling-code/${encodeURIComponent(regional)}/${encodeURIComponent(kebun)}`)
                        .then(response => response.json())
                        .then(data => {
                            let afdelingOptions = '<option value="">All Afdeling</option>';
                            data.forEach(afdeling => {
                                afdelingOptions += `<option value="${afdeling}">${afdeling}</option>`;
                            });
                            $('#filter-afdeling').html(afdelingOptions);
                        })
                        .catch(error => {
                            console.error('Error loading afdeling:', error);
                            $('#filter-afdeling').html('<option value="">All Afdeling</option>');
                        });
                }

                // Initialize DataTable
                let table = $('#dataTable').DataTable({
                    processing: true,
                    serverSide: true,
                    deferRender: true,
                    pageLength: 25,
                    ajax: {
                        url: "{{ route('ren-rel-pem-tt.index') }}",
                        data: function(d) {
                            d.regional = $('#filter-regional').val();
                            d.kebun = $('#filter-kebun').val();
                            d.afdeling = $('#filter-afdeling').val();
                            d.tahun_tanam = $('#filter-tahun-tanam').val();
                        },
                        cache: true
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
                            data: 'rencana_semester_1',
                            name: 'rencana_semester_1',
                            title: 'Rencana S1'
                        },
                        {
                            data: 'realisasi_semester_1',
                            name: 'realisasi_semester_1',
                            title: 'Realisasi S1'
                        },
                        {
                            data: 'percentage_semester_1',
                            name: 'percentage_semester_1',
                            title: '% S1'
                        },
                        {
                            data: 'rencana_semester_2',
                            name: 'rencana_semester_2',
                            title: 'Rencana S2'
                        },
                        {
                            data: 'realisasi_semester_2',
                            name: 'realisasi_semester_2',
                            title: 'Realisasi S2'
                        },
                        {
                            data: 'percentage_semester_2',
                            name: 'percentage_semester_2',
                            title: '% S2'
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
                            title: '% Total'
                        }
                    ],
                    dom: 'lBfrtip',
                    buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                    order: [
                        [0, 'asc']
                    ],
                    language: {
                        processing: '<i class="fa fa-spinner fa-spin"></i> Loading...',
                        paginate: {
                            previous: 'Prev',
                            next: 'Next'
                        }
                    }
                });

                // Load initial data
                const initialRegional = '{{ $default_regional ?? '' }}';
                if (initialRegional) {
                    loadKebun(initialRegional);
                }

                // Event listeners for filter changes
                $('#filter-regional').on('change', function() {
                    const regional = this.value;
                    loadKebun(regional);
                    table.ajax.reload();
                });

                $('#filter-kebun').on('change', function() {
                    const regional = $('#filter-regional').val();
                    const kebun = this.value;
                    loadAfdeling(regional, kebun);
                    table.ajax.reload();
                });

                $('#filter-afdeling, #filter-tahun-tanam').on('change', function() {
                    table.ajax.reload();
                });
            });
        </script>
    @endpush
</x-app-layout>
