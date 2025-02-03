@push('scripts')
    {{ $dataTable->scripts() }}
    <script>
        $(document).ready(function() {
            // Attach change event listeners to the filters
            $('#filter-regional, #filter-kebun, #filter-afdeling, #filter-tahun_tanam, #filter-jenis_pupuk').on(
                'change',
                function() {
                    $('#dataTable').DataTable().ajax.reload();
                });
        });
    </script>

    <script>
        function sendWhatsappData() {
            fetch("{{ route('whatsapp.send-data') }}", {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Data sent successfully'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to send data'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while sending data'
                    });
                });
        }
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
                        <div class="card-action">
                            {!! $headerAction ?? '' !!}
                        </div>
                    </div>
                    <div class="card-body px-0">
                        @if (request()->routeIs('rencana-pemupukan.index') || request()->routeIs('rekap-pemupukan'))
                            <!-- Dropdown filters specific to Rencana Pemupukan -->
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
                                    <select id="filter-kebun" class="form-control">
                                        <option value="">All Kebun</option>
                                        @foreach ($kebuns as $kebun)
                                            <option value="{{ $kebun }}">{{ $kebun }}</option>
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
                                    <select id="filter-tahun_tanam" class="form-control">
                                        <option value="">All Tahun Tanam</option>
                                        @foreach ($tahunTanams as $tahun)
                                            <option value="{{ $tahun }}">{{ $tahun }}</option>
                                        @endforeach
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
                        <!-- Data Table -->
                        <div class="table-responsive">
                            {{ $dataTable->table(['class' => 'table text-center table-striped w-100'], true) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
