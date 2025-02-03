@push('scripts')
    {{ $dataTable->scripts() }}
    <script>
        $(document).ready(function() {
            // Function to enable/disable dropdowns and fetch options dynamically
            function toggleDropdowns() {
                const regional = $('#filter-regional').val();
                const kebun = $('#filter-kebun').val();
                const afdeling = $('#filter-afdeling').val();

                // Manage kebun dropdown
                if (regional) {
                    $('#filter-kebun').prop('disabled', false);
                    fetchDropdownOptions('/api/kebun-code/' + regional, '#filter-kebun', 'All Kebun');
                } else {
                    resetDropdown('#filter-kebun');
                    resetDropdown('#filter-afdeling');
                }

                // Manage afdeling dropdown
                if (regional && kebun) {
                    $('#filter-afdeling').prop('disabled', false);
                    fetchDropdownOptions(`/api/afdeling-code/${regional}/${kebun}`, '#filter-afdeling',
                        'All Afdeling');
                } else {
                    resetDropdown('#filter-afdeling');
                }

                // Manage tahun tanam dropdown
                if (regional || kebun || afdeling) {
                    $('#filter-tahun_tanam').prop('disabled', false);
                    fetchDropdownOptions(`/api/tahun-tanam-code/${regional}/${kebun}/${afdeling}`,
                        '#filter-tahun_tanam', 'All Tahun Tanam');
                } else {
                    resetDropdown('#filter-tahun_tanam');
                }
            }

            // Fetch options for a dropdown and populate it
            function fetchDropdownOptions(url, dropdownSelector, placeholder) {
                const currentValue = $(dropdownSelector).val();
                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function(data) {
                        let options = `<option value="">${placeholder}</option>`;
                        if (Array.isArray(data)) {
                            data.forEach(item => {
                                options += `<option value="${item}">${item}</option>`;
                            });
                        } else {
                            for (const key in data) {
                                if (data.hasOwnProperty(key)) {
                                    options += `<option value="${key}">${data[key]}</option>`;
                                }
                            }
                        }
                        $(dropdownSelector).html(options).val(currentValue).prop('disabled', false);
                    },
                    error: function() {
                        $(dropdownSelector).html(
                            `<option value="">Error loading ${placeholder}</option>`).prop(
                            'disabled', true);
                    }
                });
            }

            // Reset dropdown to its initial state
            function resetDropdown(selector) {
                $(selector).html('<option value="">All</option>').prop('disabled', true).val('');
            }

            // Reload DataTable when filters change
            function reloadDataTable() {
                $('#dataTable').DataTable().ajax.reload();
            }

            // Initialize and attach event listeners
            toggleDropdowns();

            $('#filter-regional, #filter-kebun, #filter-afdeling, #filter-tahun_tanam').on('change', function() {
                toggleDropdowns();
                reloadDataTable();
            });
        });
    </script>
@endpush

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
                        @if (request()->routeIs('rencana-realisasi-pemupukan.index') || request()->routeIs('rekap-pemupukan'))
                            <div class="row mb-3 px-3">
                                <div class="col-md-2">
                                    <select id="filter-regional" class="form-control">
                                        <option value="">All Regional</option>
                                        @foreach ($regionals as $regional)
                                            <option value="{{ $regional }}">{{ $regional }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select id="filter-kebun" class="form-control" disabled>
                                        <option value="">All Kebun</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select id="filter-afdeling" class="form-control" disabled>
                                        <option value="">All Afdeling</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select id="filter-tahun_tanam" class="form-control" disabled>
                                        <option value="">All Tahun Tanam</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select id="filter-jenis_pupuk" class="form-control">
                                        <option value="">All Jenis Pupuk</option>
                                        @foreach ($jenisPupuks as $jenis)
                                            <option value="{{ $jenis }}">{{ $jenis }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif
                        <div class="table-responsive">
                            {{ $dataTable->table(['class' => 'table text-center table-striped w-100'], true) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
