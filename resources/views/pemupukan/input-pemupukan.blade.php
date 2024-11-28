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
                        <form class="row g-3 needs-validation"
                            action="{{ route('pemupukanController.storePemupukan') }}" method="POST" novalidate>
                            @csrf
                            <div class="col-md-6">
                                <label for="validationCustom01" class="form-label">Regional</label>
                                <select class="form-select" id="validationCustom01" required name="regional"
                                    style="background-color: green; color: white;">
                                    <option selected disabled value="">Pilih Regional...</option>
                                    <option value="regional1">Regional 1</option>
                                    <option value="regional2">Regional 2</option>
                                    <option value="regional3">Regional 3</option>
                                </select>
                                <div class="invalid-feedback">
                                    Please choose a regional.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="validationCustom02" class="form-label">Kebun</label>
                                <select class="form-select" id="validationCustom02" required name="kebun"
                                    style="background-color: green; color: white;">
                                    <option selected disabled value="">Pilih Kebun...</option>
                                    <option value="kebun1">Kebun 1</option>
                                    <option value="kebun2">Kebun 2</option>
                                    <option value="kebun3">Kebun 3</option>
                                </select>
                                <div class="invalid-feedback">
                                    Please choose a kebun.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="validationCustom03" class="form-label">Afdeling</label>
                                <select class="form-select" id="validationCustom03" required name="afdeling"
                                    style="background-color: green; color: white;">
                                    <option selected disabled value="">Pilih Afdeling...</option>
                                    <option value="afdeling1">Afdeling 1</option>
                                    <option value="afdeling2">Afdeling 2</option>
                                    <option value="afdeling3">Afdeling 3</option>
                                </select>
                                <div class="invalid-feedback">
                                    Please choose an afdeling.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="validationCustom04" class="form-label">Blok</label>
                                <select class="form-select" id="validationCustom04" required name="blok"
                                    style="background-color: green; color: white;">
                                    <option selected disabled value="">Pilih Blok...</option>
                                    <option value="blok1">Blok 1</option>
                                    <option value="blok2">Blok 2</option>
                                    <option value="blok3">Blok 3</option>
                                </select>
                                <div class="invalid-feedback">
                                    Please choose a blok.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="validationCustom05" class="form-label">Tahun Tanam</label>
                                <input type="text" class="form-control" id="validationCustom05" value="2009"
                                    name="tahun_tanam" readonly>
                                <div class="valid-feedback">
                                    Looks good!
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="validationCustom06" class="form-label">Luas Blok (Ha)</label>
                                <input type="text" class="form-control" id="validationCustom06" value="321"
                                    name="luas_blok" readonly>
                                <div class="valid-feedback">
                                    Looks good!
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="validationCustom07" class="form-label">Jumlah Pokok</label>
                                <input type="text" class="form-control" id="validationCustom07" value="123"
                                    name="jumlah_pokok" readonly>
                                <div class="valid-feedback">
                                    Looks good!
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="validationCustomUsername08" class="form-label">Jenis Pupuk</label>
                                <select class="form-select" id="validationCustomUsername08" required name="jenis_pupuk"
                                    style="background-color: green; color: white;">
                                    <option selected disabled value="">Pilih Jenis Pupuk...</option>
                                    <option value="user1">NPK</option>
                                    <option value="user2">Dolomite</option>
                                    <option value="user3">Urea</option>
                                </select>
                                <div class="invalid-feedback">
                                    Please choose a username.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="validationCustom09" class="form-label">Jumlah Pemupukan (KG)</label>
                                <input type="number" class="form-control" id="validationCustom09" required
                                    name="jumlah_pemupukan" style="background-color: green; color: white;"
                                    value="0">
                                <div class="invalid-feedback">
                                    Please provide a valid jumlah pemupukan.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="validationCustom10" class="form-label">Luas Pemupukan (HA)</label>
                                <input type="number" class="form-control" id="validationCustom10" required
                                    name="luas_pemupukan" style="background-color: green; color: white;"
                                    value="0">
                                <div class="invalid-feedback">
                                    Please provide a valid luas pemupukan.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="validationCustom10" class="form-label">Tanggal Pemupukan</label>
                                <input type="date" class="form-control" id="validationCustom10" required
                                    name="tanggal_pemupukan" style="background-color: green; color: white;">
                                <div class="invalid-feedback">
                                    Please provide a valid tanggal pemupukan.
                                </div>
                            </div>
                            {{-- <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="invalidCheck"
                                        required>
                                    <label class="form-check-label" for="invalidCheck">
                                        Agree to terms and conditions
                                    </label>
                                    <div class="invalid-feedback">
                                        You must agree before submitting.
                                    </div>
                                </div>
                            </div> --}}
                            <div class="col-12">
                                <button class="btn btn-primary form-control" type="submit">Simpan Data
                                    Pemupukan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
