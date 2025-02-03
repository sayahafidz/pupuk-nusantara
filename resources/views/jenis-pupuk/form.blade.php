<x-app-layout :assets="$assets ?? []">
    <div>
        <?php
        $id = $id ?? null;
        ?>
        @if (isset($id))
            {!! Form::model($data, [
                'route' => ['jenis-pupuk.update', $id],
                'method' => 'patch',
                'enctype' => 'multipart/form-data',
            ]) !!}
        @else
            {!! Form::open(['route' => ['jenis-pupuk.store'], 'method' => 'post', 'enctype' => 'multipart/form-data']) !!}
        @endif
        <div class="row">
            <div class="col-xl-12 col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">{{ $id !== null ? 'Update' : 'Add' }} Pupuk</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label" for="kode_pupuk">Kode Pupuk: <span
                                    class="text-danger">*</span></label>
                            {{ Form::text('kode_pupuk', old('kode_pupuk', $data->kode_pupuk ?? ''), ['class' => 'form-control', 'placeholder' => 'Kode Pupuk', 'required']) }}
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="nama_pupuk">Nama Pupuk: <span
                                    class="text-danger">*</span></label>
                            {{ Form::text('nama_pupuk', old('nama_pupuk', $data->nama_pupuk ?? ''), ['class' => 'form-control', 'placeholder' => 'Nama Pupuk', 'required']) }}
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="jenis_pupuk">Jenis Pupuk:</label>
                            {{ Form::text('jenis_pupuk', old('jenis_pupuk', $data->jenis_pupuk ?? ''), ['class' => 'form-control', 'placeholder' => 'Jenis Pupuk']) }}
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="harga">Harga:</label>
                            {{ Form::number('harga', old('harga', $data->harga ?? ''), ['class' => 'form-control', 'placeholder' => 'Harga']) }}
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="stok">Stok:</label>
                            {{ Form::number('stok', old('stok', $data->stok ?? ''), ['class' => 'form-control', 'placeholder' => 'Stok']) }}
                        </div>
                        <button type="submit" class="btn btn-primary">{{ $id !== null ? 'Update' : 'Add' }}
                            Pupuk</button>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</x-app-layout>
