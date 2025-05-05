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
                                            @foreach($data['tahun_list'] as $tahunOption)
                                                <option value="{{ $tahunOption }}" {{ $data['tahun'] == $tahunOption ? 'selected' : '' }}>{{ $tahunOption }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2 mb-2">
                                    <div class="form-group">
                                        <label class="form-label">Bulan Tanam</label>
                                        <select name="bulan_tanam" class="form-select">
                                            <option value="">Semua</option>
                                            @for($m = 1; $m <= 12; $m++)
                                                <option value="{{ $m }}" {{ $data['bulan_tanam'] == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2 mb-2">
                                    <div class="form-group">
                                        <label class="form-label">Tahun Tanam</label>
                                        <select name="tahun_tanam" class="form-select">
                                            <option value="">Semua</option>
                                            @foreach($data['tahun_tanam_list'] as $tahunTanam)
                                                <option value="{{ $tahunTanam }}" {{ $data['tahun_tanam'] == $tahunTanam ? 'selected' : '' }}>{{ $tahunTanam }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2 mb-2">
                                    <div class="form-group">
                                        <label class="form-label">Bahan Tanam</label>
                                        <select name="bahan_tanam" class="form-select">
                                            <option value="">Semua</option>
                                            @foreach($data['bahan_tanam_list'] as $bahanTanam)
                                                <option value="{{ $bahanTanam }}" {{ $data['bahan_tanam'] == $bahanTanam ? 'selected' : '' }}>{{ $bahanTanam }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2 mb-2">
                                    <div class="form-group">
                                        <label class="form-label">Jenis Pupuk</label>
                                        <select name="jenis_pupuk" class="form-select">
                                            <option value="">Semua</option>
                                            @foreach($data['jenis_pupuk_list'] as $jenisPupuk)
                                                <option value="{{ $jenisPupuk }}" {{ $data['jenis_pupuk'] == $jenisPupuk ? 'selected' : '' }}>{{ $jenisPupuk }}</option>
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
                                @foreach($data['hasil'] as $item)
                                    @php
                                        $isSpecialRegion = in_array($item['regional'], ['Palm Co Regional 1 + KSO', 'Palm Co Regional 2 + KSO', 'Total Palm Co', 'Total Regional KSO', 'HOLDING']);
                                    @endphp

                                    <!-- Pupuk Tunggal -->
                                    <tr class="{{ $isSpecialRegion ? 'table-success' : '' }}">
                                        @if($isSpecialRegion)
                                            <td rowspan="3" class="align-middle text-center fw-bold bg-success text-white">
                                                {{ $item['regional'] }}
                                            </td>
                                        @else
                                            <td rowspan="3" class="align-middle text-center">
                                                {{ $item['regional'] }}
                                            </td>
                                        @endif
                                        <td>Pupuk Tunggal</td>
                                        <td class="text-end">{{ number_format($item['kebun']['pupuk_tunggal']['semester_1']['rencana']) }}</td>
                                        <td class="text-end">{{ number_format($item['kebun']['pupuk_tunggal']['semester_1']['realisasi']) }}</td>
                                        <td class="text-end">{{ $item['kebun']['pupuk_tunggal']['semester_1']['persentase'] }}%</td>
                                        <td class="text-end">{{ number_format($item['kebun']['pupuk_tunggal']['semester_2']['rencana']) }}</td>
                                        <td class="text-end">{{ number_format($item['kebun']['pupuk_tunggal']['semester_2']['realisasi']) }}</td>
                                        <td class="text-end">{{ $item['kebun']['pupuk_tunggal']['semester_2']['persentase'] }}%</td>
                                        <td class="text-end">{{ number_format($item['kebun']['pupuk_tunggal']['tahun']['rencana']) }}</td>
                                        <td class="text-end">{{ number_format($item['kebun']['pupuk_tunggal']['tahun']['realisasi']) }}</td>
                                        <td class="text-end">{{ $item['kebun']['pupuk_tunggal']['tahun']['persentase'] }}%</td>
                                    </tr>

                                    <!-- Pupuk Majemuk -->
                                    <tr class="{{ $isSpecialRegion ? 'table-success' : '' }}">
                                        <td>Pupuk Majemuk</td>
                                        <td class="text-end">{{ number_format($item['kebun']['pupuk_majemuk']['semester_1']['rencana']) }}</td>
                                        <td class="text-end">{{ number_format($item['kebun']['pupuk_majemuk']['semester_1']['realisasi']) }}</td>
                                        <td class="text-end">{{ $item['kebun']['pupuk_majemuk']['semester_1']['persentase'] }}%</td>
                                        <td class="text-end">{{ number_format($item['kebun']['pupuk_majemuk']['semester_2']['rencana']) }}</td>
                                        <td class="text-end">{{ number_format($item['kebun']['pupuk_majemuk']['semester_2']['realisasi']) }}</td>
                                        <td class="text-end">{{ $item['kebun']['pupuk_majemuk']['semester_2']['persentase'] }}%</td>
                                        <td class="text-end">{{ number_format($item['kebun']['pupuk_majemuk']['tahun']['rencana']) }}</td>
                                        <td class="text-end">{{ number_format($item['kebun']['pupuk_majemuk']['tahun']['realisasi']) }}</td>
                                        <td class="text-end">{{ $item['kebun']['pupuk_majemuk']['tahun']['persentase'] }}%</td>
                                    </tr>

                                    <!-- Jumlah -->
                                    <tr class="{{ $isSpecialRegion ? 'table-success' : 'table-primary' }}">
                                        <td class="fw-bold">Jumlah</td>
                                        <td class="text-end fw-bold">{{ number_format($item['kebun']['jumlah']['semester_1']['rencana']) }}</td>
                                        <td class="text-end fw-bold">{{ number_format($item['kebun']['jumlah']['semester_1']['realisasi']) }}</td>
                                        <td class="text-end fw-bold">{{ $item['kebun']['jumlah']['semester_1']['persentase'] }}%</td>
                                        <td class="text-end fw-bold">{{ number_format($item['kebun']['jumlah']['semester_2']['rencana']) }}</td>
                                        <td class="text-end fw-bold">{{ number_format($item['kebun']['jumlah']['semester_2']['realisasi']) }}</td>
                                        <td class="text-end fw-bold">{{ $item['kebun']['jumlah']['semester_2']['persentase'] }}%</td>
                                        <td class="text-end fw-bold">{{ number_format($item['kebun']['jumlah']['tahun']['rencana']) }}</td>
                                        <td class="text-end fw-bold">{{ number_format($item['kebun']['jumlah']['tahun']['realisasi']) }}</td>
                                        <td class="text-end fw-bold">{{ $item['kebun']['jumlah']['tahun']['persentase'] }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
