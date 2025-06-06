<x-app-layout :assets="$assets ?? []">
    <div class="row">
        <div class="col-md-12 col-lg-12">
            <div class="row row-cols-12">
                <div class="d-slider1 overflow-hidden ">
                    <ul class="swiper-wrapper list-inline m-0 p-0 mb-2">
                        <li class="swiper-slide card card-slide" data-aos="fade-up" data-aos-delay="700">
                            <div class="card-body">
                                <div class="progress-widget">
                                    <div id="circle-progress-01"
                                        class="circle-progress-01 circle-progress circle-progress-primary text-center"
                                        data-min-value="0" data-max-value="100" data-value="90" data-type="percent">
                                        <svg class="card-slie-arrow " width="24" height="24px" viewBox="0 0 24 24">
                                            <path fill="currentColor"
                                                d="M5,17.59L15.59,7H9V5H19V15H17V8.41L6.41,19L5,17.59Z" />
                                        </svg>
                                    </div>
                                    <div class="progress-detail">
                                        <p class="mb-2">Total Rencana</p>
                                        <h4 class="counter" style="visibility: visible;">{{ number_format($rencana_pemupukan, 0, ',', '.') }}</h4>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="swiper-slide card card-slide" data-aos="fade-up" data-aos-delay="800">
                            <div class="card-body">
                                <div class="progress-widget">
                                    <div id="circle-progress-02"
                                        class="circle-progress-01 circle-progress circle-progress-info text-center"
                                        data-min-value="0" data-max-value="100" data-value="80" data-type="percent">
                                        <svg class="card-slie-arrow " width="24" height="24" viewBox="0 0 24 24">
                                            <path fill="currentColor"
                                                d="M19,6.41L17.59,5L7,15.59V9H5V19H15V17H8.41L19,6.41Z" />
                                        </svg>
                                    </div>
                                    <div class="progress-detail">
                                        <p class="mb-2">Total Realisasi</p>
                                        <h4 class="counter">{{ number_format($pemupukan, 0, ',', '.') }}</h4>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="swiper-slide card card-slide" data-aos="fade-up" data-aos-delay="900">
                            <div class="card-body">
                                <div class="progress-widget">
                                    <div id="circle-progress-03"
                                        class="circle-progress-01 circle-progress circle-progress-primary text-center"
                                        data-min-value="0" data-max-value="100" data-value="70" data-type="percent">
                                        <svg class="card-slie-arrow " width="24" viewBox="0 0 24 24">
                                            <path fill="currentColor"
                                                d="M19,6.41L17.59,5L7,15.59V9H5V19H15V17H8.41L19,6.41Z" />
                                        </svg>
                                    </div>
                                    <div class="progress-detail">
                                        <p class="mb-2">Jumlah Pupuk</p>
                                        <h4 class="counter"><?= $jenis_pupuk ?></h4>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="swiper-slide card card-slide" data-aos="fade-up" data-aos-delay="1000">
                            <div class="card-body">
                                <div class="progress-widget">
                                    <div id="circle-progress-04"
                                        class="circle-progress-01 circle-progress circle-progress-info text-center"
                                        data-min-value="0" data-max-value="100" data-value="60" data-type="percent">
                                        <svg class="card-slie-arrow " width="24px" height="24px" viewBox="0 0 24 24">
                                            <path fill="currentColor"
                                                d="M5,17.59L15.59,7H9V5H19V15H17V8.41L6.41,19L5,17.59Z" />
                                        </svg>
                                    </div>
                                    <div class="progress-detail">
                                        <p class="mb-2">Persentasi Realisasi</p>
                                        <h4 class="counter">{{ number_format($percentage_pemupukan, 2) }}%</h4>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="swiper-slide card card-slide" data-aos="fade-up" data-aos-delay="1100">
                            <div class="card-body">
                                <div class="progress-widget">
                                    <div id="circle-progress-05"
                                        class="circle-progress-01 circle-progress circle-progress-primary text-center"
                                        data-min-value="0" data-max-value="100" data-value="50" data-type="percent">
                                        <svg class="card-slie-arrow " width="24px" height="24px" viewBox="0 0 24 24">
                                            <path fill="currentColor"
                                                d="M5,17.59L15.59,7H9V5H19V15H17V8.41L6.41,19L5,17.59Z" />
                                        </svg>
                                    </div>
                                    <div class="progress-detail">
                                        <p class="mb-2">Total User</p>
                                        <h4 class="counter"><?= $users ?></h4>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="swiper-slide card card-slide" data-aos="fade-up" data-aos-delay="1200">
                            <div class="card-body">
                                <div class="progress-widget">
                                    <div id="circle-progress-06"
                                        class="circle-progress-01 circle-progress circle-progress-info text-center"
                                        data-min-value="0" data-max-value="100" data-value="40" data-type="percent">
                                        <svg class="card-slie-arrow " width="24" viewBox="0 0 24 24">
                                            <path fill="currentColor"
                                                d="M19,6.41L17.59,5L7,15.59V9H5V19H15V17H8.41L19,6.41Z" />
                                        </svg>
                                    </div>
                                    <div class="progress-detail">
                                        <p class="mb-2">Jumlah Pemupukan</p>
                                        <h4 class="counter">{{ number_format($jumlah_pupuk, 0, ',', '.') }} Kg</h4>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <div class="swiper-button swiper-button-next"></div>
                    <div class="swiper-button swiper-button-prev"></div>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-lg-12">
            <div class="row">
                <div class="col-md-12">
                    <div class="card" data-aos="fade-up" data-aos-delay="800">
                        <div class="card-header d-flex justify-content-between flex-wrap">
                            <div class="header-title">
                                <h4 class="card-title">Pemupukan Harian</h4>
                            </div>
                            <div class="d-flex align-items-center align-self-center">
                                <div class="d-flex align-items-center text-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" viewBox="0 0 24 24"
                                        fill="currentColor">
                                        <g id="Solid dot2">
                                            <circle id="Ellipse 65" cx="12" cy="12" r="8"
                                                fill="currentColor"></circle>
                                        </g>
                                    </svg>
                                    <div class="ms-2">
                                        <span class="text-gray">Pemupukan</span>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown">
                                <a href="#" class="btn btn-secondary dropdown-toggle" id="dropdownMenuButton2"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    Minggu Ini
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton2">
                                    <li><a class="dropdown-item" href="#" data-period="week">Minggu Ini</a>
                                    </li>
                                    <li><a class="dropdown-item" href="#" data-period="month">Bulan Ini</a>
                                    </li>
                                    <li><a class="dropdown-item" href="#" data-period="year">Tahun Ini</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="d-main" class="d-main"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-lg-12">
            <div class="card" data-aos="fade-up" data-aos-delay="800">
                <div class="card-header d-flex justify-content-between flex-wrap">
                    <div class="header-title">
                        <h4 class="card-title">Rencana dan Realisasi Pemupukan 2025</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="rencana-pemupukan-table" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th rowspan="2" class="text-center align-middle">ENTITAS</th>
                                    <th rowspan="2" class="text-center align-middle">Jenis Pupuk</th>
                                    <th colspan="3" class="text-center">Semester I</th>
                                    <th colspan="3" class="text-center">Semester II</th>
                                    <th colspan="3" class="text-center">Tahun 2025</th>
                                </tr>
                                <tr>
                                    <th class="text-center">Rencana (Kg)</th>
                                    <th class="text-center">Realisasi (Kg)</th>
                                    <th class="text-center">% Real Thdp Renc</th>
                                    <th class="text-center">Rencana (Kg)</th>
                                    <th class="text-center">Realisasi (Kg)</th>
                                    <th class="text-center">% Real Thdp Renc</th>
                                    <th class="text-center">Rencana (Kg)</th>
                                    <th class="text-center">Realisasi (Kg)</th>
                                    <th class="text-center">% Real Thdp Renc</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php 
                                    $lastEntitas = '';
                                    $entitasCount = [];
                                    $totalGroups = [
                                        'Palm Co Regional 1 + KSO',
                                        'Palm Co Regional 2 + KSO',
                                        'Total Palm Co',
                                        'Total Regional KSO',
                                        'HOLDING'
                                    ];
                                    
                                    // First pass untuk menghitung baris per entitas
                                    foreach($tableData as $data) {
                                        if (isset($data['is_group_total'])) {
                                            // Skip grup total
                                            continue;
                                        }
                                        
                                        if (!isset($entitasCount[$data['entitas']])) {
                                            $entitasCount[$data['entitas']] = 0;
                                        }
                                        $entitasCount[$data['entitas']]++;
                                    }
                                @endphp
        
                                @foreach($tableData as $index => $data)
                                    @php
                                        // Tentukan class berdasarkan jenis baris
                                        $rowClass = '';
                                        $isGroupTotal = isset($data['is_group_total']) && $data['is_group_total'] === true;
                                        
                                        // Cek apakah ini adalah baris total grup
                                        if ($isGroupTotal) {
                                            $rowClass = 'table-success';
                                        } elseif ($data['kebun'] === 'Jumlah') {
                                            $rowClass = 'table-primary'; // Warna biru untuk jumlah non-grup
                                        }
                                        
                                        // Cek apakah entitas ini adalah awal dari entitas baru
                                        $isNewEntity = $lastEntitas !== $data['entitas'];
                                        
                                        // Mengatur jumlah baris untuk rowspan
                                        $rowspan = $isGroupTotal ? 3 : ($entitasCount[$data['entitas']] ?? 0);
                                    @endphp
                                    
                                    <tr class="{{ $rowClass }}">
                                        @if($isNewEntity)
                                            @if($isGroupTotal)
                                                <!-- Grup total dengan rowspan=3 -->
                                                <td rowspan="3" class="align-middle text-center fw-bold bg-success text-white">
                                                    {{ $data['entitas'] }}
                                                </td>
                                            @else
                                                <!-- Entitas reguler dengan rowspan sesuai jumlah baris -->
                                                <td rowspan="{{ $rowspan }}" class="align-middle text-center">
                                                    {{ $data['entitas'] }}
                                                </td>
                                            @endif
                                            @php $lastEntitas = $data['entitas']; @endphp
                                        @endif
                                        
                                        <td class="{{ $data['kebun'] === 'Jumlah' || $isGroupTotal ? 'fw-bold' : '' }}">
                                            @if($data['kebun'] !== 'Jumlah')
                                                <span onclick="window.showDetails('{{ $data['entitas'] }}', '{{ $data['kebun'] }}')" 
                                                        style="cursor: pointer; text-decoration: underline;">
                                                    {{ $data['kebun'] }}
                                                </span>
                                            @else
                                                {{ $data['kebun'] }}
                                            @endif
                                        </td>
                                        <td class="text-end">{{ number_format($data['semester1_rencana'], 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($data['semester1_realisasi'], 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($data['semester1_percentage'], 2) }} %</td>
                                        <td class="text-end">{{ number_format($data['semester2_rencana'], 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($data['semester2_realisasi'], 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($data['semester2_percentage'], 2) }} %</td>
                                        <td class="text-end">{{ number_format($data['tahun_rencana'], 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($data['tahun_realisasi'], 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($data['tahun_percentage'], 2) }} %</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Kebun details -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalTitle">Detail Kebun</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="detailModalBody">
                    <!-- Content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Afdeling details -->
    <div class="modal fade" id="afdelingModal" tabindex="-1" aria-labelledby="afdelingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="afdelingModalTitle">Detail Afdeling</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="afdelingModalBody">
                    <!-- Content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Global variables untuk menyimpan instance modal
        let detailModal = null;
        let afdelingModal = null;
        
        // Function untuk membersihkan DOM dan event listeners saat modal ditutup
        function cleanupModal(modalElement) {
            // Hapus event listeners yang terkait dengan modal
            $(modalElement).find('[data-bs-dismiss="modal"]').off('click');
            
            // Pastikan modal backdrop dihapus
            $('.modal-backdrop').remove();
            
            // Reset body styling
            $('body').removeClass('modal-open');
            $('body').css('overflow', '');
            $('body').css('padding-right', '');
        }
        
        // Global function definition for showing details
        window.showDetails = function(entitas, jenisPupuk) {
            // Show loading indicator in modal
            document.getElementById('detailModalTitle').innerText = 'Detail ' + jenisPupuk + ' - ' + entitas;
            document.getElementById('detailModalBody').innerHTML = '<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';
            
            // Destroy existing modals if they exist
            if (detailModal) {
                detailModal.hide();
                detailModal.dispose();
            }
            
            // Show the modal using Bootstrap JS
            var modalElement = document.getElementById('detailModal');
            detailModal = new bootstrap.Modal(modalElement);
            detailModal.show();
            
            // Listen for modal hidden event to clean up properly
            $(modalElement).on('hidden.bs.modal', function () {
                cleanupModal(modalElement);
            });
            
            // Fetch the data
            fetch('/kebun/details?entitas=' + encodeURIComponent(entitas) + '&jenis_pupuk=' + encodeURIComponent(jenisPupuk))
                .then(function(response) {
                    return response.text();
                })
                .then(function(html) {
                    document.getElementById('detailModalBody').innerHTML = html;
                    
                    // Initialize DataTable for the details table
                    setTimeout(function() {
                        if ($.fn.DataTable.isDataTable('#kebun-details-table')) {
                            $('#kebun-details-table').DataTable().destroy();
                        }
                        
                        var kebunTable = $('#kebun-details-table').DataTable({
                            responsive: true,
                            pageLength: 10,
                            language: {
                                url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
                            }
                        });
                        
                        // Refresh table layout after init to avoid visual glitches
                        kebunTable.columns.adjust().draw();
                    }, 100); // Small delay to ensure the DOM is updated
                })
                .catch(function(error) {
                    document.getElementById('detailModalBody').innerHTML = '<div class="alert alert-danger">Terjadi kesalahan saat memuat data. Silakan coba lagi.</div>';
                    console.error('Error loading kebun details:', error);
                });
        };
        
        // New function for showing afdeling details
        window.showAfdelingDetails = function(entitas, kebun, jenisPupuk) {
            // Show loading indicator in modal
            document.getElementById('afdelingModalTitle').innerText = 'Detail Afdeling - ' + kebun + ' (' + jenisPupuk + ')';
            document.getElementById('afdelingModalBody').innerHTML = '<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';
            
            // Destroy existing modal if it exists
            if (afdelingModal) {
                afdelingModal.hide();
                afdelingModal.dispose();
            }
            
            // Show the modal using Bootstrap JS
            var modalElement = document.getElementById('afdelingModal');
            afdelingModal = new bootstrap.Modal(modalElement);
            afdelingModal.show();
            
            // Listen for modal hidden event to clean up properly
            $(modalElement).on('hidden.bs.modal', function () {
                cleanupModal(modalElement);
            });
            
            // Fetch the data
            fetch('/afdeling/details?entitas=' + encodeURIComponent(entitas) + '&kebun=' + encodeURIComponent(kebun) + '&jenis_pupuk=' + encodeURIComponent(jenisPupuk))
                .then(function(response) {
                    return response.text();
                })
                .then(function(html) {
                    document.getElementById('afdelingModalBody').innerHTML = html;
                    
                    // Initialize DataTable for the afdeling details table
                    setTimeout(function() {
                        if ($.fn.DataTable.isDataTable('#afdeling-details-table')) {
                            $('#afdeling-details-table').DataTable().destroy();
                        }
                        
                        var afdelingTable = $('#afdeling-details-table').DataTable({
                            responsive: true,
                            pageLength: 10,
                            language: {
                                url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
                            }
                        });
                        
                        // Refresh table layout after init
                        afdelingTable.columns.adjust().draw();
                    }, 100); // Small delay to ensure the DOM is updated
                })
                .catch(function(error) {
                    document.getElementById('afdelingModalBody').innerHTML = '<div class="alert alert-danger">Terjadi kesalahan saat memuat data. Silakan coba lagi.</div>';
                    console.error('Error loading afdeling details:', error);
                });
        };
        
        // Additional event listeners for the "Tutup" buttons to ensure proper cleanup
        $(document).ready(function() {
            // For detailModal
            $('#detailModal .btn-secondary, #detailModal .btn-close').on('click', function() {
                if (detailModal) {
                    detailModal.hide();
                }
                cleanupModal(document.getElementById('detailModal'));
            });
            
            // For afdelingModal
            $('#afdelingModal .btn-secondary, #afdelingModal .btn-close').on('click', function() {
                if (afdelingModal) {
                    afdelingModal.hide();
                }
                cleanupModal(document.getElementById('afdelingModal'));
            });
            
            // Initialize the main DataTable
            var mainTable = $('#rencana-pemupukan-table').DataTable({
                responsive: true,
                searching: true,
                ordering: false, // Disable sorting to maintain custom ordering
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
                }
            });
        });
    </script>
</x-app-layout>