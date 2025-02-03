<x-app-layout :assets="$assets ?? []">
    <div>
        {!! Form::open([
            'route' => 'rencana-realisasi-pemupukan.import',
            'method' => 'post',
            'enctype' => 'multipart/form-data',
            'id' => 'uploadForm',
        ]) !!}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <!-- Card Header -->
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Upload Rencana Realisasi Pemupukan Data</h4>
                        <a href="{{ route('rencana-realisasi-pemupukan.index') }}" class="btn btn-sm btn-primary"
                            role="button">Back</a>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body">
                        <!-- File Upload Input -->
                        <div class="form-group">
                            <label for="file-upload" class="form-label">
                                Select File to Upload: <span class="text-danger">*</span>
                            </label>
                            <input type="file" class="form-control" id="file-upload" name="data_file" required
                                accept=".csv, .xlsx, .xls">
                            <small class="file-info mt-2 d-block text-muted">
                                Allowed formats: <strong>.csv, .xlsx, .xls</strong>. Max size: 10MB.
                            </small>
                        </div>

                        <!-- Upload Instructions -->
                        <div class="upload-details mt-4">
                            <p>Please ensure the file format adheres to the template before uploading.</p>
                            <p>
                                To download the template,
                                <a href="{{ route('rekap-pemupukan') }}" class="text-primary">click here</a>.
                            </p>
                        </div>

                        <!-- Progress Bar -->
                        <div class="progress mt-4">
                            <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated"
                                role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0"
                                aria-valuemax="100">
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="button" id="upload-button" class="btn btn-primary mt-3">Upload Data</button>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>

    <!-- JavaScript Section -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            const CHUNK_SIZE = 500; // Set smaller chunk size for better performance

            $('#upload-button').on('click', function() {
                const fileInput = $('#file-upload');
                const file = fileInput[0].files[0];

                if (!file) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No File Selected',
                        text: 'Please select a file to upload.',
                    });
                    return;
                }

                processUploadedFile(file)
                    .then(function(data) {
                        let currentChunk = 0;
                        const totalChunks = Math.ceil(data.length / CHUNK_SIZE);

                        const uploadNextChunk = function() {
                            if (currentChunk >= totalChunks) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Upload Complete',
                                    text: 'All data uploaded successfully!',
                                });
                                return;
                            }

                            const chunkStart = currentChunk * CHUNK_SIZE;
                            const chunkEnd = Math.min((currentChunk + 1) * CHUNK_SIZE, data.length);
                            const chunkData = data.slice(chunkStart, chunkEnd);

                            // Process data and group by fertilizer columns on client side
                            const processedData = processFertilizerData(chunkData);

                            $.ajax({
                                url: '{{ route('rencana-realisasi-pemupukan.import') }}',
                                type: 'POST',
                                data: {
                                    parsed_data: JSON.stringify(processedData),
                                    _token: '{{ csrf_token() }}',
                                },
                                success: function(response) {
                                    const progressPercentage = ((currentChunk + 1) /
                                        totalChunks) * 100;
                                    $('#progress-bar').css('width',
                                        `${progressPercentage}%`);
                                    currentChunk++;

                                    // Upload the next chunk
                                    uploadNextChunk();
                                },
                                error: function(error) {
                                    console.error('Error uploading chunk:', error);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Upload Failed',
                                        text: `Error uploading chunk ${currentChunk + 1}.`,
                                    });
                                }
                            });
                        };

                        // Start uploading chunks
                        uploadNextChunk();
                    })
                    .catch(function(error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'File Processing Error',
                            text: error.message,
                        });
                    });
            });

            // File change handler to show selected file name
            $('#file-upload').on('change', function() {
                const file = $(this).prop('files')[0];
                const fileName = file ? file.name : 'No file selected';
                $('.file-info').html(`Selected file: ${fileName}`);
            });

            // Process the uploaded file and return data in the correct format
            function processUploadedFile(file) {
                return new Promise(function(resolve, reject) {
                    const reader = new FileReader();

                    reader.onerror = function() {
                        reject(new Error('Error reading the file.'));
                    };

                    reader.onload = function(event) {
                        try {
                            const workbook = XLSX.read(event.target.result, {
                                type: 'binary'
                            });
                            const firstSheetName = workbook.SheetNames[0];
                            const sheet = workbook.Sheets[firstSheetName];
                            const data = XLSX.utils.sheet_to_json(sheet, {
                                header: 1,
                                blankrows: false,
                                range: 1,
                                defval: '',
                            });
                            resolve(data);
                        } catch (error) {
                            reject(new Error('Failed to parse the file.'));
                        }
                    };

                    reader.readAsBinaryString(file);
                });
            }

            // Process fertilizer data and group by fertilizer columns on client side
            function processFertilizerData(data) {
                const fertilizerColumns = [{
                        jenis_pupuk: 'NPK 12.12.17.2',
                        column_index: 12
                    },
                    {
                        jenis_pupuk: 'NPK 13.6.27.4',
                        column_index: 13
                    },
                    {
                        jenis_pupuk: 'Dolomit',
                        column_index: 14
                    },
                    {
                        jenis_pupuk: 'Mop',
                        column_index: 15
                    },
                    {
                        jenis_pupuk: 'Urea',
                        column_index: 16
                    },
                    {
                        jenis_pupuk: 'TSP',
                        column_index: 17
                    },
                    {
                        jenis_pupuk: 'ZNSO4',
                        column_index: 18
                    },
                    {
                        jenis_pupuk: 'BORATE',
                        column_index: 19
                    },
                    {
                        jenis_pupuk: 'KIESERETE',
                        column_index: 20
                    },
                    {
                        jenis_pupuk: 'NPK 12.12.17.2',
                        column_index: 21
                    },
                    {
                        jenis_pupuk: 'NPK 13.6.27.4',
                        column_index: 22
                    },
                    {
                        jenis_pupuk: 'Dolomit',
                        column_index: 23
                    },
                    {
                        jenis_pupuk: 'Mop',
                        column_index: 24
                    },
                    {
                        jenis_pupuk: 'Urea',
                        column_index: 25
                    },
                    {
                        jenis_pupuk: 'TSP',
                        column_index: 26
                    },
                    {
                        jenis_pupuk: 'ZNSO4',
                        column_index: 27
                    },
                    {
                        jenis_pupuk: 'BORATE',
                        column_index: 28
                    },
                    {
                        jenis_pupuk: 'KIESERETE',
                        column_index: 29
                    },
                ];

                const processedData = [];
                const jenisPupukMap = {}; // Placeholder for the jenis_pupuk ID mapping

                // Loop through each row of data and process it by fertilizer type
                data.forEach(function(row) {
                    fertilizerColumns.forEach(function(fertilizer) {
                        const amount = parseFloat(str_replace(',', '.', row[fertilizer
                            .column_index]));
                        if (amount > 0) {
                            const semester = fertilizer.column_index >= 21 ? 2 : 1;

                            processedData.push({
                                jenis_pupuk: fertilizer.jenis_pupuk,
                                jumlah_pupuk: amount,
                                regional: row[11],
                                kebun: row[2],
                                afdeling: row[5],
                                blok: row[7],
                                tahun_tanam: parseInt(row[6]),
                                luas_blok: parseFloat(str_replace(',', '.', row[8])),
                                jumlah_pokok: parseInt(row[9]),
                                luas_pemupukan: 0, // Adjust based on your needs
                                semester_pemupukan: semester,
                                created_at: new Date(),
                                updated_at: new Date(),
                            });
                        }
                    });
                });

                return processedData;
            }

            // Helper function to replace commas with dots (for floating-point values)
            function str_replace(search, replace, subject) {
                if (typeof subject !== 'string') {
                    return subject;
                }
                return subject.replace(new RegExp(search, 'g'), replace);
            }
        });
    </script>




</x-app-layout>
