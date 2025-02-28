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
                        <div class="col-md-3 p-3">
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
                        <div class="col-md-3 p-3">
                            <select id="filter-kebun" class="form-control"
                                {{ $auth_user->regional !== 'head_office' ? 'disabled' : '' }}>
                                <option value="">All Plant</option>
                                <!-- Plant options will be populated dynamically -->
                            </select>
                        </div>
                        <div class="col-md-3 p-3">
                            <select id="filter-afdeling" class="form-control">
                                <option value="">All Afdeling</option>
                                <!-- Afdeling options will be populated dynamically -->
                            </select>
                        </div>
                        <div class="col-md-3 p-3">
                            <select id="filter-tahun-tanam" class="form-control">
                                <option value="">All Tahun Tanam</option>
                                <!-- Tahun Tanam options will be populated dynamically -->
                            </select>
                        </div>
                        <div class="row mb-3 px-3">
                            <!-- Regional, Plant, Afdeling, Tahun Tanam as before -->
                            <div class="col-md-3 p-3">
                                <select id="filter-jenis-pupuk" class="form-control">
                                    <option value="">All Jenis Pupuk</option>
                                    @foreach ($jenisPupuks as $jenisPupuk)
                                        <option value="{{ $jenisPupuk }}">{{ $jenisPupuk }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 p-3">
                                <select id="filter-semester-pemupukan" class="form-control">
                                    <option value="">All Semester Pemupukan</option>
                                    @foreach ($semesterPemupukans as $semester)
                                        <option value="{{ $semester }}">{{ $semester }}</option>
                                    @endforeach
                                </select>
                            </div>
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
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"
            rel="stylesheet">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

        <script>
            $(document).ready(function() {
                // Initialize Datepickers
                $('#filter-tgl-pemupukan-start, #filter-tgl-pemupukan-end').datepicker({
                    format: 'yyyy-mm-dd',
                    autoclose: true,
                    todayHighlight: true
                });

                // Load Plant options based on Regional
                function loadKebun(regional) {
                    if (!regional) {
                        $('#filter-kebun').html('<option value="">All Plant</option>');
                        $('#filter-afdeling').html('<option value="">All Afdeling</option>');
                        $('#filter-tahun-tanam').html('<option value="">All Tahun Tanam</option>');
                        return;
                    }
                    fetch(`/api/kebun-code/${encodeURIComponent(regional)}`)
                        .then(response => response.json())
                        .then(data => {
                            let plantOptions = '<option value="">All Plant</option>';
                            const defaultKebun = '{{ $default_plant ?? '' }}';
                            for (let code in data) {
                                if (data.hasOwnProperty(code)) {
                                    const isSelected = code === defaultKebun ? ' selected' : '';
                                    plantOptions += `<option value="${code}"${isSelected}>${data[code]}</option>`;
                                }
                            }
                            $('#filter-kebun').html(plantOptions);
                            if (defaultKebun) {
                                $('#filter-kebun').val(defaultKebun);
                                loadAfdeling(regional, defaultKebun);
                            }
                        })
                        .catch(error => {
                            console.error('Error loading plants:', error);
                            $('#filter-kebun').html('<option value="">All Plant</option>');
                        });
                }

                // Load Afdeling options based on Regional and Plant
                function loadAfdeling(regional, plant) {
                    if (!regional || !plant) {
                        $('#filter-afdeling').html('<option value="">All Afdeling</option>');
                        $('#filter-tahun-tanam').html('<option value="">All Tahun Tanam</option>');
                        return;
                    }
                    fetch(`/api/afdeling-code/${encodeURIComponent(regional)}/${encodeURIComponent(plant)}`)
                        .then(response => response.json())
                        .then(data => {
                            let afdelingOptions = '<option value="">All Afdeling</option>';
                            data.forEach(afdeling => {
                                afdelingOptions += `<option value="${afdeling}">${afdeling}</option>`;
                            });
                            $('#filter-afdeling').html(afdelingOptions);
                            const selectedAfdeling = $('#filter-afdeling').val();
                            if (selectedAfdeling) loadTahunTanam(regional, plant, selectedAfdeling);
                        })
                        .catch(error => {
                            console.error('Error loading afdelings:', error);
                            $('#filter-afdeling').html('<option value="">All Afdeling</option>');
                        });
                }

                // Load Tahun Tanam options based on Regional, Plant, and Afdeling
                function loadTahunTanam(regional, plant, afdeling) {
                    if (!regional || !plant || !afdeling) {
                        $('#filter-tahun-tanam').html('<option value="">All Tahun Tanam</option>');
                        return;
                    }
                    fetch(
                            `/api/tahun-tanam-code/${encodeURIComponent(regional)}/${encodeURIComponent(plant)}/${encodeURIComponent(afdeling)}`
                        )
                        .then(response => response.json())
                        .then(data => {
                            let tahunTanamOptions = '<option value="">All Tahun Tanam</option>';
                            data.forEach(tahunTanam => {
                                tahunTanamOptions += `<option value="${tahunTanam}">${tahunTanam}</option>`;
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
                        url: "{{ route('rencana-pemupukan.index') }}",
                        data: function(d) {
                            d.regional = $('#filter-regional').val();
                            d.plant = $('#filter-kebun').val();
                            d.afdeling = $('#filter-afdeling').val();
                            d.tahun_tanam = $('#filter-tahun-tanam').val();
                            d.jenis_pupuk = $('#filter-jenis-pupuk').val();
                            d.semester_pemupukan = $('#filter-semester-pemupukan').val();
                        },
                        cache: true
                    },
                    columns: [{
                            data: 'regional',
                            name: 'regional',
                            title: 'Regional'
                        },
                        {
                            data: 'plant',
                            name: 'plant',
                            title: 'Plant'
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
                            data: 'blok',
                            name: 'blok',
                            title: 'Blok'
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
                            data: 'jumlah_pupuk',
                            name: 'jumlah_pupuk',
                            title: 'Jumlah Pupuk'
                        },
                        {
                            data: 'semester_pemupukan',
                            name: 'semester_pemupukan',
                            title: 'Semester Pemupukan'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            title: 'Action',
                            orderable: false,
                            searchable: false
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
                const initialKebun = '{{ $default_plant ?? '' }}';
                console.log("regional = ", initialRegional);
                console.log("kebun = ", initialKebun);
                if (initialRegional) {
                    loadKebun(initialRegional);
                    if (initialKebun) {
                        $('#filter-kebun').val(initialKebun);
                        loadAfdeling(initialRegional, initialKebun);
                        table.ajax.reload();
                    }
                }

                // Event listeners
                $('#filter-regional').on('change', function() {
                    const regional = this.value;
                    loadKebun(regional);
                    $('#filter-afdeling').html('<option value="">All Afdeling</option>');
                    $('#filter-tahun-tanam').html('<option value="">All Tahun Tanam</option>');
                    table.ajax.reload();
                });

                $('#filter-kebun').on('change', function() {
                    const regional = $('#filter-regional').val();
                    const plant = this.value;
                    loadAfdeling(regional, plant);
                    $('#filter-tahun-tanam').html('<option value="">All Tahun Tanam</option>');
                    table.ajax.reload();
                });

                $('#filter-afdeling').on('change', function() {
                    const regional = $('#filter-regional').val();
                    const plant = $('#filter-kebun').val();
                    const afdeling = this.value;
                    loadTahunTanam(regional, plant, afdeling);
                    table.ajax.reload();
                });

                let debounceTimer;
                $('#filter-regional, #filter-kebun, #filter-afdeling, #filter-tahun-tanam, #filter-jenis-pupuk, #filter-semester-pemupukan')
                    .on('change', function() {
                        clearTimeout(debounceTimer);
                        debounceTimer = setTimeout(() => table.ajax.reload(), 300);
                    });
            });

            function deleteRencanaPemupukan(id) {
                if (confirm('Are you sure you want to delete this record?')) {
                    fetch('/rencana-pemupukan/' + id, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                $('#dataTable').DataTable().ajax.reload();
                                alert('Record deleted successfully');
                            } else {
                                alert('Failed to delete record');
                            }
                        })
                        .catch(() => alert('An error occurred'));
                }
            }
        </script>
    @endpush
</x-app-layout>
