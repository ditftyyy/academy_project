@extends('components.main')

@section('breadcrumbs')
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="/data-tamu">Tamu</a></li>
        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Edit</li>
    </ol>
    <h6 class="font-weight-bolder mb-0">Edit Data Tamu</h6>
@endsection

@section('content')
    @php
        $data = $tamu->data_tambahan ?? [];
    @endphp

    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Edit Data Tamu</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <form action="/tamu-edit/{{ $tamu->_id }}" class="row g-3 py-1 px-4" method="post">
                        @csrf
                        @method('PUT')

                        {{-- Nama Lengkap --}}
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="namaTamu" class="form-control"
                                   value="{{ $data['nama_tamu'] ?? '' }}" required>
                        </div>

                        {{-- Asal --}}
                        <div class="col-md-6">
                            <label class="form-label">Asal</label>
                            <input type="text" name="alamatTamu" class="form-control"
                                   value="{{ $data['alamat'] ?? '' }}" required>
                        </div>

                        {{-- Pilih Tujuan --}}
                        <div class="col-md-6">
                            <label class="form-label">Pilih Tujuan</label>
                            <select onchange="handleTujuan(this)" id="opsi_tujuan" name="Opsi" class="form-select">
                                <option value="" disabled {{ empty($data['tujuan']) ? 'selected' : '' }}>Pilih Tujuan</option>
                                @foreach ($userRoles as $role)
                                    <option value="{{ $role }}"
                                        {{ ($data['tujuan'] ?? '') == $role ? 'selected' : '' }}>
                                        {{ ucwords($role) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Pilih nama yang dituju --}}
                        <div class="col-md-6">
                            <label class="form-label">Pilih nama yang ingin dituju</label>
                            <select id="opsi_lanjutan" name="Opsi_Lanjutan" class="form-select">
                                <option value="">Cari nama</option>
                            </select>
                        </div>

                        {{-- Keterangan --}}
                        <div class="col-12">
                            <label for="floatingTextarea" class="form-label">Keterangan</label>
                            <textarea class="form-control" name="keteranganTamu" id="floatingTextarea"
                                      rows="4" required>{{ $tamu->message ?? '' }}</textarea>
                        </div>

                        <div class="card-footer d-flex justify-content-end" style="gap: 10px">
                            <a href="/data-tamu" class="btn btn-danger text-sm rounded-3">Kembali</a>
                            <button type="submit" class="btn btn-primary text-sm rounded-3">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <!-- Select2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.0/dist/jquery.slim.min.js"></script>
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
                    let options = result.map(user =>
                        `<option value="${user.username}">${user.nama}</option>`);
                    opsi_lanjutan_dropdown.empty().append(options);
                    // Set nilai tersimpan sebelumnya (dari data tambahan)
                    const savedUsername = '{{ $data['tujuan_username'] ?? '' }}';
                    if (savedUsername) {
                        opsi_lanjutan_dropdown.val(savedUsername).trigger('change');
                    }
                } catch (error) {
                    console.error('Error:', error);
                }
            };

            // Inisialisasi: jika tujuan sudah terpilih, langsung muat daftar username
            const selectedRole = opsi_tujuan_dropdown.val();
            if (selectedRole) {
                handleTujuan(selectedRole);
            }

            opsi_tujuan_dropdown.on('change', function() {
                handleTujuan($(this).val());
            });
        });
    </script>
@endpush