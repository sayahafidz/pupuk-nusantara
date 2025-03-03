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
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
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
                        })
                        .catch(error => {
                            console.error('Error loading kebun:', error);
                            $('#filter-kebun').html('<option value="">All Kebun</option>');
                        });
                }

                // Initialize DataTable
                let table = $('#dataTable').DataTable({
                    processing: true,
                    serverSide: true,
                    deferRender: true,
                    pageLength: 10,
                    ajax: {
                        url: "{{ route('ren-rel-pem-afd.index') }}",
                        data: function(d) {
                            d.regional = $('#filter-regional').val();
                            d.kebun = $('#filter-kebun').val();
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
                            data: 'afdeling',
                            name: 'afdeling',
                            title: 'Afdeling'
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

                // Initial setup
                const initialRegional = $('#filter-regional').val() || '{{ $default_regional ?? '' }}';
                const initialKebun = '{{ $default_kebun ?? '' }}';
                if (initialRegional) {
                    loadKebun(initialRegional);
                    if (initialKebun) {
                        $('#filter-kebun').val(initialKebun);
                        table.ajax.reload();
                    }
                }

                // Event listeners for filter changes
                $('#filter-regional').on('change', function() {
                    const regional = this.value;
                    loadKebun(regional);
                    table.ajax.reload();
                });

                $('#filter-kebun').on('change', function() {
                    table.ajax.reload();
                });

                // Delete function (placeholder, implement server-side logic)
                window.deleteRecord = function(regional, kebun, afdeling) {
                    if (confirm('Are you sure you want to delete this record?')) {
                        // Add DELETE request here, e.g., using fetch or axios
                        console.log(`Delete: ${regional}, ${kebun}, ${afdeling}`);
                        // Example: fetch('/delete-route', { method: 'DELETE', ... }).then(() => table.ajax.reload());
                    }
                };
            });
        </script>
    @endpush
</x-app-layout>
