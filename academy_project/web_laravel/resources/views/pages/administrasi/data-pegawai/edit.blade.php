@extends('components.main')

@section('title-content')
    Data Pegawai
@endsection

@section('breadcrumbs')
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="/data-pegawai">Pegawai</a></li>
        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Edit</li>
    </ol>
    <h6 class="font-weight-bolder mb-0">Data Pegawai</h6>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Edit Data Pegawai</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    @php
                        $profileData = $user->profile ?? [];
                    @endphp
                    {{-- MONGODB: Gunakan _id --}}
                    <form action="/data-pegawai-update/{{ (string)$user->_id }}" class="row g-3 py-1 px-4" method="post" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        
                        <div class="col-md-6">
                            <label for="inputEmail4" class="form-label">NIP</label>
                            <div class="input-group">
                                <input type="text" onkeypress="return hanyaAngka(event)" name="nip"
                                    class="form-control rounded-3" required 
                                    value="{{ $profileData['nip'] ?? '' }}">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap</label>
                            <div class="input-group">
                                <input type="text" name="nama" class="form-control rounded-3" required 
                                    value="{{ $profileData['nama_lengkap'] ?? $user->nama_lengkap }}">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="inputPassword4" class="form-label">Jenis Kelamin</label>
                            <br>
                            @php $jk = $profileData['jenis_kelamin'] ?? ''; @endphp
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jeniskelamin" value="Laki-laki" 
                                    {{ $jk == 'Laki-laki' || $jk == 'laki-laki' ? 'checked' : '' }}>
                                <label class="form-check-label">Laki-laki</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jeniskelamin" value="Perempuan" 
                                    {{ $jk == 'Perempuan' || $jk == 'perempuan' ? 'checked' : '' }}>
                                <label class="form-check-label">Perempuan</label>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Jabatan</label>
                            <div class="input-group">
                                @php $jabatan = $profileData['jabatan'] ?? ''; @endphp
                                <select class="form-select rounded-3 form-control-lg text-sm" name="jabatan">
                                    <option selected disabled>-- Pilih Jabatan --</option>
                                    <option value="Kepala Sekolah" {{ $jabatan == 'Kepala Sekolah' ? 'selected' : '' }}>Kepala Sekolah</option>
                                    <option value="Wakil Kepala Sekolah" {{ $jabatan == 'Wakil Kepala Sekolah' ? 'selected' : '' }}>Wakil Kepala Sekolah</option>
                                    <option value="Tata Usaha" {{ $jabatan == 'Tata Usaha' ? 'selected' : '' }}>Tata Usaha</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">Tempat Lahir</label>
                            <div class="input-group">
                                <input type="text" name="tempatlahir" class="form-control rounded-3" required 
                                    value="{{ $profileData['tempat_lahir'] ?? '' }}">
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Lahir</label>
                            <div class="input-group">
                                <input type="date" name="tgllahir" class="form-control rounded-3" required 
                                    value="{{ $profileData['tanggal_lahir'] ?? '' }}">
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">No Telepon</label>
                            <div class="input-group">
                                <input type="text" maxlength="13" onkeypress="return hanyaAngka(event)" name="notelp"
                                    class="form-control rounded-3" required 
                                    value="{{ $profileData['no_telp'] ?? '' }}">
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">Agama</label>
                            <div class="input-group">
                                @php $agama = $profileData['agama'] ?? ''; @endphp
                                <select class="form-select rounded-3 form-control-lg text-sm" name="agama">
                                    <option selected disabled>-- Pilih Agama --</option>
                                    <option value="Islam" {{ $agama == 'Islam' || $agama == 'islam' ? 'selected' : '' }}>Islam</option>
                                    <option value="Kristen Protestan" {{ $agama == 'Kristen Protestan' ? 'selected' : '' }}>Kristen Protestan</option>
                                    <option value="Kristen Katolik" {{ $agama == 'Kristen Katolik' ? 'selected' : '' }}>Kristen Katolik</option>
                                    <option value="Hindu" {{ $agama == 'Hindu' || $agama == 'hindu' ? 'selected' : '' }}>Hindu</option>
                                    <option value="Buddha" {{ $agama == 'Buddha' || $agama == 'buddha' ? 'selected' : '' }}>Buddha</option>
                                    <option value="Konghucu" {{ $agama == 'Konghucu' || $agama == 'konghucu' ? 'selected' : '' }}>Konghucu</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="inputPassword4" class="form-label">Alamat</label>
                            <div class="input-group">
                                <textarea name="alamat" class="form-control rounded-3" style="height: 100px" required>{{ $profileData['alamat'] ?? '' }}</textarea>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="formFile" class="form-label">Foto</label>
                            <input class="form-control rounded-3 text-sm" name="foto" type="file"
                                id="file-input-poster" accept="image/*" onchange="showPreviewposter(event);">
                            @php $foto = $profileData['foto'] ?? 'default_img.png'; @endphp
                            <img src="{{ asset('assets/img/pegawai/' . $foto) }}" id="file-preview-poster"
                                alt="..." class="img-thumbnail mt-2" width="50%">
                        </div>
                        
                        <div class="col text-right">
                            <button type="submit" onclick="return confirm('Apakah anda yakin data sudah benar?')"
                                class="btn btn-primary ml-5 text-sm rounded-3" style="float:right;">
                                <i class="fa fa-save"></i> Simpan
                            </button>
                            <a href="/data-pegawai" class="btn btn-danger text-sm rounded-3" style="float: right;margin-right:10px">
                                <i class="fa fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
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
    </script>
@endsection