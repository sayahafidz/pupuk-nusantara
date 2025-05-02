<x-app-layout :assets="$assets ?? []">
    <div>
        <div class="row">
            <div class="col-sm-12 col-lg-12">
                <div class="bd-example mb-3">
                    <div class="alert alert-success mb-0" role="alert">
                        <h4 class="alert-heading">Informasi!</h4>
                        <p>Hanya isi input dengan warna hijau.</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Input Pemupukan Harian</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <p>Halaman ini digunakan untuk mencatat aktivitas pemupukan harian di lapangan. Silakan lengkapi
                            semua informasi yang diperlukan untuk memastikan data yang akurat dan lengkap. Data ini akan
                            membantu dalam pemantauan pemupukan dan perawatan tanaman secara efektif.</p>
                        <form class="row g-3 needs-validation" novalidate>
                            <!-- Hidden input for ID (if editing) -->
                            <input type="hidden" name="id" value="{{ $data->id ?? '' }}">

                            <div class="col-md-6">
                                <label for="validationCustom01" class="form-label">Regional</label>
                                <select class="form-select" id="regional" required name="regional"
                                    style="background-color: green; color: white;">
                                    <option selected disabled value="">Pilih Regional...</option>
                                    @foreach ($regions as $region)
                                        <option value="{{ $region }}"
                                            {{ isset($data) && $data->regional == $region ? 'selected' : '' }}>
                                            {{ $region }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">
                                    Please choose a regional.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="validationCustom02" class="form-label">Kebun</label>
                                <select class="form-select" id="kebun" required name="kebun"
                                    style="background-color: green; color: white;">
                                    <option selected disabled value="">Pilih Kebun...</option>
                                    @if (isset($data))
                                        <option value="{{ $data->kebun }}" selected>{{ $data->kebun }}</option>
                                    @endif
                                </select>
                                <div class="invalid-feedback">
                                    Please choose a kebun.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="validationCustom03" class="form-label">Afdeling</label>
                                <select class="form-select" id="afdeling" required name="afdeling"
                                    style="background-color: green; color: white;">
                                    <option selected disabled value="">Pilih Afdeling...</option>
                                    @if (isset($data))
                                        <option value="{{ $data->afdeling }}" selected>{{ $data->afdeling }}</option>
                                    @endif
                                </select>
                                <div class="invalid-feedback">
                                    Please choose an afdeling.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="validationCustom04" class="form-label">Blok</label>
                                <select class="form-select" id="blok" required name="blok"
                                    style="background-color: green; color: white;">
                                    <option selected disabled value="">Pilih Blok...</option>
                                    @if (isset($data))
                                        <option value="{{ $data->blok }}" selected>{{ $data->blok }}</option>
                                    @endif
                                </select>
                                <div class="invalid-feedback">
                                    Please choose a blok.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="validationCustom05" class="form-label">Tahun Tanam</label>
                                <input type="text" class="form-control" id="validationCustom05" name="tahun_tanam"
                                    value="{{ $data->tahun_tanam ?? '' }}" readonly>
                                <div class="valid-feedback">
                                    Looks good!
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="validationCustom06" class="form-label">Luas Blok (Ha)</label>
                                <input type="text" class="form-control" id="validationCustom06" name="luas_blok"
                                    value="{{ $data->luas_blok ?? '' }}" readonly>
                                <div class="valid-feedback">
                                    Looks good!
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="validationCustom07" class="form-label">Jumlah Pokok</label>
                                <input type="text" class="form-control" id="validationCustom07" name="jumlah_pokok"
                                    value="{{ $data->jumlah_pokok ?? '' }}" readonly>
                                <div class="valid-feedback">
                                    Looks good!
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="validationCustomUsername08" class="form-label">Jenis Pupuk</label>
                                <select class="form-select" id="validationCustomUsername08" required name="jenis_pupuk"
                                    style="background-color: green; color: white;">
                                    <option selected disabled value="">Pilih Jenis Pupuk...</option>
                                    @foreach ($jenisPupuk as $jp)
                                        <option value="{{ $jp->id }}"
                                            {{ isset($data) && $data->id_pupuk == $jp->id ? 'selected' : '' }}>
                                            {{ $jp->nama_pupuk . ' - ' . $jp->jenis_pupuk }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">
                                    Please choose a username.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="validationCustom09" class="form-label">Jumlah Pemupukan (KG)</label>
                                <input type="number" class="form-control" id="validationCustom09" required
                                    name="jumlah_pupuk" value="{{ $data->jumlah_pupuk ?? '' }}"
                                    style="background-color: green; color: white;">
                                <div class="invalid-feedback">
                                    Please provide a valid jumlah pemupukan.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="validationCustom10" class="form-label">Luas Pemupukan (HA)</label>
                                <input type="number" class="form-control" id="validationCustom10" required
                                    name="luas_pemupukan" value="{{ $data->luas_pemupukan ?? '' }}"
                                    style="background-color: green; color: white;">
                                <div class="invalid-feedback">
                                    Please provide a valid luas pemupukan.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="validationCustom10" class="form-label">Tanggal Pemupukan</label>
                                <input type="date" class="form-control" id="validationCustom11" required
                                    name="tanggal_pemupukan" value="{{ $data->tanggal_pemupukan ?? date('Y-m-d') }}"
                                    style="background-color: green; color: white;">
                                <div class="invalid-feedback">
                                    Please provide a valid tanggal pemupukan.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="validationCustom12" class="form-label">Cara Pemupukan</label>
                                <select class="form-select" id="validationCustom12" required name="cara_pemupukan"
                                    style="background-color: green; color: white;">
                                    <option value="manual"
                                        {{ isset($data) && $data->cara_pemupukan == 'manual' ? 'selected' : '' }}>
                                        Manual</option>
                                    <option value="mekanisasi"
                                        {{ isset($data) && $data->cara_pemupukan == 'mekanisasi' ? 'selected' : '' }}>
                                        Mekanisasi</option>
                                </select>
                                <div class="invalid-feedback">
                                    Please provide a valid cara pemupukan.
                                </div>
                            </div>
                            <div class="col-md-6" id="jumlah-tenaga-kerja-container"
                                style="display: {{ isset($data) && $data->cara_pemupukan == 'mekanisasi' ? 'block' : 'none' }};">
                                <label for="validationCustom13" class="form-label">Jumlah Tenaga Kerja</label>
                                <input type="number" class="form-control" id="validationCustom13"
                                    name="jumlah_tenaga_kerja" min="1"
                                    value="{{ $data->jumlah_tenaga_kerja ?? '' }}"
                                    style="background-color: green; color: white;">
                                <div class="invalid-feedback">
                                    Please provide a valid jumlah tenaga kerja.
                                </div>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary form-control" id="submit-data" type="button">
                                    {{ isset($data) ? 'Update Data Pemupukan' : 'Simpan Data Pemupukan' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.getElementById('validationCustom12').addEventListener('change', function() {
            const caraPemupukan = this.value;
            const tenagaKerjaContainer = document.getElementById('jumlah-tenaga-kerja-container');
            if (caraPemupukan === 'mekanisasi') {
                tenagaKerjaContainer.style.display = 'block';
            } else {
                tenagaKerjaContainer.style.display = 'none';
            }
        });

        document.getElementById('regional').addEventListener('change', function() {
            const regional = this.value;
            fetch(`/api/kebun/${regional}`)
                .then(response => response.json())
                .then(data => {
                    let kebunOptions = '<option selected disabled value="">Pilih Kebun...</option>';
                    data.forEach(kebun => {
                        kebunOptions += `<option value="${kebun}">${kebun}</option>`;
                    });
                    document.getElementById('kebun').innerHTML = kebunOptions;
                });
        });

        document.getElementById('kebun').addEventListener('change', function() {
            const regional = document.getElementById('regional').value;
            const kebun = this.value;
            fetch(`/api/afdeling/${regional}/${kebun}`)
                .then(response => response.json())
                .then(data => {
                    let afdelingOptions = '<option selected disabled value="">Pilih Afdeling...</option>';
                    data.forEach(afdeling => {
                        afdelingOptions += `<option value="${afdeling}">${afdeling}</option>`;
                    });
                    document.getElementById('afdeling').innerHTML = afdelingOptions;
                });
        });

        document.getElementById('afdeling').addEventListener('change', function() {
            const regional = document.getElementById('regional').value;
            const kebun = document.getElementById('kebun').value;
            const afdeling = this.value;
            fetch(`/api/blok/${regional}/${kebun}/${afdeling}`)
                .then(response => response.json())
                .then(data => {
                    let blokOptions = '<option selected disabled value="">Pilih Blok...</option>';
                    data.forEach(blok => {
                        blokOptions += `<option value="${blok}">${blok}</option>`;
                    });
                    document.getElementById('blok').innerHTML = blokOptions;
                });
        });

        document.getElementById('blok').addEventListener('change', function() {
            const regional = document.getElementById('regional').value;
            const kebun = document.getElementById('kebun').value;
            const afdeling = document.getElementById('afdeling').value;
            const blok = this.value;
            fetch(`/api/detail/${regional}/${kebun}/${afdeling}/${blok}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('validationCustom05').value = data.tahun_tanam;
                    document.getElementById('validationCustom06').value = data.luas;
                    document.getElementById('validationCustom07').value = data.jlh_pokok;
                });
        });

        document.querySelector('#submit-data').addEventListener('click', function() {
            const form = document.querySelector('form');
            const formData = new FormData(form);
            const pathArray = window.location.pathname.split('/'); // Split the URL by '/'
            const id = pathArray[pathArray.length - 1]; // Get the last segment of the URL, which is the ID


            {{-- const url = id ? `/pemupukan/update/${id}` : '{{ route('pemupukan.update') }}'; --}}

            // set the data
            const regionalValue = document.querySelector('#regional').value;
            const kebunValue = document.querySelector('#kebun').value;
            const afdelingValue = document.querySelector('#afdeling').value;
            const blokValue = document.querySelector('#blok').value;
            const tahunTanamValue = document.querySelector('#validationCustom05').value;
            const luasBlokValue = document.querySelector('#validationCustom06').value;
            const jumlahPokokValue = document.querySelector('#validationCustom07').value;
            const jenisPupukValue = document.querySelector('#validationCustomUsername08').value;
            const jumlahPupukValue = document.querySelector('#validationCustom09').value;
            const luasPemupukanValue = document.querySelector('#validationCustom10').value;
            const tanggalPemupukanValue = document.querySelector('#validationCustom11').value;
            const caraPemupukan = document.querySelector('#validationCustom12').value;
            const jumlahMekanisasi = document.querySelector('#validationCustom13').value;



            // Log the values to see them
            console.log('Regional:', regionalValue);
            console.log('Kebun:', kebunValue);
            console.log('Afdeling:', afdelingValue);
            console.log('Blok:', blokValue);
            console.log('Tahun Tanam:', tahunTanamValue);
            console.log('Luas Blok:', luasBlokValue);
            console.log('Jumlah Pokok:', jumlahPokokValue);
            console.log('Jenis Pupuk:', jenisPupukValue);
            console.log('Jumlah Pemupukan:', jumlahPupukValue);
            console.log('Luas Pemupukan:', luasPemupukanValue);
            console.log('Tanggal Pemupukan:', tanggalPemupukanValue);
            console.log('Cara Pemupukan:', caraPemupukan);
            console.log('Jumlah Tenaga Kerja:', jumlahMekanisasi);

            // Append the additional manually collected values to the FormData object
            formData.append('regional', regionalValue);
            formData.append('kebun', kebunValue);
            formData.append('afdeling', afdelingValue);
            formData.append('blok', blokValue);
            formData.append('tahun_tanam', tahunTanamValue);
            formData.append('luas_blok', luasBlokValue);
            formData.append('jumlah_pokok', jumlahPokokValue);
            formData.append('jenis_pupuk', jenisPupukValue);
            formData.append('jumlah_pupuk', jumlahPupukValue);
            formData.append('luas_pemupukan', luasPemupukanValue);
            formData.append('tanggal_pemupukan', tanggalPemupukanValue);
            formData.append('cara_pemupukan', caraPemupukan);
            formData.append('jumlah_mekanisasi', jumlahMekanisasi);

            url = `/pemupukan/update/${id}`;

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: formData,
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw err;
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonText: 'OK',
                        }).then(() => {
                            window.location.href = '{{ route('rekap-pemupukan') }}';
                        });
                    } else {
                        Swal.fire({
                            title: 'Failed!',
                            text: data.message || 'Data failed to save. Please try again.',
                            icon: 'error',
                            confirmButtonText: 'OK',
                        });
                    }
                })
                .catch(error => {
                    const messages = error.errors ?
                        Object.values(error.errors).flat().join('\n') :
                        'An error occurred. Please check your input or try again later.';

                    Swal.fire({
                        title: 'Error!',
                        text: messages,
                        icon: 'error',
                        confirmButtonText: 'OK',
                    });
                });
        });
    </script>
</x-app-layout>
