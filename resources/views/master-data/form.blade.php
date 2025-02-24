<x-app-layout :assets="$assets ?? []">
   <div>
      <?php
         $id = $id ?? null;
      ?>
      @if(isset($id))
      {!! Form::model($data, ['route' => ['master-data.update', $id], 'method' => 'patch']) !!}
      @else
      {!! Form::open(['route' => ['master-data.store'], 'method' => 'post']) !!}
      @endif
      <div class="row">
         <div class="col-xl-12 col-lg-12">
            <div class="card">
               <div class="card-header d-flex justify-content-between">
                  <div class="header-title">
                     <h4 class="card-title">{{ $id !== null ? 'Update' : 'Add' }} Master Data</h4>
                  </div>
                  <div class="card-action">
                     <a href="{{ route('master-data.index') }}" class="btn btn-sm btn-primary" role="button">Back</a>
                  </div>
               </div>
               <div class="card-body">
                  <div class="new-master-data-info">
                     <div class="row">
                        <div class="form-group col-md-6">
                           <label class="form-label" for="kondisi">Kondisi: <span class="text-danger">*</span></label>
                           {{ Form::text('kondisi', old('kondisi'), ['class' => 'form-control', 'id' => 'kondisi', 'placeholder' => 'Enter Kondisi', 'required']) }}
                        </div>
                        <div class="form-group col-md-6">
                           <label class="form-label" for="status_umur">Status Umur: <span class="text-danger">*</span></label>
                           {{ Form::text('status_umur', old('status_umur'), ['class' => 'form-control', 'id' => 'status_umur', 'placeholder' => 'Enter Status Umur', 'required']) }}
                        </div>
                        <div class="form-group col-md-6">
                           <label class="form-label" for="kode_kebun">Kode Kebun: <span class="text-danger">*</span></label>
                           {{ Form::text('kode_kebun', old('kode_kebun'), ['class' => 'form-control', 'id' => 'kode_kebun', 'placeholder' => 'Enter Kode Kebun', 'required']) }}
                        </div>
                        <div class="form-group col-md-6">
                           <label class="form-label" for="nama_kebun">Nama Kebun: <span class="text-danger">*</span></label>
                           {{ Form::text('nama_kebun', old('nama_kebun'), ['class' => 'form-control', 'id' => 'nama_kebun', 'placeholder' => 'Enter Nama Kebun', 'required']) }}
                        </div>
                        <div class="form-group col-md-6">
                           <label class="form-label" for="kkl_kebun">KKL Kebun:</label>
                           {{ Form::text('kkl_kebun', old('kkl_kebun'), ['class' => 'form-control', 'id' => 'kkl_kebun', 'placeholder' => 'Enter KKL Kebun']) }}
                        </div>
                        <div class="form-group col-md-6">
                           <label class="form-label" for="afdeling">Afdeling: <span class="text-danger">*</span></label>
                           {{ Form::text('afdeling', old('afdeling'), ['class' => 'form-control', 'id' => 'afdeling', 'placeholder' => 'Enter Afdeling', 'required']) }}
                        </div>
                        <div class="form-group col-md-6">
                           <label class="form-label" for="tahun_tanam">Tahun Tanam: <span class="text-danger">*</span></label>
                           {{ Form::number('tahun_tanam', old('tahun_tanam'), ['class' => 'form-control', 'id' => 'tahun_tanam', 'placeholder' => 'Enter Tahun Tanam', 'required']) }}
                        </div>
                        <div class="form-group col-md-6">
                           <label class="form-label" for="no_blok">No Blok: <span class="text-danger">*</span></label>
                           {{ Form::text('no_blok', old('no_blok'), ['class' => 'form-control', 'id' => 'no_blok', 'placeholder' => 'Enter No Blok', 'required']) }}
                        </div>
                        <div class="form-group col-md-6">
                           <label class="form-label" for="luas">Luas:</label>
                           {{ Form::number('luas', old('luas'), ['class' => 'form-control', 'id' => 'luas', 'placeholder' => 'Enter Luas', 'step' => 'any']) }}
                        </div>
                        <div class="form-group col-md-6">
                           <label class="form-label" for="jlh_pokok">Jumlah Pokok:</label>
                           {{ Form::number('jlh_pokok', old('jlh_pokok'), ['class' => 'form-control', 'id' => 'jlh_pokok', 'placeholder' => 'Enter Jumlah Pokok']) }}
                        </div>
                        <div class="form-group col-md-6">
                           <label class="form-label" for="pkk_ha">PKK/HA:</label>
                           {{ Form::number('pkk_ha', old('pkk_ha'), ['class' => 'form-control', 'id' => 'pkk_ha', 'placeholder' => 'Enter PKK/HA', 'step' => 'any']) }}
                        </div>
                        <div class="form-group col-md-6">
                           <label class="form-label" for="rpc">RPC:</label>
                           {{ Form::text('rpc', old('rpc'), ['class' => 'form-control', 'id' => 'rpc', 'placeholder' => 'Enter RPC']) }}
                        </div>
                        <div class="form-group col-md-6">
                           <label class="form-label" for="plant">Plant:</label>
                           {{ Form::text('plant', old('plant'), ['class' => 'form-control', 'id' => 'plant', 'placeholder' => 'Enter Plant']) }}
                        </div>
                     </div>
                     <button type="submit" class="btn btn-primary">{{ $id !== null ? 'Update' : 'Add' }} Master Data</button>
                  </div>
               </div>
            </div>
         </div>
      </div>
      {!! Form::close() !!}
   </div>
</x-app-layout>