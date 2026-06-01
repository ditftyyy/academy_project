@extends('components.main')
@section('breadcrumbs')
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="/mou">Kerjasama</a></li>
        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Edit</li>
    </ol>
    <h6 class="font-weight-bolder mb-0">Data Kerjasama</h6>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Edit Data Kerjasama</h6>
                    </div>
                </div>
                @php
                    $dataTambahan = $mou->data_tambahan ?? [];
                @endphp
                <div class="card-body px-0 pb-2">
                    <form action="/edit-mou/{{ $mou->_id }}" class="row g-3 py-1 px-4" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="col-md-6">
                            <label class="form-label" for="nama_mitra">Nama Mitra</label>
                            <input type="text" name="nama_mitra" class="form-control" id="nama_mitra" required 
                                   value="{{ old('nama_mitra', $dataTambahan['nama_mitra'] ?? '') }}">
                            @error('nama_mitra') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="asal_mitra">Asal Mitra atau Instansi</label>
                            <input type="text" name="asal_mitra" class="form-control" id="asal_mitra" required
                                   value="{{ old('asal_mitra', $dataTambahan['asal_mitra'] ?? '') }}">
                            @error('asal_mitra') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="deskripsi_singkat_mitra">Deskripsi singkat Mitra</label>
                            <textarea name="deskripsi_singkat_mitra" class="form-control" rows="3" required>{{ old('deskripsi_singkat_mitra', $mou->message ?? '') }}</textarea>
                            @error('deskripsi_singkat_mitra') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="tgl_mulai_kerjasama">Tanggal Mulai Kerjasama</label>
                            <input type="date" name="tgl_mulai_kerjasama" class="form-control" id="tgl_mulai" required
                                   value="{{ old('tgl_mulai_kerjasama', $dataTambahan['tanggal_mulai'] ?? '') }}">
                            @error('tgl_mulai_kerjasama') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="tgl_berakhir_kerjasama">Tanggal Berakhir Kerjasama</label>
                            <input type="date" name="tgl_berakhir_kerjasama" class="form-control" id="tgl_berakhir" required
                                   value="{{ old('tgl_berakhir_kerjasama', $dataTambahan['tanggal_berakhir'] ?? '') }}">
                            @error('tgl_berakhir_kerjasama') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="pt_mitra">PT Mitra</label>
                            <input type="text" name="pt_mitra" class="form-control" id="pt_mitra" required
                                   value="{{ old('pt_mitra', $dataTambahan['pt_mitra'] ?? '') }}">
                            @error('pt_mitra') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">File</label>
                            @if(!empty($dataTambahan['file']))
                                <div class="mb-2">
                                    <a href="{{ asset('storage/kerjasama/file/'.$dataTambahan['file']) }}" target="_blank" class="btn btn-sm btn-info">
                                        {{ $dataTambahan['original_name_file'] ?? 'Lihat File Saat Ini' }}
                                    </a>
                                </div>
                            @endif
                            <div class="text-muted small">
                                <p class="mb-0">Keterangan:</p>
                                <p class="mb-0">- Jika terdapat perubahan pada file sebelumnya silahkan upload ulang file</p>
                                <p class="mb-0">- Silahkan upload file dalam bentuk doc, docx atau pdf</p>
                            </div>
                            <input class="form-control mt-2" name="file_mitra" type="file" accept=".doc,.docx,.pdf">
                            @error('file_mitra') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="card-footer d-flex justify-content-end" style="gap: 10px">
                            <a href="/mou" class="btn btn-danger text-sm rounded-3"><i class="fa fa-arrow-left"></i> Kembali</a>
                            <button type="submit" onclick="return confirm('Apakah anda yakin data sudah benar?')" class="btn btn-primary text-sm rounded-3"><i class="fa fa-save"></i> Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        var tglMulaiInput = document.getElementById('tgl_mulai');
        var tglBerakhirInput = document.getElementById('tgl_berakhir');
        var lastValidTglMulai = tglMulaiInput.value;
        var lastValidTglBerakhir = tglBerakhirInput.value;

        tglMulaiInput.addEventListener('change', validateDates);
        tglBerakhirInput.addEventListener('change', validateDates);

        function validateDates() {
            var tglMulai = new Date(tglMulaiInput.value);
            var tglBerakhir = new Date(tglBerakhirInput.value);
            if (tglMulai >= tglBerakhir) {
                alert('Tanggal Mulai Kerjasama harus sebelum Tanggal Berakhir Kerjasama.');
                tglBerakhirInput.value = lastValidTglBerakhir;
            } else {
                lastValidTglMulai = tglMulaiInput.value;
                lastValidTglBerakhir = tglBerakhirInput.value;
            }
        }
    </script>
@endsection