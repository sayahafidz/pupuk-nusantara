<x-app-layout :assets="$assets ?? []">
    <div>
        {!! Form::open([
            'route' => 'rencana-pemupukan.import',
            'method' => 'post',
            'enctype' => 'multipart/form-data',
            'id' => 'uploadForm',
        ]) !!}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <!-- Card Header -->
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Upload Rencana Pemupukan Data</h4>
                        <a href="{{ route('rencana-pemupukan.index') }}" class="btn btn-sm btn-primary"
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

                            const formData = new FormData();
                            formData.append('parsed_data', JSON.stringify(chunkData));
                            formData.append('_token', '{{ csrf_token() }}');

                            $.ajax({
                                url: '{{ route('rencana-pemupukan.import') }}',
                                type: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
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

            $('#file-upload').on('change', function() {
                const file = $(this).prop('files')[0];
                const fileName = file ? file.name : 'No file selected';
                $('.file-info').html(`Selected file: ${fileName}`);
            });
        });
    </script>



</x-app-layout>
