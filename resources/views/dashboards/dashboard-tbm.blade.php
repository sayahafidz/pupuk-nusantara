<x-app-layout :assets="$assets ?? []">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="header-title mb-3">
                        <h4 class="card-title">Realisasi Pemupukan TBM Tahun {{ $data['tahun'] }}</h4>
                    </div>
                    <div>
                        <form method="GET" action="{{ route('dashboard-tbm') }}" class="mb-3">
                            <div class="row">
                                <div class="col-md-2 mb-2">
                                    <div class="form-group">
                                        <label class="form-label">Tahun</label>
                                        <select name="tahun" class="form-select">
                                            @foreach ($data['tahun_list'] as $tahunOption)
                                                <option value="{{ $tahunOption }}"
                                                    {{ $data['tahun'] == $tahunOption ? 'selected' : '' }}>
                                                    {{ $tahunOption }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2 mb-2">
                                    <div class="form-group">
                                        <label class="form-label">Bulan Tanam</label>
                                        <select name="bulan_tanam" class="form-select">
                                            <option value="">Semua</option>
                                            @for ($m = 1; $m <= 12; $m++)
                                                <option value="{{ $m }}"
                                                    {{ $data['bulan_tanam'] == $m ? 'selected' : '' }}>
                                                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2 mb-2">
                                    <div class="form-group">
                                        <label class="form-label">Tahun Tanam</label>
                                        <select name="tahun_tanam" class="form-select">
                                            <option value="">Semua</option>
                                            @foreach ($data['tahun_tanam_list'] as $tahunTanam)
                                                <option value="{{ $tahunTanam }}"
                                                    {{ $data['tahun_tanam'] == $tahunTanam ? 'selected' : '' }}>
                                                    {{ $tahunTanam }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2 mb-2">
                                    <div class="form-group">
                                        <label class="form-label">Bahan Tanam</label>
                                        <select name="bahan_tanam" class="form-select">
                                            <option value="">Semua</option>
                                            @foreach ($data['bahan_tanam_list'] as $bahanTanam)
                                                <option value="{{ $bahanTanam }}"
                                                    {{ $data['bahan_tanam'] == $bahanTanam ? 'selected' : '' }}>
                                                    {{ $bahanTanam }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2 mb-2">
                                    <div class="form-group">
                                        <label class="form-label">Jenis Pupuk</label>
                                        <select name="jenis_pupuk" class="form-select">
                                            <option value="">Semua</option>
                                            @foreach ($data['jenis_pupuk_list'] as $jenisPupuk)
                                                <option value="{{ $jenisPupuk }}"
                                                    {{ $data['jenis_pupuk'] == $jenisPupuk ? 'selected' : '' }}>
                                                    {{ $jenisPupuk }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2 mb-2">
                                    <div class="form-group">
                                        <label class="form-label">&nbsp;</label>
                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-primary">Filter</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 text-end">
                                    <a href="{{ route('dashboard-tbm') }}" class="btn btn-secondary">Reset Filter</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th rowspan="2" class="text-center align-middle">Regional</th>
                                    <th rowspan="2" class="text-center align-middle">Jenis Pupuk</th>
                                    <th colspan="3" class="text-center">Semester I</th>
                                    <th colspan="3" class="text-center">Semester II</th>
                                    <th colspan="3" class="text-center">Tahun {{ $data['tahun'] }}</th>
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
                                @foreach ($data['hasil'] as $item)
                                    @php
                                        $isSpecialRegion = in_array($item['regional'], [
                                            'Palm Co Regional 1 + KSO',
                                            'Palm Co Regional 2 + KSO',
                                            'Total Palm Co',
                                            'Total Regional KSO',
                                            'HOLDING',
                                        ]);
                                    @endphp
                                    <!-- Pupuk Tunggal -->
                                    <tr class="{{ $isSpecialRegion ? 'table-success' : '' }}">
                                        @if ($isSpecialRegion)
                                            <td rowspan="3"
                                                class="align-middle text-center fw-bold bg-success text-white">
                                                {{ $item['regional'] }}
                                            </td>
                                        @else
                                            <td rowspan="3" class="align-middle text-center">
                                                {{ $item['regional'] }}
                                            </td>
                                        @endif
                                        <td>
                                            @if ($isSpecialRegion)
                                                Pupuk Tunggal
                                            @else
                                                <a href="#"
                                                    class="text-decoration-underline detail-link text-black"
                                                    data-bs-toggle="modal" data-bs-target="#detailModal"
                                                    data-regional="{{ $item['regional'] }}" data-jenis="tunggal"
                                                    data-tahun="{{ $data['tahun'] }}"
                                                    data-bulan-tanam="{{ $data['bulan_tanam'] }}"
                                                    data-tahun-tanam="{{ $data['tahun_tanam'] }}"
                                                    data-bahan-tanam="{{ $data['bahan_tanam'] }}"
                                                    data-jenis-pupuk="{{ $data['jenis_pupuk'] }}">
                                                    Pupuk Tunggal
                                                </a>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            {{ number_format($item['kebun']['pupuk_tunggal']['semester_1']['rencana']) }}
                                        </td>
                                        <td class="text-end">
                                            {{ number_format($item['kebun']['pupuk_tunggal']['semester_1']['realisasi']) }}
                                        </td>
                                        <td class="text-end">
                                            {{ $item['kebun']['pupuk_tunggal']['semester_1']['persentase'] }}%</td>
                                        <td class="text-end">
                                            {{ number_format($item['kebun']['pupuk_tunggal']['semester_2']['rencana']) }}
                                        </td>
                                        <td class="text-end">
                                            {{ number_format($item['kebun']['pupuk_tunggal']['semester_2']['realisasi']) }}
                                        </td>
                                        <td class="text-end">
                                            {{ $item['kebun']['pupuk_tunggal']['semester_2']['persentase'] }}%</td>
                                        <td class="text-end">
                                            {{ number_format($item['kebun']['pupuk_tunggal']['tahun']['rencana']) }}
                                        </td>
                                        <td class="text-end">
                                            {{ number_format($item['kebun']['pupuk_tunggal']['tahun']['realisasi']) }}
                                        </td>
                                        <td class="text-end">
                                            {{ $item['kebun']['pupuk_tunggal']['tahun']['persentase'] }}%</td>
                                    </tr>
                                    <!-- Pupuk Majemuk -->
                                    <tr class="{{ $isSpecialRegion ? 'table-success' : '' }}">
                                        <td>
                                            @if ($isSpecialRegion)
                                                Pupuk Majemuk
                                            @else
                                                <a href="#"
                                                    class="text-decoration-underline detail-link text-black"
                                                    data-bs-toggle="modal" data-bs-target="#detailModal"
                                                    data-regional="{{ $item['regional'] }}" data-jenis="majemuk"
                                                    data-tahun="{{ $data['tahun'] }}"
                                                    data-bulan-tanam="{{ $data['bulan_tanam'] }}"
                                                    data-tahun-tanam="{{ $data['tahun_tanam'] }}"
                                                    data-bahan-tanam="{{ $data['bahan_tanam'] }}"
                                                    data-jenis-pupuk="{{ $data['jenis_pupuk'] }}">
                                                    Pupuk Majemuk
                                                </a>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            {{ number_format($item['kebun']['pupuk_majemuk']['semester_1']['rencana']) }}
                                        </td>
                                        <td class="text-end">
                                            {{ number_format($item['kebun']['pupuk_majemuk']['semester_1']['realisasi']) }}
                                        </td>
                                        <td class="text-end">
                                            {{ $item['kebun']['pupuk_majemuk']['semester_1']['persentase'] }}%</td>
                                        <td class="text-end">
                                            {{ number_format($item['kebun']['pupuk_majemuk']['semester_2']['rencana']) }}
                                        </td>
                                        <td class="text-end">
                                            {{ number_format($item['kebun']['pupuk_majemuk']['semester_2']['realisasi']) }}
                                        </td>
                                        <td class="text-end">
                                            {{ $item['kebun']['pupuk_majemuk']['semester_2']['persentase'] }}%</td>
                                        <td class="text-end">
                                            {{ number_format($item['kebun']['pupuk_majemuk']['tahun']['rencana']) }}
                                        </td>
                                        <td class="text-end">
                                            {{ number_format($item['kebun']['pupuk_majemuk']['tahun']['realisasi']) }}
                                        </td>
                                        <td class="text-end">
                                            {{ $item['kebun']['pupuk_majemuk']['tahun']['persentase'] }}%</td>
                                    </tr>
                                    <!-- Jumlah -->
                                    <tr class="{{ $isSpecialRegion ? 'table-success' : 'table-primary' }}">
                                        <td class="fw-bold">Jumlah</td>
                                        <td class="text-end fw-bold">
                                            {{ number_format($item['kebun']['jumlah']['semester_1']['rencana']) }}</td>
                                        <td class="text-end fw-bold">
                                            {{ number_format($item['kebun']['jumlah']['semester_1']['realisasi']) }}
                                        </td>
                                        <td class="text-end fw-bold">
                                            {{ $item['kebun']['jumlah']['semester_1']['persentase'] }}%</td>
                                        <td class="text-end fw-bold">
                                            {{ number_format($item['kebun']['jumlah']['semester_2']['rencana']) }}</td>
                                        <td class="text-end fw-bold">
                                            {{ number_format($item['kebun']['jumlah']['semester_2']['realisasi']) }}
                                        </td>
                                        <td class="text-end fw-bold">
                                            {{ $item['kebun']['jumlah']['semester_2']['persentase'] }}%</td>
                                        <td class="text-end fw-bold">
                                            {{ number_format($item['kebun']['jumlah']['tahun']['rencana']) }}</td>
                                        <td class="text-end fw-bold">
                                            {{ number_format($item['kebun']['jumlah']['tahun']['realisasi']) }}</td>
                                        <td class="text-end fw-bold">
                                            {{ $item['kebun']['jumlah']['tahun']['persentase'] }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Detail Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalTitle">Detail Kebun</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="detailModalBody">
                    <div class="text-center mb-3 loading-spinner">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div class="table-responsive table-container" style="display: none;">
                        <table id="detailTable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center align-middle">Kebun</th>
                                    <th class="text-center">Semester I Rencana (Kg)</th>
                                    <th class="text-center">Semester I Realisasi (Kg)</th>
                                    <th class="text-center">% Real Thdp Renc</th>
                                    <th class="text-center">Semester II Rencana (Kg)</th>
                                    <th class="text-center">Semester II Realisasi (Kg)</th>
                                    <th class="text-center">% Real Thdp Renc</th>
                                    <th class="text-center">Tahun Rencana (Kg)</th>
                                    <th class="text-center">Tahun Realisasi (Kg)</th>
                                    <th class="text-center">% Real Thdp Renc</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Afdeling Modal -->
    <div class="modal fade" id="afdelingModal" tabindex="-1" aria-labelledby="afdelingModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="afdelingModalTitle">Detail Afdeling</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="afdelingModalBody">
                    <div class="text-center mb-3 afdeling-loading-spinner">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div class="table-responsive afdeling-table-container" style="display: none;">
                        <table id="afdelingTable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center align-middle">Afdeling</th>
                                    <th class="text-center">Semester I Rencana (Kg)</th>
                                    <th class="text-center">Semester I Realisasi (Kg)</th>
                                    <th class="text-center">% Real Thdp Renc</th>
                                    <th class="text-center">Semester II Rencana (Kg)</th>
                                    <th class="text-center">Semester II Realisasi (Kg)</th>
                                    <th class="text-center">% Real Thdp Renc</th>
                                    <th class="text-center">Tahun Rencana (Kg)</th>
                                    <th class="text-center">Tahun Realisasi (Kg)</th>
                                    <th class="text-center">% Real Thdp Renc</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        let detailModal = null;
        let detailTable = null;
        let afdelingModal = null;
        let afdelingTable = null;
        let currentRegional = '';
        let currentJenis = '';
        let currentTahun = '';
        let currentBulanTanam = '';
        let currentTahunTanam = '';
        let currentBahanTanam = '';
        let currentJenisPupuk = '';

        document.addEventListener('DOMContentLoaded', function() {
            const detailLinks = document.querySelectorAll('.detail-link');

            // Function to clean up modal
            function cleanupModal(modalElement, tableSelector) {
                try {
                    $(modalElement).find('[data-bs-dismiss="modal"]').off('click');
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open');
                    $('body').css('overflow', '');
                    $('body').css('padding-right', '');
                } catch (error) {
                    console.error('Error cleaning up modal:', error);
                }
            }

            $('#detailModal').on('hidden.bs.modal', function() {
                cleanupModal(this, '#detailTable');
            });

            $('#afdelingModal').on('hidden.bs.modal', function() {
                cleanupModal(this, '#afdelingTable');
            });

            window.showAfdelingDetails = function(kebunCode, kebunName) {
                if (!kebunCode || kebunCode === '' || kebunCode === 'undefined' || kebunCode === 'null') {
                    alert('Kode kebun tidak valid');
                    return;
                }

                document.getElementById('afdelingModalTitle').textContent =
                    `Detail Afdeling - ${kebunName} (Pupuk ${currentJenis === 'tunggal' ? 'Tunggal' : 'Majemuk'})`;

                document.querySelector('.afdeling-loading-spinner').style.display = 'block';
                document.querySelector('.afdeling-table-container').style.display = 'none';

                if (afdelingModal) {
                    afdelingModal.hide();
                    afdelingModal.dispose();
                }

                var modalElement = document.getElementById('afdelingModal');
                afdelingModal = new bootstrap.Modal(modalElement);
                afdelingModal.show();

                fetch(`/dashboard-tbm-afdeling/${currentRegional}/${kebunCode}/${currentJenis}?tahun=${currentTahun}` +
                        `${currentBulanTanam ? '&bulan_tanam=' + currentBulanTanam : ''}` +
                        `${currentTahunTanam ? '&tahun_tanam=' + currentTahunTanam : ''}` +
                        `${currentBahanTanam ? '&bahan_tanam=' + encodeURIComponent(currentBahanTanam) : ''}` +
                        `${currentJenisPupuk ? '&jenis_pupuk=' + encodeURIComponent(currentJenisPupuk) : ''}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        document.querySelector('.afdeling-loading-spinner').style.display = 'none';
                        document.querySelector('.afdeling-table-container').style.display = 'block';

                        let tableHtml = '<table id="afdelingTable" class="table table-bordered">';
                        tableHtml += `
                        <thead>
                            <tr>
                                <th class="text-center align-middle">Afdeling</th>
                                <th class="text-center">Semester I Rencana (Kg)</th>
                                <th class="text-center">Semester I Realisasi (Kg)</th>
                                <th class="text-center">% Real Thdp Renc</th>
                                <th class="text-center">Semester II Rencana (Kg)</th>
                                <th class="text-center">Semester II Realisasi (Kg)</th>
                                <th class="text-center">% Real Thdp Renc</th>
                                <th class="text-center">Tahun Rencana (Kg)</th>
                                <th class="text-center">Tahun Realisasi (Kg)</th>
                                <th class="text-center">% Real Thdp Renc</th>
                            </tr>
                        </thead>
                        <tbody>
                    `;

                        if (data.length === 0) {
                            tableHtml += `
                            <tr>
                                <td colspan="10" class="text-center">Tidak ada data afdeling untuk kebun ini</td>
                            </tr>
                        `;
                        } else {
                            data.forEach(item => {
                                tableHtml += `
                                <tr>
                                    <td class="text-start">${item.afdeling || '-'}</td>
                                    <td class="text-end">${new Intl.NumberFormat('id-ID').format(item.semester_1_rencana)}</td>
                                    <td class="text-end">${new Intl.NumberFormat('id-ID').format(item.semester_1_realisasi)}</td>
                                    <td class="text-end">${item.semester_1_persentase}%</td>
                                    <td class="text-end">${new Intl.NumberFormat('id-ID').format(item.semester_2_rencana)}</td>
                                    <td class="text-end">${new Intl.NumberFormat('id-ID').format(item.semester_2_realisasi)}</td>
                                    <td class="text-end">${item.semester_2_persentase}%</td>
                                    <td class="text-end">${new Intl.NumberFormat('id-ID').format(item.tahun_rencana)}</td>
                                    <td class="text-end">${new Intl.NumberFormat('id-ID').format(item.tahun_realisasi)}</td>
                                    <td class="text-end">${item.tahun_persentase}%</td>
                                </tr>
                            `;
                            });
                        }

                        tableHtml += '</tbody></table>';

                        document.querySelector('.afdeling-table-container').innerHTML = tableHtml;

                        setTimeout(function() {
                            try {
                                $('#afdelingTable').DataTable({
                                    responsive: true,
                                    pageLength: 10,
                                    language: {
                                        url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
                                    }
                                });
                            } catch (error) {
                                console.error('Error initializing afdeling DataTable:', error);
                            }
                        }, 100);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        document.querySelector('.afdeling-loading-spinner').style.display = 'none';
                        document.querySelector('.afdeling-table-container').style.display = 'block';
                        document.querySelector('.afdeling-table-container').innerHTML =
                            '<div class="alert alert-danger">Terjadi kesalahan saat memuat data afdeling. Silakan coba lagi.</div>';
                    });
            };

            detailLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();

                    currentRegional = this.getAttribute('data-regional');
                    currentJenis = this.getAttribute('data-jenis');
                    currentTahun = this.getAttribute('data-tahun');
                    currentBulanTanam = this.getAttribute('data-bulan-tanam');
                    currentTahunTanam = this.getAttribute('data-tahun-tanam');
                    currentBahanTanam = this.getAttribute('data-bahan-tanam');
                    currentJenisPupuk = this.getAttribute('data-jenis-pupuk');

                    document.getElementById('detailModalTitle').textContent =
                        `Detail Kebun ${currentRegional} - Pupuk ${currentJenis === 'tunggal' ? 'Tunggal' : 'Majemuk'} Tahun ${currentTahun}`;

                    document.querySelector('.loading-spinner').style.display = 'block';
                    document.querySelector('.table-container').style.display = 'none';

                    if (detailModal) {
                        detailModal.hide();
                        detailModal.dispose();
                    }

                    var modalElement = document.getElementById('detailModal');
                    detailModal = new bootstrap.Modal(modalElement);
                    detailModal.show();

                    fetch(`/dashboard-tbm-detail/${currentRegional}/${currentJenis}?tahun=${currentTahun}` +
                            `${currentBulanTanam ? '&bulan_tanam=' + currentBulanTanam : ''}` +
                            `${currentTahunTanam ? '&tahun_tanam=' + currentTahunTanam : ''}` +
                            `${currentBahanTanam ? '&bahan_tanam=' + encodeURIComponent(currentBahanTanam) : ''}` +
                            `${currentJenisPupuk ? '&jenis_pupuk=' + encodeURIComponent(currentJenisPupuk) : ''}`
                        )
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            document.querySelector('.loading-spinner').style.display = 'none';
                            document.querySelector('.table-container').style.display = 'block';

                            let tableHtml =
                                '<table id="detailTable" class="table table-bordered">';
                            tableHtml += `
                            <thead>
                                <tr>
                                    <th class="text-center align-middle">Kebun</th>
                                    <th class="text-center">Semester I Rencana (Kg)</th>
                                    <th class="text-center">Semester I Realisasi (Kg)</th>
                                    <th class="text-center">% Real Thdp Renc</th>
                                    <th class="text-center">Semester II Rencana (Kg)</th>
                                    <th class="text-center">Semester II Realisasi (Kg)</th>
                                    <th class="text-center">% Real Thdp Renc</th>
                                    <th class="text-center">Tahun Rencana (Kg)</th>
                                    <th class="text-center">Tahun Realisasi (Kg)</th>
                                    <th class="text-center">% Real Thdp Renc</th>
                                </tr>
                            </thead>
                            <tbody>
                        `;

                            if (data.length === 0) {
                                tableHtml += `
                                <tr>
                                    <td colspan="10" class="text-center">Tidak ada data kebun untuk regional ini</td>
                                </tr>
                            `;
                            } else {
                                data.forEach(item => {
                                    tableHtml += `
                                    <tr>
                                        <td class="text-start">
                                            <a href="#" class="text-decoration-underline text-black"
                                               onclick="showAfdelingDetails('${item.kebun || ''}', '${item.nama_kebun || ''}'); return false;">
                                                ${item.nama_kebun || '-'}
                                            </a>
                                        </td>
                                        <td class="text-end">${new Intl.NumberFormat('id-ID').format(item.semester_1_rencana)}</td>
                                        <td class="text-end">${new Intl.NumberFormat('id-ID').format(item.semester_1_realisasi)}</td>
                                        <td class="text-end">${item.semester_1_persentase}%</td>
                                        <td class="text-end">${new Intl.NumberFormat('id-ID').format(item.semester_2_rencana)}</td>
                                        <td class="text-end">${new Intl.NumberFormat('id-ID').format(item.semester_2_realisasi)}</td>
                                        <td class="text-end">${item.semester_2_persentase}%</td>
                                        <td class="text-end">${new Intl.NumberFormat('id-ID').format(item.tahun_rencana)}</td>
                                        <td class="text-end">${new Intl.NumberFormat('id-ID').format(item.tahun_realisasi)}</td>
                                        <td class="text-end">${item.tahun_persentase}%</td>
                                    </tr>
                                `;
                                });
                            }

                            tableHtml += '</tbody></table>';

                            document.querySelector('.table-container').innerHTML = tableHtml;

                            setTimeout(function() {
                                try {
                                    $('#detailTable').DataTable({
                                        responsive: true,
                                        pageLength: 10,
                                        language: {
                                            url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
                                        }
                                    });
                                } catch (error) {
                                    console.error(
                                        'Error initializing detail DataTable:',
                                        error);
                                }
                            }, 100);
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            document.querySelector('.loading-spinner').style.display = 'none';
                            document.querySelector('.table-container').style.display = 'block';
                            document.querySelector('.table-container').innerHTML =
                                '<div class="alert alert-danger">Terjadi kesalahan saat memuat data kebun. Silakan coba lagi.</div>';
                        });
                });
            });

            $('#detailModal .btn-secondary, #detailModal .btn-close').on('click', function() {
                if (detailModal) {
                    detailModal.hide();
                }
                cleanupModal(document.getElementById('detailModal'), '#detailTable');
            });

            $('#afdelingModal .btn-secondary, #afdelingModal .btn-close').on('click', function() {
                if (afdelingModal) {
                    afdelingModal.hide();
                }
                cleanupModal(document.getElementById('afdelingModal'), '#afdelingTable');
            });
        });
    </script>
</x-app-layout>
