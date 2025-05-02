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
                            <h4 class="card-title">Input Rencana Pemupukan</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <p>Halaman ini digunakan untuk merencanakan aktivitas pemupukan. Silakan lengkapi semua
                            informasi yang diperlukan untuk memastikan data yang akurat dan lengkap.</p>
                        <form class="row g-3 needs-validation" novalidate>

                            <input type="hidden" name="id" value="{{ $data->id ?? '' }}" id="id">
                            <div class="col-md-6">
                                <label for="regional" class="form-label">Regional</label>
                                <select class="form-select" id="regional" required name="regional"
                                    style="background-color: green; color: white;">
                                    <option selected disabled value="">Pilih Regional...</option>
                                    @foreach ($regions as $region)
                                        <option value="{{ $region }}"
                                            {{ old('regional', $data->regional ?? '') == $region ? 'selected' : '' }}>
                                            {{ $region }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">
                                    Please choose a regional.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="kebun" class="form-label">Kebun</label>
                                <select class="form-select" id="kebun" required name="kebun"
                                    style="background-color: green; color: white;">
                                    <option selected disabled value="">Pilih Kebun...</option>
                                    @if (old('kebun', $data->kebun ?? ''))
                                        <option value="{{ old('kebun', $data->kebun ?? '') }}" selected>
                                            {{ old('kebun', $data->kebun ?? '') }}</option>
                                    @endif
                                </select>
                                <div class="invalid-feedback">
                                    Please choose a kebun.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="afdeling" class="form-label">Afdeling</label>
                                <select class="form-select" id="afdeling" required name="afdeling"
                                    style="background-color: green; color: white;">
                                    <option selected disabled value="">Pilih Afdeling...</option>
                                    @if (old('afdeling', $data->afdeling ?? ''))
                                        <option value="{{ old('afdeling', $data->afdeling ?? '') }}" selected>
                                            {{ old('afdeling', $data->afdeling ?? '') }}</option>
                                    @endif
                                </select>
                                <div class="invalid-feedback">
                                    Please choose an afdeling.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="blok" class="form-label">Blok</label>
                                <select class="form-select" id="blok" required name="blok"
                                    style="background-color: green; color: white;">
                                    <option selected disabled value="">Pilih Blok...</option>
                                    @if (old('blok', $data->blok ?? ''))
                                        <option value="{{ old('blok', $data->blok ?? '') }}" selected>
                                            {{ old('blok', $data->blok ?? '') }}</option>
                                    @endif
                                </select>
                                <div class="invalid-feedback">
                                    Please choose a blok.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="tahun_tanam" class="form-label">Tahun Tanam</label>
                                <input type="text" class="form-control" id="tahun_tanam" name="tahun_tanam" readonly
                                    value="{{ old('tahun_tanam', $data->tahun_tanam ?? '') }}">
                                <div class="valid-feedback">
                                    Looks good!
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="luas_blok" class="form-label">Luas Blok (Ha)</label>
                                <input type="text" class="form-control" id="luas_blok" name="luas_blok" readonly
                                    value="{{ old('luas_blok', $data->luas_blok ?? '') }}">
                                <div class="valid-feedback">
                                    Looks good!
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="jumlah_pokok" class="form-label">Jumlah Pokok</label>
                                <input type="text" class="form-control" id="jumlah_pokok" name="jumlah_pokok"
                                    readonly value="{{ old('jumlah_pokok', $data->jumlah_pokok ?? '') }}">
                                <div class="valid-feedback">
                                    Looks good!
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="jenis_pupuk" class="form-label">Jenis Pupuk</label>
                                <select class="form-select" id="jenis_pupuk" required name="jenis_pupuk"
                                    style="background-color: green; color: white;">
                                    <option selected disabled value="">Pilih Jenis Pupuk...</option>
                                    @foreach ($jenisPupuk as $jp)
                                        <option value="{{ $jp->id }}"
                                            {{ old('jenis_pupuk', $data->id_pupuk ?? '') == $jp->id ? 'selected' : '' }}>
                                            {{ $jp->nama_pupuk . ' - ' . $jp->jenis_pupuk }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">
                                    Please choose a jenis pupuk.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="jumlah_pupuk" class="form-label">Jumlah Pupuk (KG)</label>
                                <input type="number" class="form-control" id="jumlah_pupuk" required
                                    name="jumlah_pupuk" style="background-color: green; color: white;"
                                    value="{{ old('jumlah_pupuk', $data->jumlah_pupuk ?? '') }}">
                                <div class="invalid-feedback">
                                    Please provide a valid jumlah pupuk.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="luas_pemupukan" class="form-label">Luas Pemupukan (HA)</label>
                                <input type="number" class="form-control" id="luas_pemupukan" required
                                    name="luas_pemupukan" style="background-color: green; color: white;"
                                    value="{{ old('luas_pemupukan', $data->luas_pemupukan ?? '') }}">
                                <div class="invalid-feedback">
                                    Please provide a valid luas pemupukan.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="semester_pemupukan" class="form-label">Semester Pemupukan</label>
                                <select class="form-select" id="semester_pemupukan" required
                                    name="semester_pemupukan" style="background-color: green; color: white;">
                                    <option selected disabled value="">Pilih Semester...</option>
                                    <option value="1"
                                        {{ old('semester_pemupukan', $data->semester_pemupukan ?? '') == '1' ? 'selected' : '' }}>
                                        Semester 1</option>
                                    <option value="2"
                                        {{ old('semester_pemupukan', $data->semester_pemupukan ?? '') == '2' ? 'selected' : '' }}>
                                        Semester 2</option>
                                </select>
                                <div class="invalid-feedback">
                                    Please provide a valid semester pemupukan.
                                </div>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary form-control" id="submit-data" type="button">Simpan
                                    Data Rencana Pemupukan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
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
                    document.getElementById('tahun_tanam').value = data.tahun_tanam;
                    document.getElementById('luas_blok').value = data.luas;
                    document.getElementById('jumlah_pokok').value = data.jlh_pokok;
                });
        });

        document.querySelector('#submit-data').addEventListener('click', function() {
            event.preventDefault(); // Prevent default form submission

            const form = document.querySelector('form');
            const formData = new FormData(form);

            const id = document.querySelector('input[name="id"]').value; // Get the ID from the hidden input

            // const pathArray = window.location.pathname.split('/'); // Split the URL by '/'
            // const id = pathArray[pathArray.length - 1]; // Get the last segment of the URL, which is the ID

            console.log(id);
            // Get the values from the form fields manually using their IDs
            const regional = document.getElementById('regional').value;
            const kebun = document.getElementById('kebun').value;
            const afdeling = document.getElementById('afdeling').value;
            const blok = document.getElementById('blok').value;
            const jenis_pupuk = document.getElementById('jenis_pupuk').value;
            const jumlah_pupuk = document.getElementById('jumlah_pupuk').value;
            const luas_pemupukan = document.getElementById('luas_pemupukan').value;
            const semester_pemupukan = document.getElementById('semester_pemupukan').value;
            const tahun_tanam = document.getElementById('tahun_tanam').value;
            const luas_blok = document.getElementById('luas_blok').value;
            const jumlah_pokok = document.getElementById('jumlah_pokok').value;



            // Spoof PUT by adding _method field
            formData.append('_method', 'PUT');
            // Append the additional manually collected values to the FormData object
            formData.append('regional', regional);
            formData.append('kebun', kebun);
            formData.append('afdeling', afdeling);
            formData.append('blok', blok);
            formData.append('jenis_pupuk', jenis_pupuk);
            formData.append('jumlah_pupuk', jumlah_pupuk);
            formData.append('luas_pemupukan', luas_pemupukan);
            formData.append('semester_pemupukan', semester_pemupukan);
            formData.append('tahun_tanam', tahun_tanam);
            formData.append('luas_blok', luas_blok);
            formData.append('jumlah_pokok', jumlah_pokok);

            const url = id ? '{{ route('rencana-pemupukan.update', ['rencana_pemupukan' => ':id']) }}'.replace(
                ':id', id) : '{{ route('rencana-pemupukan.store') }}';


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
                            form.reset();
                        });
                    } else {
                        Swal.fire({
                            title: 'Failed!',
                            text: data.message || 'Data failed to save.',
                            icon: 'error',
                            confirmButtonText: 'OK',
                        });
                    }
                })
                .catch(error => {
                    const messages = error.errors ?
                        Object.values(error.errors).flat().join('\n') :
                        'An error occurred. Please check your input.';
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
