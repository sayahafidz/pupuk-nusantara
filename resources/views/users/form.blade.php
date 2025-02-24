<x-app-layout :assets="$assets ?? []">
    <div>
        <?php
        $id = $id ?? null;
        ?>
        @if (isset($id))
            {!! Form::model($data, [
                'route' => ['users.update', $id],
                'method' => 'patch',
                'enctype' => 'multipart/form-data',
            ]) !!}
        @else
            {!! Form::open(['route' => ['users.store'], 'method' => 'post', 'enctype' => 'multipart/form-data']) !!}
        @endif
        <div class="row">
            <div class="col-xl-3 col-lg-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">{{ $id !== null ? 'Update' : 'Add' }} User</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <div class="profile-img-edit position-relative">
                                <img src="{{ $profileImage ?? asset('images/avatars/01.png') }}" alt="User-Profile"
                                    class="profile-pic rounded avatar-100">
                                <div class="upload-icone bg-primary">
                                    <svg class="upload-button" width="14" height="14" viewBox="0 0 24 24">
                                        <path fill="#ffffff"
                                            d="M14.06,9L15,9.94L5.92,19H5V18.08L14.06,9M17.66,3C17.41,3 17.15,3.1 16.96,3.29L15.13,5.12L18.88,8.87L20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18.17,3.09 17.92,3 17.66,3M14.06,6.19L3,17.25V21H6.75L17.81,9.94L14.06,6.19Z" />
                                    </svg>
                                    <input class="file-upload" type="file" accept="image/*" name="profile_image">
                                </div>
                            </div>
                            <div class="img-extension mt-3">
                                <div class="d-inline-block align-items-center">
                                    <span>Only</span>
                                    <a href="javascript:void();">.jpg</a>
                                    <a href="javascript:void();">.png</a>
                                    <a href="javascript:void();">.jpeg</a>
                                    <span>allowed</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Status: <span class="text-danger">*</span></label>
                            <div class="grid" style="--bs-gap: 1rem">
                                <div class="form-check g-col-6">
                                    {{ Form::radio('status', 'active', old('status', $data->status ?? true), ['class' => 'form-check-input', 'id' => 'status-active']) }}
                                    <label class="form-check-label" for="status-active">Active</label>
                                </div>
                                <div class="form-check g-col-6">
                                    {{ Form::radio('status', 'pending', old('status', $data->status ?? false), ['class' => 'form-check-input', 'id' => 'status-pending']) }}
                                    <label class="form-check-label" for="status-pending">Pending</label>
                                </div>
                                <div class="form-check g-col-6">
                                    {{ Form::radio('status', 'inactive', old('status', $data->status ?? false), ['class' => 'form-check-input', 'id' => 'status-inactive']) }}
                                    <label class="form-check-label" for="status-inactive">Inactive</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Banned:</label>
                            <div class="form-check">
                                {{ Form::checkbox('banned', 1, old('banned', $data->banned ?? false), ['class' => 'form-check-input', 'id' => 'banned']) }}
                                <label class="form-check-label" for="banned">Yes</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">User Role: <span class="text-danger">*</span></label>
                            {{ Form::select('user_role', $roles, old('user_role', $data->user_type ?? 'user'), ['class' => 'form-control', 'placeholder' => 'Select User Role', 'required']) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-9 col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">{{ $id !== null ? 'Update' : 'New' }} User Information</h4>
                        </div>
                        <div class="card-action">
                            <a href="{{ route('users.index') }}" class="btn btn-sm btn-primary" role="button">Back</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="new-user-info">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="form-label" for="username">Username: <span
                                            class="text-danger">*</span></label>
                                    {{ Form::text('username', old('username', $data->username ?? ''), ['class' => 'form-control', 'id' => 'username', 'placeholder' => 'Enter Username', 'required']) }}
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label" for="email">Email: <span
                                            class="text-danger">*</span></label>
                                    {{ Form::email('email', old('email', $data->email ?? ''), ['class' => 'form-control', 'id' => 'email', 'placeholder' => 'Enter Email', 'required']) }}
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label" for="first_name">First Name: <span
                                            class="text-danger">*</span></label>
                                    {{ Form::text('first_name', old('first_name', $data->first_name ?? ''), ['class' => 'form-control', 'id' => 'first_name', 'placeholder' => 'Enter First Name', 'required']) }}
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label" for="last_name">Last Name: <span
                                            class="text-danger">*</span></label>
                                    {{ Form::text('last_name', old('last_name', $data->last_name ?? ''), ['class' => 'form-control', 'id' => 'last_name', 'placeholder' => 'Enter Last Name', 'required']) }}
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label" for="phone_number">Phone Number:</label>
                                    {{ Form::text('phone_number', old('phone_number', $data->phone_number ?? ''), ['class' => 'form-control', 'id' => 'phone_number', 'placeholder' => 'Enter Phone Number']) }}
                                </div>
                            </div>
                            <hr>
                            <h5 class="mb-3">Security</h5>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="form-label" for="password">Password:
                                        {{ $id ? '' : '<span class="text-danger">*</span>' }}</label>
                                    {{ Form::password('password', ['class' => 'form-control', 'id' => 'password', 'placeholder' => 'Enter Password', $id ? '' : 'required']) }}
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label" for="password_confirmation">Repeat Password:</label>
                                    {{ Form::password('password_confirmation', ['class' => 'form-control', 'id' => 'password_confirmation', 'placeholder' => 'Repeat Password']) }}
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">{{ $id !== null ? 'Update' : 'Add' }}
                                User</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</x-app-layout>
