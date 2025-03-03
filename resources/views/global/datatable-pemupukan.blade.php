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
                            <select id="filter-kebun" class="form-control">
                                {{ $auth_user->regional !== 'head_office' ? 'disabled' : '' }}>
                                <option value="">All Kebun</option>
                                <!-- Kebun options will be populated dynamically -->
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
                                {{-- @foreach ($tahunTanams as $tahun_tanam)
                                    <option value="{{ $tahun_tanam }}">{{ $tahun_tanam }}</option>
                                @endforeach --}}
                            </select>
                        </div>
                        <div class="col-md-3 p-3">
                            <select id="filter-jenis-pupuk" class="form-control">
                                <option value="">All Jenis Pupuk</option>
                                @foreach ($jenisPupuks as $jenis)
                                    <option value="{{ $jenis }}">{{ $jenis }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 p-3">
                            <input type="text" id="filter-tgl-pemupukan-start" class="form-control"
                                placeholder="Start Date">
                        </div>
                        <div class="col-md-3 p-3">
                            <input type="text" id="filter-tgl-pemupukan-end" class="form-control"
                                placeholder="End Date">
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
        <!-- Include jQuery (assuming it's not already included via $assets) -->
        {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
        <!-- DataTables Core -->
        {{-- <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script> --}}
        <!-- DataTables Buttons -->
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
        <!-- Bootstrap Datepicker -->
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

                            // Explicitly set the selected value to default_kebun
                            if (defaultKebun) {
                                $('#filter-kebun').val(defaultKebun);
                            }

                            // Trigger Afdeling load if a kebun is selected
                            const selectedKebun = $('#filter-kebun').val();
                            if (selectedKebun) {
                                loadAfdeling(regional, selectedKebun);
                            }
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

                            // Trigger Tahun Tanam load if an afdeling is selected
                            const selectedAfdeling = $('#filter-afdeling').val();
                            if (selectedAfdeling) {
                                loadTahunTanam(regional, kebun, selectedAfdeling);
                            }
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
                        url: "{{ route('rekap-pemupukan') }}",
                        data: function(d) {
                            d.regional = $('#filter-regional').val();
                            d.kebun = $('#filter-kebun').val();
                            d.afdeling = $('#filter-afdeling').val();
                            d.tahun_tanam = $('#filter-tahun-tanam').val();
                            d.jenis_pupuk = $('#filter-jenis-pupuk').val();
                            d.tgl_pemupukan_start = $('#filter-tgl-pemupukan-start').val();
                            d.tgl_pemupukan_end = $('#filter-tgl-pemupukan-end').val();
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
                            title: 'Kode Plant'
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
                            data: 'tgl_pemupukan',
                            name: 'tgl_pemupukan',
                            title: 'Tanggal Pemupukan'
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

                // Initial setup based on user data
                const authUserRegional = '{{ $auth_user->regional }}';
                const initialRegional = $('#filter-regional').val() || '{{ $default_regional ?? '' }}';
                const initialKebun = '{{ $default_kebun ?? '' }}';
                // Assuming you might want to set a default Afdeling, add it if available
                const initialAfdeling =
                    '{{ $default_afdeling ?? '' }}'; // Add this if you have a default_afdeling in your controller

                console.log('Initial setup:', {
                    authUserRegional,
                    initialRegional,
                    initialKebun,
                    initialAfdeling
                });

                // Load initial Kebun, Afdeling, and Tahun Tanam based on defaults
                if (initialRegional) {
                    loadKebun(initialRegional);
                    if (initialKebun) {
                        $('#filter-kebun').val(initialKebun); // Set the Kebun dropdown
                        loadAfdeling(initialRegional, initialKebun);
                        if (initialAfdeling) {
                            $('#filter-afdeling').val(initialAfdeling); // Set the Afdeling dropdown if provided
                            loadTahunTanam(initialRegional, initialKebun, initialAfdeling);
                        }
                        table.ajax.reload(); // Refresh DataTable with the selected filters
                    }
                }

                // Event listeners for dynamic dropdowns
                $('#filter-regional').on('change', function() {
                    const regional = this.value;
                    loadKebun(regional);
                    $('#filter-afdeling').html('<option value="">All Afdeling</option>'); // Reset Afdeling
                    $('#filter-tahun-tanam').html(
                        '<option value="">All Tahun Tanam</option>'); // Reset Tahun Tanam
                    table.ajax.reload();
                });

                $('#filter-kebun').on('change', function() {
                    const regional = $('#filter-regional').val();
                    const kebun = this.value;
                    loadAfdeling(regional, kebun);
                    $('#filter-tahun-tanam').html(
                        '<option value="">All Tahun Tanam</option>'); // Reset Tahun Tanam
                    table.ajax.reload();
                });

                $('#filter-afdeling').on('change', function() {
                    const regional = $('#filter-regional').val();
                    const kebun = $('#filter-kebun').val();
                    const afdeling = this.value;
                    loadTahunTanam(regional, kebun,
                        afdeling); // Load Tahun Tanam based on Regional, Kebun, Afdeling
                    table.ajax.reload();
                });

                // Debounce filter changes for other filters
                let debounceTimer;
                $('#filter-tahun-tanam, #filter-jenis-pupuk, #filter-tgl-pemupukan-start, #filter-tgl-pemupukan-end')
                    .on('change', function() {
                        clearTimeout(debounceTimer);
                        debounceTimer = setTimeout(() => table.ajax.reload(), 300);
                    });
            });

            // Delete function (unchanged)
            function deletePemupukan(id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You will not be able to recover this record!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('/pemupukan/' + id, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.message === "Data has been deleted successfully!") {
                                    $('#dataTable').DataTable().ajax.reload(); // Reload the DataTable
                                    Swal.fire({
                                        title: 'Deleted!',
                                        text: 'The record has been deleted.',
                                        icon: 'success',
                                        confirmButtonText: 'OK'
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: 'Failed to delete the record.',
                                        icon: 'error',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            })
                            .catch(error => {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'An error occurred while deleting the record.',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            });
                    }
                });
            }
        </script>
    @endpush
</x-app-layout>
