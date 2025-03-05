<x-app-layout :assets="$assets ?? []">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h4 class="card-title">{{ $pageTitle ?? 'List' }}</h4>
                    <div class="card-action">
                        {!! $headerAction ?? '' !!}
                    </div>
                </div>
                <div class="card-body px-0">
                    <div class="row mb-3 px-3">
                        <div class="col-md-2 p-3">
                            <select id="filter-regional" class="form-control"
                                {{ $auth_user->regional !== 'head_office' ? 'disabled' : '' }}>
                                <option value="">All Regional</option>
                                @foreach ($regionals as $regional)
                                    <option value="{{ $regional }}"
                                        {{ $default_regional == $regional ? 'selected' : '' }}>
                                        {{ $regional }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 p-3">
                            <select id="filter-kebun" class="form-control"
                                {{ $auth_user->regional !== 'head_office' ? 'disabled' : '' }}>
                                <option value="">All Kebun</option>
                                <!-- Kebun options will be populated dynamically -->
                            </select>
                        </div>
                        <div class="col-md-2 p-3">
                            <select id="filter-afdeling" class="form-control">
                                <option value="">All Afdeling</option>
                                <!-- Afdeling options will be populated dynamically -->
                            </select>
                        </div>
                        <div class="col-md-2 p-3">
                            <select id="filter-tahun-tanam" class="form-control">
                                <option value="">All Tahun Tanam</option>
                                <!-- Tahun Tanam options will be populated dynamically -->
                            </select>
                        </div>
                        <div class="col-md-2 p-3">
                            <select id="filter-jenis-pupuk" class="form-control">
                                <option value="">All Jenis Pupuk</option>
                                @foreach ($jenisPupuks as $jenis)
                                    <option value="{{ $jenis }}">{{ $jenis }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive px-4">
                        <table id="dataTable" class="table text-center table-striped w-100"></table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <!-- DataTables Core and Buttons -->
        {{-- <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script> --}}
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
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
                        $('#filter-tahun-tanam').html('<option value="">All Tahun Tanam</option>');
                        return;
                    }
                    fetch(`/api/kebun-code/${encodeURIComponent(regional)}`)
                        .then(response => response.json())
                        .then(data => {
                            let kebunOptions = '<option value="">All Kebun</option>';
                            const defaultKebun = '{{ $default_kebun ?? '' }}';
                            for (let code in data) {
                                if (data.hasOwnProperty(code)) {
                                    const isSelected = code === defaultKebun ? ' selected' : '';
                                    kebunOptions += `<option value="${code}"${isSelected}>${data[code]}</option>`;
                                }
                            }
                            $('#filter-kebun').html(kebunOptions);
                            if (defaultKebun) $('#filter-kebun').val(defaultKebun);
                            const selectedKebun = $('#filter-kebun').val();
                            if (selectedKebun) loadAfdeling(regional, selectedKebun);
                        })
                        .catch(error => {
                            console.error('Error loading kebun:', error);
                            $('#filter-kebun').html('<option value="">All Kebun</option>');
                        });
                }

                // Load Afdeling options based on Regional and Kebun
                function loadAfdeling(regional, kebun) {
                    if (!regional || !kebun) {
                        $('#filter-afdeling').html('<option value="">All Afdeling</option>');
                        $('#filter-tahun-tanam').html('<option value="">All Tahun Tanam</option>');
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
                            const selectedAfdeling = $('#filter-afdeling').val();
                            if (selectedAfdeling) loadTahunTanam(regional, kebun, selectedAfdeling);
                        })
                        .catch(error => {
                            console.error('Error loading afdeling:', error);
                            $('#filter-afdeling').html('<option value="">All Afdeling</option>');
                        });
                }

                // Load Tahun Tanam options based on Regional, Kebun, and Afdeling
                function loadTahunTanam(regional, kebun, afdeling) {
                    if (!regional || !kebun || !afdeling) {
                        $('#filter-tahun-tanam').html('<option value="">All Tahun Tanam</option>');
                        return;
                    }
                    fetch(
                            `/api/tahun-tanam-code/${encodeURIComponent(regional)}/${encodeURIComponent(kebun)}/${encodeURIComponent(afdeling)}`
                        )
                        .then(response => response.json())
                        .then(data => {
                            let tahunTanamOptions = '<option value="">All Tahun Tanam</option>';
                            data.forEach(tahun => {
                                tahunTanamOptions += `<option value="${tahun}">${tahun}</option>`;
                            });
                            $('#filter-tahun-tanam').html(tahunTanamOptions);
                        })
                        .catch(error => {
                            console.error('Error loading tahun tanam:', error);
                            $('#filter-tahun-tanam').html('<option value="">All Tahun Tanam</option>');
                        });
                }

                // Initialize DataTable
                let table = $('#dataTable').DataTable({
                    processing: true,
                    serverSide: true,
                    deferRender: true,
                    pageLength: 10,
                    ajax: {
                        url: "{{ route('ren-rel-pem-jp.index') }}",
                        data: function(d) {
                            d.regional = $('#filter-regional').val();
                            d.kebun = $('#filter-kebun').val();
                            d.afdeling = $('#filter-afdeling').val();
                            d.tahun_tanam = $('#filter-tahun-tanam').val();
                            d.jenis_pupuk = $('#filter-jenis-pupuk').val();
                        },
                        cache: true,
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
                            data: 'rencana_plant',
                            name: 'rencana_plant',
                            title: 'Plant'
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
                    dom: 'lBfrtip',
                    buttons: ['copy', 'csv', 'excel', 'pdf', {
                        text: 'Print',
                        action: function(e, dt, node, config) {
                            let regional = $('#filter-regional').val();
                            let kebun = $('#filter-kebun').val();
                            let afdeling = $('#filter-afdeling').val();
                            let tahunTanam = $('#filter-tahun-tanam').val();
                            let jenisPupuk = $('#filter-jenis-pupuk').val();
                            let url = "{{ route('ren-rel-pem-jp.print') }}";
                            let params = [];
                            if (regional) params.push('regional=' + encodeURIComponent(regional));
                            if (kebun) params.push('kebun=' + encodeURIComponent(kebun));
                            if (afdeling) params.push('afdeling=' + encodeURIComponent(afdeling));
                            if (tahunTanam) params.push('tahun_tanam=' + encodeURIComponent(
                                tahunTanam));
                            if (jenisPupuk) params.push('jenis_pupuk=' + encodeURIComponent(
                                jenisPupuk));
                            if (params.length) url += '?' + params.join('&');
                            window.location.href = url;
                        }
                    }],
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

                // Initial setup
                const initialRegional = $('#filter-regional').val() || '{{ $default_regional ?? '' }}';
                const initialKebun = '{{ $default_kebun ?? '' }}';
                if (initialRegional) {
                    loadKebun(initialRegional);
                    if (initialKebun) {
                        $('#filter-kebun').val(initialKebun);
                        loadAfdeling(initialRegional, initialKebun);
                        table.ajax.reload();
                    }
                }

                // Event listeners for dynamic dropdowns
                $('#filter-regional').on('change', function() {
                    const regional = this.value;
                    loadKebun(regional);
                    $('#filter-afdeling').html('<option value="">All Afdeling</option>');
                    $('#filter-tahun-tanam').html('<option value="">All Tahun Tanam</option>');
                    table.ajax.reload();
                });

                $('#filter-kebun').on('change', function() {
                    const regional = $('#filter-regional').val();
                    const kebun = this.value;
                    loadAfdeling(regional, kebun);
                    $('#filter-tahun-tanam').html('<option value="">All Tahun Tanam</option>');
                    table.ajax.reload();
                });

                $('#filter-afdeling').on('change', function() {
                    const regional = $('#filter-regional').val();
                    const kebun = $('#filter-kebun').val();
                    const afdeling = this.value;
                    loadTahunTanam(regional, kebun, afdeling);
                    table.ajax.reload();
                });

                // Debounce filter changes
                let debounceTimer;
                $('#filter-tahun-tanam, #filter-jenis-pupuk').on('change', function() {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => table.ajax.reload(), 300);
                });
            });
        </script>
    @endpush
</x-app-layout>
