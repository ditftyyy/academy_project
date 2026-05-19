@extends('components.main')
@section('title-content','Data Jadwal')
@section('breadcrumbs')
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="/data-jadwalmengajar">Jadwal Guru</a></li>
        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Atur</li>
    </ol>
    <h6 class="font-weight-bolder mb-0">Atur Jadwal Guru</h6>
@endsection
@section('content')
    @php
        $guruNama = $guru->guru_data['nama'] ?? $guru->nama_lengkap;
        $guruNip = $guru->guru_data['nip'] ?? '-';
    @endphp
    <div class="row">
        <div class="col-12">
            <a href="/data-jadwalmengajar" class="btn btn-secondary rounded-pill font-weight-bold text-xs text-white">
                <i class="material-icons opacity-10">arrow_back</i> Kembali
            </a>
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Atur Jadwal: {{ $guruNama }} ({{ $guruNip }})</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive pb-2 px-3">
                        <div class="col text-right mb-2">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                <i class="material-icons opacity-10">add</i> Tambah
                            </button>
                            <a href="/data-jadwalmengajar/cetak_pdf/{{ $guru->_id }}" target="_blank" class="btn btn-sm text-white" style="background-color:rgb(167,72,255);">
                                <i class="material-icons opacity-10">print</i> Cetak
                            </a>
                        </div>
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center">Hari</th><th class="text-center">Jam</th>
                                    <th class="text-center">Kelas</th><th class="text-center">Ruang</th>
                                    <th class="text-center">Keterangan</th><th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($jadwal as $index => $j)
                                    <tr>
                                        <td class="text-center">{{ ucfirst($j['hari']) }}</td>
                                        <td class="text-center">{{ $j['jam_mulai'] }} - {{ $j['jam_selesai'] }}</td>
                                        <td class="text-center">{{ $j['kelas'] ?? '-' }}</td>
                                        <td class="text-center">{{ $j['ruang'] ?? '-' }}</td>
                                        <td class="text-center">{{ $j['keterangan'] ?? '-' }}</td>
                                        <td class="text-center">
                                            <button type="button" data-bs-toggle="modal" data-bs-target="#edit-modal{{ $index }}" class="btn btn-warning btn-sm rounded"><i class="fa fa-edit"></i></button>
                                            <form action="/data-jadwalmengajar-hapus/{{ $guru->_id }}/{{ $j['hari'] }}/{{ $index }}" method="post" style="display:inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm rounded" onclick="return confirm('Hapus?')"><i class="fa fa-close"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    {{-- Modal Edit --}}
                                    <div class="modal fade" id="edit-modal{{ $index }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary"><h5 class="modal-title text-white">Edit Jadwal</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                                <div class="modal-body">
                                                    <form action="/data-jadwalmengajar-update/{{ $guru->_id }}/{{ $j['hari'] }}/{{ $index }}" method="post">
                                                        @csrf @method('PUT')
                                                        <div class="row">
                                                            <div class="col-md-6"><label>Hari</label>
                                                                <select class="form-select" name="hari">
                                                                    @foreach(['senin','selasa','rabu','kamis','jumat','sabtu'] as $d)
                                                                        <option value="{{ $d }}" {{ $j['hari']==$d?'selected':'' }}>{{ ucfirst($d) }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6"><label>Kelas</label>
                                                                <select class="form-select" name="kelas_id">
                                                                    <option value="">-- Pilih --</option>
                                                                    @foreach($kelas_list as $k)
                                                                        <option value="{{ $k->_id }}" {{ ($j['kelas_id']??'')==$k->_id?'selected':'' }}>{{ $k->nama_kelas }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6"><label>Jam Mulai</label><input type="time" name="jam_mulai" class="form-control" value="{{ $j['jam_mulai'] }}"></div>
                                                            <div class="col-md-6"><label>Jam Selesai</label><input type="time" name="jam_selesai" class="form-control" value="{{ $j['jam_selesai'] }}"></div>
                                                            <div class="col-md-6"><label>Ruang</label>
                                                                <select class="form-select" name="ruang_id">
                                                                    @foreach($ruangs as $r)
                                                                        <option value="{{ $r->_id }}" {{ ($j['ruang_id']??'')==$r->_id?'selected':'' }}>{{ $r->nama_ruang }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6"><label>Keterangan</label><input type="text" name="keterangan" class="form-control" value="{{ $j['keterangan']??'' }}"></div>
                                                        </div>
                                                        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button><button type="submit" class="btn btn-primary">Simpan</button></div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr><td colspan="6" class="text-center">Belum ada jadwal.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Tambah --}}
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary"><h5 class="modal-title text-white">Tambah Jadwal</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <form action="/data-jadwalmengajar-insert" method="post">
                        @csrf
                        <input type="hidden" name="guru_id" value="{{ $guru->_id }}">
                        <div class="row">
                            <div class="col-md-6"><label>Hari</label><select class="form-select" name="hari" required><option value="">-- Pilih --</option>@foreach(['senin','selasa','rabu','kamis','jumat','sabtu'] as $d)<option value="{{ $d }}">{{ ucfirst($d) }}</option>@endforeach</select></div>
                            <div class="col-md-6"><label>Kelas</label><select class="form-select" name="kelas_id"><option value="">-- Pilih --</option>@foreach($kelas_list as $k)<option value="{{ $k->_id }}">{{ $k->nama_kelas }}</option>@endforeach</select></div>
                            <div class="col-md-6"><label>Jam Mulai</label><input type="time" name="jam_mulai" class="form-control" required></div>
                            <div class="col-md-6"><label>Jam Selesai</label><input type="time" name="jam_selesai" class="form-control" required></div>
                            <div class="col-md-6"><label>Ruang</label><select class="form-select" name="ruang_id">@foreach($ruangs as $r)<option value="{{ $r->_id }}">{{ $r->nama_ruang }}</option>@endforeach</select></div>
                            <div class="col-md-6"><label>Keterangan</label><input type="text" name="keterangan" class="form-control"></div>
                        </div>
                        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button><button type="submit" class="btn btn-primary">Simpan</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection