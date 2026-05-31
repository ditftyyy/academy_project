@extends('components.main')

@section('title-content', 'Data Tamu')

@section('breadcrumbs')
<ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item text-sm text-dark active"><a class="opacity-5 text-dark" href="/data-tamu">Daftar Tamu</a></li>
    <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Tambah Tamu</li>
</ol>
<h6 class="font-weight-bolder mb-0">Data Tamu</h6>
@endsection

@section('content')
<div class="row">
  <div class="col-12">
      <div class="card my-4">
          <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                  <h6 class="text-white text-capitalize ps-3">Tambah Data Tamu</h6>
              </div>
          </div>
          <div class="card-body px-0 pb-2">
            <main class="form-tambah-tamu">
              <form action="/tamu" method="post">
                @csrf

                {{-- Nama Tamu --}}
                <div class="mb-3 col-md-6" style="padding-left: 20px; padding-right: 20px;">
                  <label for="nama_tamu" class="form-label">Nama Tamu</label>
                  <input id="nama_tamu" type="text" name="namaTamu" class="form-control rounded-3" 
                         maxlength="20" value="{{ old('namaTamu') }}" required>
                  @error('namaTamu') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                {{-- Alamat / Asal Instansi --}}
                <div class="mb-3 col-md-6" style="padding-left: 20px; padding-right: 20px;">
                  <label for="input_alamat" class="form-label">Alamat / Asal Instansi</label>
                  <input id="input_alamat" type="text" name="alamatTamu" class="form-control" 
                         value="{{ old('alamatTamu') }}" required>
                  @error('alamatTamu') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                {{-- Keterangan --}}
                <div class="mb-3" style="padding-left: 20px; padding-right: 20px;">
                  <label for="keterangan" class="form-label fs-6">Keterangan</label>
                  <textarea class="form-control" name="keteranganTamu" id="keterangan" rows="4" 
                            placeholder="Jelaskan tujuan kedatangan" required>{{ old('keteranganTamu') }}</textarea>
                  @error('keteranganTamu') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="card-footer d-flex justify-content-end" style="gap: 10px">
                  <a href="/data-tamu" class="btn btn-danger text-sm rounded-3">
                    <i class="fa fa-arrow-left"></i> Kembali
                  </a>
                  <button type="submit" class="btn btn-primary text-sm rounded-3">
                    <i class="fa fa-save"></i> Simpan
                  </button>
                </div>
              </form>
            </main>
          </div>
      </div>
  </div>
</div>
@endsection

{{-- Tidak ada script tambahan karena Select2 dihapus --}}