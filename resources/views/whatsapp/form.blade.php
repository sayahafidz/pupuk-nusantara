<x-app-layout :assets="$assets ?? []">
    <div>
        <?php
        $id = $id ?? null; // Check if an ID is passed
        ?>
        <div class="row">
            <div class="col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">{{ $id ? 'Update' : 'Add' }} WhatsApp Record</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <p>Use this form to {{ $id ? 'update' : 'add' }} WhatsApp records in the system. All fields are
                            required for accuracy.</p>
                        @if ($id)
                            {!! Form::model($whatsapp, [
                                'route' => ['whatsapp.update', $id],
                                'method' => 'patch',
                                'id' => 'whatsapp-form',
                                'class' => 'row g-3 needs-validation',
                                'novalidate' => true,
                            ]) !!}
                        @else
                            {!! Form::open([
                                'route' => ['whatsapp.store'],
                                'method' => 'post',
                                'id' => 'whatsapp-form',
                                'class' => 'row g-3 needs-validation',
                                'novalidate' => true,
                            ]) !!}
                        @endif
                        <!-- CSRF Token -->
                        @csrf

                        <!-- Name Field -->
                        <div class="col-md-6">
                            <label for="name" class="form-label">Name</label>
                            {{ Form::text('name', old('name', $whatsapp->name ?? ''), ['class' => 'form-control', 'id' => 'name', 'required']) }}
                            <div class="invalid-feedback">Please enter a name.</div>
                        </div>

                        <!-- Phone Field -->
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone</label>
                            {{ Form::text('phone', old('phone', $whatsapp->phone ?? ''), ['class' => 'form-control', 'id' => 'phone', 'required']) }}
                            <div class="invalid-feedback">Please enter a valid phone number.</div>
                        </div>

                        <!-- Status Field -->
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status</label>
                            {{ Form::select('status', ['Active' => 'Active', 'Inactive' => 'Inactive'], old('status', $whatsapp->status ?? ''), ['class' => 'form-select', 'id' => 'status', 'required']) }}
                            <div class="invalid-feedback">Please select a status.</div>
                        </div>

                        <!-- User Dropdown -->
                        <div class="col-md-6">
                            <label for="user_id" class="form-label">User</label>
                            {{ Form::select('user_id', $users ?? [], old('user_id', $whatsapp->user_id ?? ''), [
                                'class' => 'form-control',
                                'id' => 'user_id',
                                'required',
                                'placeholder' => 'Select User...',
                            ]) }}
                            <div class="invalid-feedback">Please select a valid user.</div>
                        </div>


                        <!-- Submit Button -->
                        <div class="col-12">
                            <button class="btn btn-primary form-control" type="submit">
                                {{ $id ? 'Update' : 'Save' }} WhatsApp Record
                            </button>
                        </div>

                        <!-- Back Button -->
                        <div class="col-12 mt-3">
                            <a href="{{ route('whatsapp.index') }}" class="btn btn-secondary form-control">Back</a>
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
            const userDropdown = $('#user_id');

            // Fetch users and populate the dropdown
            $.ajax({
                url: '/api/users',
                method: 'GET',
                success: function(whatsapp) {
                    userDropdown.empty(); // Clear existing options
                    userDropdown.append(new Option('Select User...', '', true, true));
                    whatsapp.forEach(user => {
                        userDropdown.append(new Option(user.first_name, user.id));
                    });
                },
                error: function() {
                    console.error('Error fetching users');
                }
            });

            // Handle form validation on submit
            $('#whatsapp-form').on('submit', function(event) {
                if (!this.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                    this.classList.add('was-validated');
                }
            });
        });
    </script>
</x-app-layout>
