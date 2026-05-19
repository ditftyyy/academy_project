{{-- Load Select2 sebelum extends --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

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
                      maxlength="20" value="{{ old('namaTamu') }}"
                      @if($errors->has('namaTamu')) autofocus @endif>
                  @error('namaTamu')
                      <span class="text-danger">{{ $message }}</span>
                  @enderror
                </div>

                {{-- Alamat --}}
                <div class="mb-3 col-md-6" style="padding-left: 20px; padding-right: 20px;">
                  <label for="input_alamat" class="form-label">Alamat / Asal Instansi</label>
                  <input id="input_alamat" type="text" name="alamatTamu" class="form-control"
                      value="{{ old('alamatTamu') }}"
                      @if($errors->has('alamatTamu')) autofocus @endif>
                  @error('alamatTamu')
                      <span class="text-danger">{{ $message }}</span>
                  @enderror
                </div>

                {{-- Tujuan Bertemu --}}
                <div class="mb-3" style="padding-left: 20px; padding-right: 20px;">
                  <label class="col-form-label">Bertujuan Bertemu Dengan Siapa</label>
                  <div class="row g-3 py-1">
                    <div class="col-md-4">
                      <select onchange="handleTujuan(this)" id="opsi_tujuan" name="Opsi" class="form-select">
                        <option value="" selected disabled>Pilih Tujuan</option>
                        @foreach ($userRoles as $role)
                          <option value="{{ $role }}">{{ ucwords($role) }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="col-md-4">
                      <select id="opsi_lanjutan" name="Opsi_Lanjutan" class="form-select">
                        <option value="">Cari Nama</option>
                      </select>
                    </div>
                  </div>
                  <span class="form-text">
                    Keterangan : Silahkan pilih Tujuan, kemudian cari nama yang ingin ditemui.
                  </span>
                </div>

                {{-- Keterangan --}}
                <div class="mb-3" style="padding-left: 20px; padding-right: 20px;">
                  <label for="keterangan" class="form-label fs-6">Keterangan</label>
                  <textarea class="form-control" name="keteranganTamu" id="keterangan" rows="4"
                      placeholder="Jelaskan tujuan kedatangan">{{ old('keteranganTamu') }}</textarea>
                  @error('keteranganTamu')
                      <span class="text-danger">{{ $message }}</span>
                  @enderror
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

{{-- Letakkan script di bawah, gunakan @push('script') agar masuk ke yield('script') --}}
@push('script')
<!-- Select2 -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.0/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
  $(document).ready(function() {
    const opsi_lanjutan_dropdown = $('#opsi_lanjutan');
    const opsi_tujuan_dropdown = $('#opsi_tujuan');

    opsi_tujuan_dropdown.select2({
      theme: 'bootstrap-5',
      width: '100%',
      placeholder: 'Pilih Tujuan',
    });

    opsi_lanjutan_dropdown.select2({
      theme: 'bootstrap-5',
      width: '100%',
      placeholder: 'Cari nama',
    });

    const handleTujuan = async (role) => {
      try {
        const res = await fetch(`/get-username-by-role/${role}`);
        const result = await res.json();
        // result = [{nama: '...', username: '...'}, ...]
        let options = result.map(user => `<option value="${user.username}">${user.nama}</option>`);
        opsi_lanjutan_dropdown.empty().append(options).trigger('change');
      } catch (error) {
        console.error('Error:', error);
      }
    };

    opsi_tujuan_dropdown.on('change', function() {
      const selectedRole = $(this).val();
      if (selectedRole) handleTujuan(selectedRole);
    });

    opsi_lanjutan_dropdown.on('select2:opening', function(e) {
      if (!opsi_tujuan_dropdown.val()) {
        e.preventDefault();
        alert('Pilih terlebih dahulu tujuan untuk memfilter nama.');
      }
    });
  });
</script>
@endpush