<x-app-layout :assets="$assets ?? []">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Rekap Pemupukan Tabel</h4>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Dropdown Filters in a single row -->
                    <div class="d-flex mb-3 align-items-center">
                        <div class="me-3">
                            <label for="regionalSelect" class="form-label">Regional:</label>
                            <select id="regionalSelect" class="form-select">
                                <option value="">All</option>
                                <option value="Tiger Nixon">Tiger Nixon</option>
                                <!-- Add other options dynamically if needed -->
                            </select>
                        </div>

                        <div class="me-3">
                            <label for="kebunSelect" class="form-label">Kebun:</label>
                            <select id="kebunSelect" class="form-select" disabled>
                                <option value="">All</option>
                                <option value="System Architect">System Architect</option>
                                <!-- Add other options dynamically if needed -->
                            </select>
                        </div>

                        <div class="me-3">
                            <label for="afdelingSelect" class="form-label">Afdeling:</label>
                            <select id="afdelingSelect" class="form-select" disabled>
                                <option value="">All</option>
                                <option value="Edinburgh">Edinburgh</option>
                                <!-- Add other options dynamically if needed -->
                            </select>
                        </div>

                        <div class="me-3">
                            <label for="tahunTanamSelect" class="form-label">Tahun Tanam:</label>
                            <select id="tahunTanamSelect" class="form-select"
                                style="background-color: green; color: white;">
                                <option value="">All</option>
                                <option value="2006">2006</option>
                                <!-- Add other options dynamically if needed -->
                            </select>
                        </div>
                    </div>



                    <div class="table-responsive">
                        <table id="datatable" class="table table-striped" data-toggle="data-table">
                            <thead>
                                <tr>
                                    <th>Regional</th>
                                    <th>Kebun</th>
                                    <th>Afdeling</th>
                                    <th>Blok</th>
                                    <th>Tahun Tanam</th>
                                    <th>Jumlah Pokok</th>
                                    <th>Luas Blok (Ha)</th>
                                    <th>Jenis Pupuk</th>
                                    <th>Jumlah Pemupukan</th>
                                    <th>Luas Pemupukan</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Tiger Nixon</td>
                                    <td>System Architect</td>
                                    <td>Edinburgh</td>
                                    <td>61</td>
                                    <td>2011/04/25</td>
                                    <td>$320,800</td>
                                    <td>$320,800</td>
                                    <td>$320,800</td>
                                    <td>$320,800</td>
                                    <td>$320,800</td>
                                    <td>
                                        <button class="btn btn-primary btn-sm">Edit</button>
                                        <button class="btn btn-danger btn-sm"
                                            onclick="confirmDelete(this)">Delete</button>
                                    </td>
                                </tr>
                                <!-- Add more rows as needed -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Regional</th>
                                    <th>Kebun</th>
                                    <th>Afdeling</th>
                                    <th>Blok</th>
                                    <th>Tahun Tanam</th>
                                    <th>Jumlah Pokok</th>
                                    <th>Luas Blok (Ha)</th>
                                    <th>Jenis Pupuk</th>
                                    <th>Jumlah Pemupukan</th>
                                    <th>Luas Pemupukan</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                        </table>

                        <script type="text/javascript">
                            // Dropdown Filtering Logic
                            const regionalSelect = document.getElementById('regionalSelect');
                            const kebunSelect = document.getElementById('kebunSelect');
                            const afdelingSelect = document.getElementById('afdelingSelect');

                            regionalSelect.addEventListener('change', () => {
                                kebunSelect.disabled = !regionalSelect.value;
                                afdelingSelect.disabled = true;
                                kebunSelect.value = '';
                                afdelingSelect.value = '';
                                filterTable();
                            });

                            kebunSelect.addEventListener('change', () => {
                                afdelingSelect.disabled = !kebunSelect.value;
                                afdelingSelect.value = '';
                                filterTable();
                            });

                            afdelingSelect.addEventListener('change', filterTable);

                            function filterTable() {
                                const regional = regionalSelect.value.toLowerCase();
                                const kebun = kebunSelect.value.toLowerCase();
                                const afdeling = afdelingSelect.value.toLowerCase();

                                document.querySelectorAll('#datatable tbody tr').forEach(row => {
                                    const regionalText = row.cells[0].textContent.toLowerCase();
                                    const kebunText = row.cells[1].textContent.toLowerCase();
                                    const afdelingText = row.cells[2].textContent.toLowerCase();

                                    const matchesFilter =
                                        (!regional || regionalText.includes(regional)) &&
                                        (!kebun || kebunText.includes(kebun)) &&
                                        (!afdeling || afdelingText.includes(afdeling));

                                    row.style.display = matchesFilter ? '' : 'none';
                                });
                            }

                            // Delete Confirmation
                            function confirmDelete(button) {
                                Swal.fire({
                                    title: 'Are you sure?',
                                    text: "This action cannot be undone!",
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#d33',
                                    confirmButtonText: 'Yes, delete it!'
                                }).then(result => {
                                    if (result.isConfirmed) {
                                        Swal.fire('Deleted!', 'The record has been deleted.', 'success');
                                    }
                                });
                            }
                        </script>

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
