@extends('components.main')

@section('breadcrumbs')
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="/administrasi/siswa">Siswa</a></li>
        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Tambah</li>
    </ol>
    <h6 class="font-weight-bolder mb-0">Tambah Data Siswa</h6>
@endsection

@section('script')
    <script>
        function hanyaAngka(evt) {
            var charCode = (evt.which) ? evt.which : event.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57)) return false;
            return true;
        }

        function showPreviewposter(event) {
            if (event.target.files.length > 0) {
                var src = URL.createObjectURL(event.target.files[0]);
                var preview = document.getElementById("file-preview-poster");
                preview.src = src;
                preview.style.display = "block";
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const statusSelect = document.getElementById('status');
            const asalSekolah = document.getElementById('asal_sekolah_container');

            function toggleAsalSekolah() {
                asalSekolah.style.display = (statusSelect.value === 'pindahan') ? 'block' : 'none';
            }
            toggleAsalSekolah();
            statusSelect.addEventListener('change', toggleAsalSekolah);
        });
    </script>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Tambah Data Siswa</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <form action="/administrasi/siswa-tambah" class="row g-3 py-1 px-4" method="post" enctype="multipart/form-data">
                        @csrf

                        {{-- NIS --}}
                        <div class="col-md-6">
                            <label for="nis" class="form-label">NIS</label>
                            <input type="text" onkeypress="return hanyaAngka(event)" name="nis"
                                   class="form-control rounded-3" id="nis" required
                                   value="{{ old('nis') }}">
                            @error('nis') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        {{-- NISN --}}
                        <div class="col-md-6">
                            <label for="nisn" class="form-label">NISN</label>
                            <input type="text" onkeypress="return hanyaAngka(event)" name="nisn"
                                   class="form-control rounded-3" required value="{{ old('nisn') }}" id="nisn">
                            @error('nisn') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        {{-- NIK --}}
                        <div class="col-md-6">
                            <label for="nik" class="form-label">NIK</label>
                            <input type="text" onkeypress="return hanyaAngka(event)" name="nik"
                                   class="form-control rounded-3" id="nik" required value="{{ old('nik') }}">
                            @error('nik') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        {{-- Nama Lengkap --}}
                        <div class="col-md-6">
                            <label class="form-label" for="nama">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control rounded-3" id="nama" required
                                   value="{{ old('nama') }}">
                        </div>

                        {{-- Tempat Lahir --}}
                        <div class="col-md-3">
                            <label class="form-label" for="tempat-lahir">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="form-control rounded-3" id="tempat-lahir"
                                   required value="{{ old('tempat_lahir') }}">
                        </div>

                        {{-- Tanggal Lahir --}}
                        <div class="col-md-3">
                            <label class="form-label" for="tanggal-lahir">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control rounded-3" id="tanggal-lahir"
                                   required value="{{ old('tanggal_lahir') }}">
                        </div>

                        {{-- Jenis Kelamin --}}
                        <div class="col-md-6">
                            <label class="form-label">Jenis Kelamin</label><br>
                            @foreach (['laki-laki', 'perempuan'] as $gender)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="jenis_kelamin"
                                           id="{{ $gender }}" value="{{ $gender }}"
                                           @if(old('jenis_kelamin') == $gender) checked @endif required>
                                    <label class="form-check-label" for="{{ $gender }}">{{ ucfirst($gender) }}</label>
                                </div>
                            @endforeach
                        </div>

                        {{-- Agama --}}
                        <div class="col-md-6">
                            <label class="form-label" for="agama">Agama</label>
                            <select class="form-select rounded-3" name="agama" id="agama" required>
                                <option value="">-- Pilih Agama --</option>
                                @foreach ($agamas as $agama)
                                    <option value="{{ $agama }}" @if(old('agama') == $agama) selected @endif>{{ ucfirst($agama) }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Nama Ayah --}}
                        <div class="col-md-6">
                            <label class="form-label" for="nama-ayah">Nama Ayah</label>
                            <input type="text" name="nama_ayah" class="form-control rounded-3" id="nama-ayah"
                                   required value="{{ old('nama_ayah') }}">
                        </div>

                        {{-- Nama Ibu --}}
                        <div class="col-md-6">
                            <label class="form-label" for="nama-ibu">Nama Ibu</label>
                            <input type="text" name="nama_ibu" class="form-control rounded-3" id="nama-ibu"
                                   required value="{{ old('nama_ibu') }}">
                        </div>

                        {{-- Nama Wali --}}
                        <div class="col-md-6">
                            <label class="form-label" for="nama-wali">Nama Wali</label>
                            <input type="text" name="nama_wali" class="form-control rounded-3" id="nama-wali"
                                   value="{{ old('nama_wali') }}">
                        </div>

                        {{-- Kelas --}}
                        <div class="col-md-6">
                            <label class="form-label" for="kelas">Kelas</label>
                            <select class="form-select rounded-3" name="kelas" id="kelas" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach ($list_kelas as $kelas)
                                    <option value="{{ (string)$kelas->_id }}" @if(old('kelas') == (string)$kelas->_id) selected @endif>
                                        {{ $kelas->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- No Telepon --}}
                        <div class="col-md-6">
                            <label class="form-label" for="no-telp">No Telepon</label>
                            <input type="text" maxlength="13" onkeypress="return hanyaAngka(event)"
                                   name="no_telp" class="form-control rounded-3" id="no-telp" required
                                   value="{{ old('no_telp') }}">
                            @error('no_telp') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        {{-- Status --}}
                        <div class="col-md-6">
                            <label class="form-label" for="status">Status</label>
                            <select class="form-select rounded-3" name="status" id="status" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="bukan pindahan" @if(old('status') == 'bukan pindahan') selected @endif>Baru</option>
                                <option value="pindahan" @if(old('status') == 'pindahan') selected @endif>Pindahan</option>
                            </select>
                        </div>

                        {{-- Asal Sekolah (hanya untuk pindahan) --}}
                        <div class="col-md-6" id="asal_sekolah_container" style="display: none;">
                            <label class="form-label" for="asal-sekolah">Asal Sekolah</label>
                            <input type="text" class="form-control rounded-3" name="asal_sekolah" id="asal-sekolah"
                                   value="{{ old('asal_sekolah') }}">
                        </div>

                        {{-- Alamat --}}
                        <div class="col-md-6">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control rounded-3" rows="3" required>{{ old('alamat') }}</textarea>
                        </div>

                        {{-- Foto --}}
                        <div class="col-md-6">
                            <label for="file-input-poster" class="form-label">Foto</label>
                            <input class="form-control rounded-3 text-sm" name="foto" type="file"
                                   id="file-input-poster" accept="image/*" required onchange="showPreviewposter(event);">
                            <img src="{{ asset('assets/img/thumbnail.png') }}" id="file-preview-poster"
                                 alt="..." class="img-thumbnail mt-2" width="50%">
                            @error('foto') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        {{-- Tombol --}}
                        <div class="col-12 d-flex justify-content-end gap-2">
                            <a href="/administrasi/siswa" class="btn btn-danger text-sm rounded-3">
                                <i class="fa fa-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary text-sm rounded-3"
                                    onclick="return confirm('Apakah anda yakin data sudah benar?')">
                                <i class="fa fa-save"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection