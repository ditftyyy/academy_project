@extends('components.main')

@section('breadcrumbs')
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="/akademik/jadwal">Jadwal Pelajaran</a></li>
        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Atur</li>
    </ol>
    <h6 class="font-weight-bolder mb-0">Atur Jadwal Pelajaran - {{ $kelas->nama_kelas }}</h6>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <a href="/akademik/jadwal" class="btn btn-secondary rounded-pill font-weight-bold text-xs text-white">
            <i class="material-icons opacity-10">arrow_back</i> Kembali
        </a>
        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                    <h6 class="text-white text-capitalize ps-3">Atur Jadwal - {{ $kelas->nama_kelas }}</h6>
                </div>
            </div>
            <div class="card-body px-0 pb-2">
                <div class="container mt-5">
                    <div class="row">
                        @foreach ($jadwals as $jadwal)
                            @php
                                $hari = $jadwal['hari'];
                                $status = $jadwal['status'] ?? 'masuk';
                                $mataPelajaran = $jadwal['mata_pelajaran'] ?? [];
                            @endphp
                            <div class="col-md-4" style="margin-bottom: 20px">
                                <div class="card">
                                    <div class="card-header bg-info" style="display: flex; justify-content: space-between; align-items: center">
                                        <b>{{ ucfirst($hari) }}</b>
                                        @if (auth()->user()->hasRole('admin'))
                                            <div style="display: flex; column-gap: 10px;">
                                                @if (strtolower($status) == 'libur')
                                                    <form action="" method="POST">
                                                        @csrf
                                                        <button type="submit" name="status" value="masuk"
                                                            class="btn btn-success btn-sm text-sm rounded">Masuk</button>
                                                    </form>
                                                @else
                                                    <button type="button" data-bs-toggle="modal" data-bs-target="#insert-modal"
                                                        class="btn btn-primary btn-sm text-sm rounded"
                                                        onclick="setInsertData('{{ $kelas->_id }}', '{{ $hari }}')">
                                                        <i class="bi bi-plus-square-fill"></i> Tambah
                                                    </button>
                                                    <form action="" method="POST">
                                                        @csrf
                                                        <button type="submit" name="status" value="libur"
                                                            class="btn btn-danger btn-sm text-sm rounded">Liburkan</button>
                                                    </form>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    <div class="card-body">
                                        @if (strtolower($status) == 'libur')
                                            <h5 class="card-title">Libur</h5>
                                        @else
                                            @foreach ($mataPelajaran as $index => $mp)
                                                <div style="border-bottom: 1.5px dashed grey; padding-bottom: 10px; margin-top: 10px">
                                                    <div>
                                                        {{ $mp['jam_mulai'] }} - {{ $mp['jam_selesai'] }}
                                                        <b>({{ $mp['ruang'] }})</b>
                                                    </div>
                                                    <div>
                                                        <b>{{ $mp['mapel'] }}</b>
                                                        <span>({{ $mp['guru'] }})</span>
                                                    </div>
                                                </div>
                                                @if (auth()->user()->hasRole('admin'))
                                                    <div style="display: flex; justify-content: end; column-gap: 10px; margin: 10px 0px">
                                                        <button type="button" data-bs-toggle="modal" data-bs-target="#update-modal"
                                                            class="btn btn-warning btn-sm text-sm rounded"
                                                            onclick="setUpdateData('{{ $kelas->_id }}', '{{ $hari }}', {{ $index }}, '{{ $mp['jam_mulai'] }}', '{{ $mp['jam_selesai'] }}', '{{ $mp['mapel_id'] }}', '{{ $mp['guru_id'] }}', '{{ $mp['ruang'] }}')">
                                                            <i class="fa fa-edit"></i>
                                                        </button>
                                                        <form action="/akademik/jadwal-hapus/{{ $kelas->_id }}/{{ $hari }}/{{ $index }}" method="POST" style="display:inline">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm text-sm rounded"
                                                                onclick="return confirm('Hapus?')"><i class="fa fa-close"></i></button>
                                                        </form>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                        <div class=""><p><b>Catatan : </b>{{ $jadwal['catatan'] ?? '' }}</p></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah --}}
