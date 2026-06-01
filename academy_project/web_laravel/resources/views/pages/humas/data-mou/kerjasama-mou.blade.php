@extends('components.main')
@section('title-content', 'Kerja Sama MoU')
@section('breadcrumbs')
<ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="/mou">Data Kerja Sama</a></li>
</ol>
<h6 class="font-weight-bolder mb-0">Kerja Sama</h6>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                    <h6 class="text-white text-capitalize ps-3">Tambah Mitra Kerja Sama</h6>
                </div>
            </div>
            <div class="card-body px-0 pb-2">
                <form action="/add-mou" method="post" enctype="multipart/form-data" class="row g-3 py-1 px-4">
                    @csrf
                    <div class="col-md-6">
                        <label class="form-label">Nama Mitra</label>
                        <input type="text" name="nama_mitra" class="form-control" required value="{{ old('nama_mitra') }}">
                        @error('nama_mitra') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">PT Mitra</label>
                        <input type="text" name="pt_mitra" class="form-control" required value="{{ old('pt_mitra') }}">
                        @error('pt_mitra') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Asal Mitra atau Instansi</label>
                        <input type="text" name="asal_mitra" class="form-control" required value="{{ old('asal_mitra') }}">
                        @error('asal_mitra') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Deskripsi Singkat Mitra</label>
                        <textarea name="deskripsi_singkat_mitra" class="form-control" rows="3" required>{{ old('deskripsi_singkat_mitra') }}</textarea>
                        @error('deskripsi_singkat_mitra') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">File</label>
                        <small class="text-muted d-block">Keterangan : Silahkan upload file dalam bentuk doc, docx, atau pdf</small>
                        <input class="form-control mt-1" name="file_mitra" type="file" accept=".doc,.docx,.pdf" required>
                        <span id="file-error" class="text-danger"></span>
                        @error('file_mitra') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Mulai Kerjasama</label>
                        <input type="date" name="tgl_mulai_kerjasama" class="form-control" id="tgl_mulai" required value="{{ old('tgl_mulai_kerjasama') }}">
                        @error('tgl_mulai_kerjasama') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Berakhir Kerjasama</label>
                        <input type="date" name="tgl_berakhir_kerjasama" class="form-control" id="tgl_berakhir" required value="{{ old('tgl_berakhir_kerjasama') }}">
                        @error('tgl_berakhir_kerjasama') <span class="text-danger">{{ $message }}</span> @enderror
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
    const tglMulai = document.getElementById('tgl_mulai');
    const tglBerakhir = document.getElementById('tgl_berakhir');
    function validateDates() {
        if (tglMulai.value && tglBerakhir.value && new Date(tglMulai.value) >= new Date(tglBerakhir.value)) {
            alert('Tanggal Mulai harus sebelum Tanggal Berakhir!');
            tglBerakhir.value = '';
        }
    }
    tglMulai.addEventListener('change', validateDates);
    tglBerakhir.addEventListener('change', validateDates);
</script>
@endsection