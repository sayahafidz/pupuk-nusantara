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
                        <div class="col-md-2">
                            <select id="filter-regional" class="form-control"
                                {{ $auth_user->regional !== 'head_office' ? 'disabled' : '' }}>
                                <option value="">All Regional</option>
                                @foreach ($regionals as $regional)
                                    <option value="{{ $regional }}"
                                        {{ request('regional', $default_regional) == $regional ? 'selected' : '' }}>
                                        {{ $regional }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select id="filter-kebun" class="form-control">
                                <option value="">All Kebun</option>
                                @foreach ($kebuns as $kebun)
                                    <option value="{{ $kebun }}"
                                        {{ request('kebun', $default_kebun) == $kebun ? 'selected' : '' }}>
                                        {{ $kebun }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select id="filter-afdeling" class="form-control">
                                <option value="">All Afdeling</option>
                                @foreach ($afdelings as $afdeling)
                                    <option value="{{ $afdeling }}"
                                        {{ request('afdeling') == $afdeling ? 'selected' : '' }}>
                                        {{ $afdeling }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select id="filter-tahun_tanam" class="form-control">
                                <option value="">All Tahun Tanam</option>
                                @foreach ($tahunTanams as $tahun)
                                    <option value="{{ $tahun }}"
                                        {{ request('tahun_tanam') == $tahun ? 'selected' : '' }}>
                                        {{ $tahun }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select id="filter-jenis_pupuk" class="form-control">
                                <option value="">All Jenis Pupuk</option>
                                @foreach ($jenisPupuks as $jenis)
                                    <option value="{{ $jenis }}"
                                        {{ request('jenis_pupuk') == $jenis ? 'selected' : '' }}>
                                        {{ $jenis }}
                                    </option>
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
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
        <script>
            $(document).ready(function() {
                let table = $('#dataTable').DataTable({
                    processing: true,
                    serverSide: true,
                    deferRender: true,
                    pageLength: 25,
                    ajax: {
                        url: "{{ route('rekap-pemupukan') }}",
                        data: function(d) {
                            d.regional = $('#filter-regional').val();
                            d.kebun = $('#filter-kebun').val();
                            d.afdeling = $('#filter-afdeling').val();
                            d.tahun_tanam = $('#filter-tahun_tanam').val();
                            d.jenis_pupuk = $('#filter-jenis_pupuk').val();
                        },
                        cache: true
                    },
                    columns: [{
                            data: 'id',
                            name: 'id',
                            title: 'ID'
                        },
                        {
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
                            data: null,
                            title: 'Action',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row) {
                                return `
                                    <a href="#" class="btn btn-sm btn-primary">Edit</a>
                                    <a href="#" class="btn btn-sm btn-danger">Delete</a>
                                `;
                            }
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

                // Debounce filter changes
                let debounceTimer;
                $('#filter-regional, #filter-kebun, #filter-afdeling, #filter-tahun_tanam, #filter-jenis_pupuk').on(
                    'change',
                    function() {
                        clearTimeout(debounceTimer);
                        debounceTimer = setTimeout(() => table.ajax.reload(), 300);
                    });

                // WhatsApp functionality
                window.sendWhatsappData = function() {
                    fetch("{{ route('whatsapp.send-data') }}", {
                            method: 'GET',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            Swal.fire({
                                icon: data.success ? 'success' : 'error',
                                title: data.success ? 'Success' : 'Error',
                                text: data.success ? 'Data sent successfully' : 'Failed to send data'
                            });
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred while sending data'
                            });
                        });
                };
            });
        </script>
    @endpush
</x-app-layout>