@if (auth()->user()->hasRole('admin'))
<div class="modal fade" id="insert-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary"><h5 class="modal-title text-white">Tambah Jadwal</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <form action="/akademik/jadwal-tambah" method="post">
                    @csrf
                    <input type="hidden" name="id_kelas" id="insert-kelas-id">
                    <input type="hidden" name="hari" id="insert-hari">
                    <div class="mb-3">
                        <label>Mapel</label>
                        <select class="form-select" name="id_mapel" required>
                            <option value="">-- Pilih --</option>
                            @foreach ($mapels as $mapel)
                                <option value="{{ $mapel->_id }}">{{ $mapel->nama_mapel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Guru</label>
                        <select class="form-select" name="id_guru" required>
                            <option value="">-- Pilih --</option>
                            @foreach ($gurus as $guru)
                                <option value="{{ $guru->_id }}">{{ $guru->guru_data['nama'] ?? $guru->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><label>Jam Mulai</label><input type="time" name="jam_mulai" class="form-control" required></div>
                        <div class="col-md-6"><label>Jam Selesai</label><input type="time" name="jam_selesai" class="form-control" required></div>
                    </div>
                    <div class="mb-3">
                        <label>Ruang</label>
                        <select class="form-select" name="id_ruang" required>
                            <option value="">-- Pilih --</option>
                            @foreach ($ruangs as $ruang)
                                <option value="{{ $ruang->_id }}">{{ $ruang->nama_ruang }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal fade" id="update-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary"><h5 class="modal-title text-white">Edit Jadwal</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <form action="/akademik/jadwal-update" method="post">
                    @csrf @method('PUT')
                    <input type="hidden" name="kelas_id" id="edit-kelas-id">
                    <input type="hidden" name="hari" id="edit-hari">
                    <input type="hidden" name="index" id="edit-index">
                    <div class="mb-3">
                        <label>Mapel</label>
                        <select class="form-select" name="mapel" id="edit-mapel" required>
                            <option value="">-- Pilih --</option>
                            @foreach ($mapels as $mapel)
                                <option value="{{ $mapel->_id }}">{{ $mapel->nama_mapel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Guru</label>
                        <select class="form-select" name="guru" id="edit-guru" required>
                            <option value="">-- Pilih --</option>
                            @foreach ($gurus as $guru)
                                <option value="{{ $guru->_id }}">{{ $guru->guru_data['nama'] ?? $guru->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><label>Jam Mulai</label><input type="time" name="jam_mulai" id="edit-jam-mulai" class="form-control" required></div>
                        <div class="col-md-6"><label>Jam Selesai</label><input type="time" name="jam_selesai" id="edit-jam-selesai" class="form-control" required></div>
                    </div>
                    <div class="mb-3">
                        <label>Ruang</label>
                        <select class="form-select" name="ruang" id="edit-ruang" required>
                            <option value="">-- Pilih --</option>
                            @foreach ($ruangs as $ruang)
                                <option value="{{ $ruang->_id }}">{{ $ruang->nama_ruang }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<script>
    function setInsertData(kelasId, hari) {
        document.getElementById('insert-kelas-id').value = kelasId;
        document.getElementById('insert-hari').value = hari;
    }
    function setUpdateData(kelasId, hari, index, jamMulai, jamSelesai, mapelId, guruId, ruang) {
        document.getElementById('edit-kelas-id').value = kelasId;
        document.getElementById('edit-hari').value = hari;
        document.getElementById('edit-index').value = index;
        document.getElementById('edit-jam-mulai').value = jamMulai;
        document.getElementById('edit-jam-selesai').value = jamSelesai;
        document.getElementById('edit-mapel').value = mapelId;
        document.getElementById('edit-guru').value = guruId;
        document.getElementById('edit-ruang').value = ruang;
    }
</script>
@endsection