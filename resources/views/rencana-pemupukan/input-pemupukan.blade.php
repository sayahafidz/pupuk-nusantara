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
                            <div class="col-md-6">
                                <label for="validationCustom01" class="form-label">Regional</label>
                                <select class="form-select" id="regional" required name="regional"
                                    style="background-color: green; color: white;">
                                    <option selected disabled value="">Pilih Regional...</option>
                                    @foreach ($regions as $region)
                                        <option value="{{ $region }}">{{ $region }}</option>
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
                                </select>
                                <div class="invalid-feedback">
                                    Please choose a blok.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="validationCustom05" class="form-label">Tahun Tanam</label>
                                <input type="text" class="form-control" id="validationCustom05" name="tahun_tanam"
                                    readonly>
                                <div class="valid-feedback">
                                    Looks good!
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="validationCustom06" class="form-label">Luas Blok (Ha)</label>
                                <input type="text" class="form-control" id="validationCustom06" name="luas_blok"
                                    readonly>
                                <div class="valid-feedback">
                                    Looks good!
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="validationCustom07" class="form-label">Jumlah Pokok</label>
                                <input type="text" class="form-control" id="validationCustom07" name="jumlah_pokok"
                                    readonly>
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
                                        <option value="{{ $jp->id }}">
                                            {{ $jp->nama_pupuk . ' - ' . $jp->jenis_pupuk }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">
                                    Please choose a username.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="validationCustom09" class="form-label">Jumlah Pemupukan (KG)</label>
                                <input type="number" class="form-control" id="validationCustom09" required
                                    name="jumlah_pemupukan" style="background-color: green; color: white;">
                                <div class="invalid-feedback">
                                    Please provide a valid jumlah pemupukan.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="semesterpemupukan" class="form-label">Semester Pemupukan</label>
                                <select class="form-select" id="semesterpemupukan" required name="semester_pemupukan"
                                    style="background-color: green; color: white;">
                                    <option selected disabled value="">Pilih Semester</option>
                                    <option value="1">Semester 1</option>
                                    <option value="2">Semester 2</option>
                                </select>
                                <div class="invalid-feedback">
                                    Please choose a username.
                                </div>
                            </div>

                            <div class="col-12">
                                <button class="btn btn-primary form-control" id="submit-data" type="button">Simpan
                                    Data
                                    Pemupukan</button>
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
                    document.getElementById('validationCustom05').value = data.tahun_tanam;
                    document.getElementById('validationCustom06').value = data.luas;
                    document.getElementById('validationCustom07').value = data.jlh_pokok;
                });
        });


        document.querySelector('#submit-data').addEventListener('click', function() {
            const form = document.querySelector('form'); // Get the form element
            const formData = new FormData(form); // Collect form data

            // Get the values from the form fields manually using their IDs
            const regionalValue = document.querySelector('#regional').value;
            const kebunValue = document.querySelector('#kebun').value;
            const afdelingValue = document.querySelector('#afdeling').value;
            const blokValue = document.querySelector('#blok').value;
            const tahunTanamValue = document.querySelector('#validationCustom05').value;
            const luasBlokValue = document.querySelector('#validationCustom06').value;
            const jumlahPokokValue = document.querySelector('#validationCustom07').value;
            const jenisPupukValue = document.querySelector('#validationCustomUsername08').value;
            const jumlahPemupukanValue = document.querySelector('#validationCustom09').value;
            const luasPemupukanValue = document.querySelector('#validationCustom10').value;
            const tanggalPemupukanValue = document.querySelector('#validationCustom11').value;

            // Log the values to see them
            console.log('Regional:', regionalValue);
            console.log('Kebun:', kebunValue);
            console.log('Afdeling:', afdelingValue);
            console.log('Blok:', blokValue);
            console.log('Tahun Tanam:', tahunTanamValue);
            console.log('Luas Blok:', luasBlokValue);
            console.log('Jumlah Pokok:', jumlahPokokValue);
            console.log('Jenis Pupuk:', jenisPupukValue);
            console.log('Jumlah Pemupukan:', jumlahPemupukanValue);
            console.log('Luas Pemupukan:', luasPemupukanValue);
            console.log('Tanggal Pemupukan:', tanggalPemupukanValue);

            // Append the additional manually collected values to the FormData object
            formData.append('regional', regionalValue);
            formData.append('kebun', kebunValue);
            formData.append('afdeling', afdelingValue);
            formData.append('blok', blokValue);
            formData.append('tahun_tanam', tahunTanamValue);
            formData.append('luas_blok', luasBlokValue);
            formData.append('jumlah_pokok', jumlahPokokValue);
            formData.append('jenis_pupuk', jenisPupukValue);
            formData.append('jumlah_pemupukan', jumlahPemupukanValue);
            formData.append('luas_pemupukan', luasPemupukanValue);
            formData.append('tanggal_pemupukan', tanggalPemupukanValue);

            fetch('{{ route('pemupukan.store') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}', // CSRF token for Laravel
                        'Accept': 'application/json',
                    },
                    body: formData, // Send form data
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw err;
                        }); // Parse error response
                    }
                    return response.json(); // Parse success response
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonText: 'OK',
                        }).then(() => {
                            // Reset form after successful submission
                            form.reset();
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
