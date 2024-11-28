<x-app-layout :assets="$assets ?? []">
    <div>
        {!! Form::open([
            'route' => 'master-data.import',
            'method' => 'post',
            'enctype' => 'multipart/form-data',
            'id' => 'uploadForm',
        ]) !!}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <!-- Card Header -->
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Upload Master Data</h4>
                        <a href="{{ route('master-data.index') }}" class="btn btn-sm btn-primary" role="button">Back</a>
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
                            <small class="file-info mt-2 d-block">
                                Allowed file formats: <strong>.csv, .xlsx, .xls</strong>
                            </small>
                        </div>

                        <!-- Upload Details -->
                        <div class="upload-details mt-4">
                            <p>Please ensure the file format is correct and adheres to the template before uploading.
                            </p>
                            <p>
                                To download the template,
                                <a href="{{ route('master-data.index') }}" class="text-primary">click here</a>.
                            </p>
                        </div>

                        <!-- Progress Bar -->
                        <div class="progress mt-4">
                            <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 0%;"
                                aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('upload-button').addEventListener('click', function() {
            const fileInput = document.getElementById('file-upload');
            const file = fileInput.files[0];

            if (!file) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No File Selected',
                    text: 'Please select a file to upload.',
                });
                return;
            }

            processUploadedFile(file)
                .then((data) => {
                    const CHUNK_SIZE = 5000;
                    let currentChunk = 0;
                    const totalChunks = Math.ceil(data.length / CHUNK_SIZE);

                    const sendChunk = () => {
                        if (currentChunk >= totalChunks) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Upload Complete',
                                text: 'All data uploaded successfully!',
                            });
                            return;
                        }

                        const chunk = data.slice(currentChunk * CHUNK_SIZE, (currentChunk + 1) *
                        CHUNK_SIZE);
                        currentChunk++;

                        const formData = new FormData();
                        formData.append('parsed_data', JSON.stringify(chunk));
                        formData.append('_token', '{{ csrf_token() }}');

                        fetch('{{ route('master-data.import') }}', {
                                method: 'POST',
                                body: formData,
                            })
                            .then((response) => response.json())
                            .then((result) => {
                                console.log(`Chunk ${currentChunk} uploaded successfully.`);
                                document.getElementById('progress-bar').style.width =
                                    `${(currentChunk / totalChunks) * 100}%`;
                                sendChunk();
                            })
                            .catch((error) => {
                                console.error('Error uploading chunk:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Upload Failed',
                                    text: `Failed to upload chunk ${currentChunk}. Please try again.`,
                                });
                            });
                    };

                    sendChunk();
                })
                .catch((error) => {
                    console.error('Error processing file:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error Processing File',
                        html: error.message,
                    });
                });
        });

        function processUploadedFile(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();

                reader.onerror = () => reject(new Error('Error reading the file.'));
                reader.onload = (event) => {
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
    </script>
</x-app-layout>
