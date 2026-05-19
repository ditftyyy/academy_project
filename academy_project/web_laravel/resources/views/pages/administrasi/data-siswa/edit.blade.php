@extends('components.main')

@section('breadcrumbs')
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="/administrasi/siswa">Siswa</a></li>
        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Edit</li>
    </ol>
    <h6 class="font-weight-bolder mb-0">Data Siswa</h6>
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
            const asalSekolah = document.getElementById('asal_sekolah');
            function setAsalSekolahVisibility() {
                asalSekolah.style.display = (statusSelect.value === 'pindahan') ? 'block' : 'none';
            }
            setAsalSekolahVisibility();
            statusSelect.addEventListener('change', setAsalSekolahVisibility);
        });
    </script>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Edit Data Siswa</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    @php
                        // MONGODB: Ambil data dari siswa_data
                        $siswaData = $siswa->siswa_data ?? [];
                        $profileData = $siswa->profile ?? [];
                        
                        $nis = $siswaData['nis'] ?? '';
                        $nisn = $siswaData['nisn'] ?? '';
                        $nik = $siswaData['nik'] ?? '';
                        $nama = $siswaData['nama'] ?? $profileData['nama_lengkap'] ?? '';
                        $tempatLahir = $siswaData['tempat_lahir'] ?? '';
                        $tanggalLahir = $siswaData['tanggal_lahir'] ?? '';
                        $jenisKelamin = $siswaData['jenis_kelamin'] ?? $profileData['jenis_kelamin'] ?? '';
                        $agama = $siswaData['agama'] ?? $profileData['agama'] ?? '';
                        $noTelp = $siswaData['no_telp'] ?? $profileData['no_telp'] ?? '';
                        $alamat = $siswaData['alamat'] ?? $profileData['alamat'] ?? '';
                        $foto = $siswaData['foto'] ?? $profileData['foto'] ?? 'default_img.png';
                        $status = $siswaData['status'] ?? 'bukan pindahan';
                        $sekolahAsal = $siswaData['asal_sekolah'] ?? $siswaData['sekolah_asal'] ?? '';
                        $orangTua = $siswaData['orang_tua'] ?? [];
                        $kelasSiswa = $siswaData['kelas'] ?? [];
                        $idKelas = $kelasSiswa['id'] ?? '';
                    @endphp
                    
                    {{-- MONGODB: Gunakan _id --}}
                    <form action="/administrasi/siswa-update/{{ (string)$siswa->_id }}" class="row g-3 py-1 px-4" method="post" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        
                        <div class="col-md-6">
                            <label for="nis" class="form-label">NIS</label>
                            <div class="input-group">
                                <input type="text" onkeypress="return hanyaAngka(event)" name="nis"
                                    class="form-control rounded-3" required value="{{ $nis }}">
                            </div>
                            @if ($errors->has('nis'))
                                <span class="text-danger">{{ $errors->first('nis') }}</span>
                            @endif
                        </div>
                        
                        <div class="col-md-6">
                            <label for="nisn" class="form-label">NISN</label>
                            <div class="input-group">
                                <input type="text" onkeypress="return hanyaAngka(event)" name="nisn"
                                    class="form-control rounded-3" required value="{{ $nisn }}">
                            </div>
                            @if ($errors->has('nisn'))
                                <span class="text-danger">{{ $errors->first('nisn') }}</span>
                            @endif
                        </div>
                        
                        <div class="col-md-6">
                            <label for="nik" class="form-label">NIK</label>
                            <div class="input-group">
                                <input type="text" onkeypress="return hanyaAngka(event)" name="nik"
                                    class="form-control rounded-3" required value="{{ $nik }}">
                            </div>
                            @if ($errors->has('nik'))
                                <span class="text-danger">{{ $errors->first('nik') }}</span>
                            @endif
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap</label>
                            <div class="input-group">
                                <input type="text" name="nama" class="form-control rounded-3" required value="{{ $nama }}">
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">Tempat Lahir</label>
                            <div class="input-group">
                                <input type="text" name="tempat_lahir" class="form-control rounded-3" required value="{{ $tempatLahir }}">
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Lahir</label>
                            <div class="input-group">
                                <input type="date" name="tanggal_lahir" class="form-control rounded-3" required value="{{ $tanggalLahir }}">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Jenis Kelamin</label>
                            <br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jenis_kelamin" value="laki-laki"
                                    {{ $jenisKelamin == 'laki-laki' ? 'checked' : '' }}>
                                <label class="form-check-label">Laki-laki</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jenis_kelamin" value="perempuan"
                                    {{ $jenisKelamin == 'perempuan' ? 'checked' : '' }}>
                                <label class="form-check-label">Perempuan</label>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Agama</label>
                            <div class="input-group">
                                <select class="form-select rounded-3 form-control-lg text-sm" name="agama">
                                    <option selected disabled>-- Pilih Agama --</option>
                                    @foreach (['islam', 'kristen', 'hindu', 'buddha', 'konghucu'] as $ag)
                                        <option value="{{ $ag }}" {{ $agama == $ag ? 'selected' : '' }}>{{ ucfirst($ag) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Nama Ayah</label>
                            <div class="input-group">
                                <input type="text" name="nama_ayah" class="form-control rounded-3" required value="{{ $orangTua['nama_ayah'] ?? '' }}">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Nama Ibu</label>
                            <div class="input-group">
                                <input type="text" name="nama_ibu" class="form-control rounded-3" required value="{{ $orangTua['nama_ibu'] ?? '' }}">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Nama Wali</label>
                            <div class="input-group">
                                <input type="text" name="nama_wali" class="form-control rounded-3" required value="{{ $orangTua['nama_wali'] ?? '' }}">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Kelas</label>
                            <div class="input-group">
                                <select class="form-select rounded-3 form-control-lg text-sm" name="kelas" id="kelas">
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach ($kelas_list as $kelas)
                                        {{-- MONGODB: $kelas->_id --}}
                                        <option value="{{ (string)$kelas->_id }}" {{ $idKelas == (string)$kelas->_id ? 'selected' : '' }}>
                                            {{ $kelas->nama_kelas }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">No Telepon</label>
                            <div class="input-group">
                                <input type="text" maxlength="13" onkeypress="return hanyaAngka(event)" name="no_telp"
                                    class="form-control rounded-3" required value="{{ $noTelp }}">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <div class="input-group">
                                <select class="form-select rounded-3 form-control-lg text-sm" name="status" id="status">
                                    <option value="">-- Pilih Status --</option>
                                    @foreach ($status_siswa as $st)
                                        <option value="{{ $st }}" {{ $status == $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6" id="asal_sekolah" style="{{ $status == 'pindahan' ? 'display: block;' : 'display: none;' }}">
                            <label class="form-label">Asal Sekolah</label>
                            <input type="text" class="form-control rounded-3" name="asal_sekolah" value="{{ $sekolahAsal }}">
                        </div>
                        
                        <div class="col-md-6">
                            <label for="formFile" class="form-label">Foto</label>
                            <input class="form-control rounded-3 text-sm" name="foto" type="file"
                                id="file-input-poster" accept="image/*" onchange="showPreviewposter(event);">
                            <img src="{{ asset('storage/murid/img/' . $foto) }}" id="file-preview-poster"
                                alt="..." class="img-thumbnail mt-2" width="50%">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Alamat</label>
                            <div class="input-group">
                                <textarea name="alamat" class="form-control rounded-3" style="height: 100px" required>{{ $alamat }}</textarea>
                            </div>
                        </div>
                        
                        <div class="text-right card-footer">
                            <button type="submit" onclick="return confirm('Apakah anda yakin data sudah benar?')"
                                class="btn btn-primary ml-5 text-sm rounded-3" style="float:right;">
                                <i class="fa fa-save"></i> Simpan
                            </button>
                            <a href="/administrasi/siswa" class="btn btn-danger text-sm rounded-3" style="float: right;margin-right:10px">
                                <i class="fa fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection