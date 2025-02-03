<x-app-layout :assets="$assets ?? []">
    <div>
        <?php
        $id = $id ?? null;
        ?>
        @if (isset($id))
            {!! Form::model($data, [
                'route' => ['setting.update', $id],
                'method' => 'patch',
                'id' => 'setting-form',
                'class' => 'row g-3 needs-validation',
                'novalidate',
            ]) !!}
        @else
            {!! Form::open([
                'route' => 'setting.store',
                'method' => 'post',
                'id' => 'setting-form',
                'class' => 'row g-3 needs-validation',
                'novalidate',
            ]) !!}
        @endif
        <div class="row">
            <div class="col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">{{ $id !== null ? 'Update' : 'Add' }} Setting Record</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <p>Use this form to {{ $id !== null ? 'update' : 'add' }} setting records in the system. All
                            fields are required for accuracy.</p>

                        <!-- Name Field -->
                        <div class="col-md-12">
                            <label for="name" class="form-label">Name</label>
                            {{ Form::text('name', old('name', $data->name ?? ''), [
                                'class' => 'form-control',
                                'id' => 'name',
                                'required',
                            ]) }}
                            <div class="invalid-feedback">Please enter a name.</div>
                        </div>

                        <!-- Value Field -->
                        <div class="col-md-12">
                            <label for="value" class="form-label">Value</label>
                            {{ Form::text('value', old('value', $data->value ?? ''), [
                                'class' => 'form-control',
                                'id' => 'value',
                                'required',
                            ]) }}
                            <div class="invalid-feedback">Please enter a valid value number.</div>
                        </div>

                        <!-- Submit Button -->
                        <div class="col-12">
                            <button class="btn btn-primary form-control" type="submit">
                                {{ $id !== null ? 'Update' : 'Save' }} Setting Record
                            </button>
                        </div>

                        <!-- Back Button -->
                        <div class="col-12 mt-3">
                            <a href="{{ route('setting.index') }}" class="btn btn-secondary form-control">Back</a>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include jQuery and SweetAlert -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {

            // Form submission logic
            $('#submit-setting').on('click', function() {
                const formData = $('#setting-form').serialize(); // Serialize form data
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

                $.ajax({
                    url: '{{ route('setting.store') }}', // Define the route for storing the record
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken // Add CSRF token to headers
                    },
                    data: formData,
                    success: function(response) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Setting record saved successfully.',
                            icon: 'success'
                        });
                        $('#setting-form')[0]
                            .reset(); // Reset the form after successful submission
                    },
                    error: function(xhr) {
                        // Handle error response
                        Swal.fire({
                            title: 'Error!',
                            text: xhr.responseJSON?.message ||
                                'An error occurred while saving the record.',
                            icon: 'error'
                        });
                    }
                });
            });
        });
    </script>
</x-app-layout>
