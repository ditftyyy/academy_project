@extends('components.main')

@section('breadcrumbs')
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="/data-jadwal-cek">Data Kelas</a></li>
        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Jadwal</li>
    </ol>
    <h6 class="font-weight-bolder mb-0">Cek Jadwal - {{ $kelas->nama_kelas ?? '' }}</h6>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <a href="/data-jadwal-cek" class="btn btn-secondary rounded-pill font-weight-bold text-xs text-white">
                <i class="material-icons opacity-10">arrow_back</i> Kembali
            </a>
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Jadwal Pelajaran Kelas : {{ $kelas->nama_kelas ?? '' }}</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive pb-2 px-3">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Hari</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Jam</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Mata Pelajaran</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Guru</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Ruang</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- MONGODB: $jadwal adalah array yang dikirim controller, setiap elemen punya key: hari, jam_mulai, jam_selesai, mapel, guru, ruang, keterangan, mapel_id, guru_id, ruang_id --}}
                                @forelse($jadwal as $index => $j)
                                    <tr>
                                        <td class="text-center">{{ ucfirst($j['hari']) }}</td>
                                        <td class="text-center">{{ $j['jam_mulai'] }} - {{ $j['jam_selesai'] }}</td>
                                        <td class="text-center">{{ $j['mapel'] }}</td>
                                        <td class="text-center">{{ $j['guru'] }}</td>
                                        <td class="text-center">{{ $j['ruang'] }}</td>
                                        <td class="text-center">{{ $j['keterangan'] ?? '-' }}</td>
                                    </tr>

                                    {{-- Modal Edit per jadwal --}}
                                    <div class="modal fade" id="edit-modal{{ $index }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary">
                                                    <h5 class="modal-title text-white">Edit Jadwal</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="/data-jadwal-update/{{ $kelas->_id }}/{{ $j['hari'] }}/{{ $index }}" method="post">
                                                        @csrf @method('PUT')
                                                        <div class="row">
                                                            <div class="col-md-6 pb-2">
                                                                <label>Mapel</label>
                                                                <select class="form-select" name="mapel_id">
                                                                    <option value="">-- Pilih --</option>
                                                                    @foreach($mapel as $item)
                                                                        {{-- MONGODB: $item->_id --}}
                                                                        <option value="{{ $item->_id }}" {{ ($j['mapel_id'] ?? '') == $item->_id ? 'selected' : '' }}>
                                                                            {{ $item->nama_mapel }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6 pb-2">
                                                                <label>Guru</label>
                                                                <select class="form-select" name="guru_id">
                                                                    <option value="">-- Pilih --</option>
                                                                    @foreach($guru as $item)
                                                                        <option value="{{ $item->_id }}" {{ ($j['guru_id'] ?? '') == $item->_id ? 'selected' : '' }}>
                                                                            {{ $item->guru_data['nama'] ?? $item->nama_lengkap }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6 pb-2">
                                                                <label>Jam Mulai</label>
                                                                <input type="time" name="jam_mulai" class="form-control" value="{{ $j['jam_mulai'] }}">
                                                            </div>
                                                            <div class="col-md-6 pb-2">
                                                                <label>Jam Selesai</label>
                                                                <input type="time" name="jam_selesai" class="form-control" value="{{ $j['jam_selesai'] }}">
                                                            </div>
                                                            <div class="col-md-6 pb-2">
                                                                <label>Ruang</label>
                                                                <select class="form-select" name="ruang_id">
                                                                    <option value="">-- Pilih --</option>
                                                                    @foreach($ruangs as $ruang)
                                                                        <option value="{{ $ruang->_id }}" {{ ($j['ruang_id'] ?? '') == $ruang->_id ? 'selected' : '' }}>
                                                                            {{ $ruang->nama_ruang }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6 pb-2">
                                                                <label>Keterangan</label>
                                                                <input type="text" name="keterangan" class="form-control" value="{{ $j['keterangan'] ?? '' }}">
                                                            </div>
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
                                @empty
                                    <tr><td colspan="6" class="text-center">Belum ada jadwal untuk kelas ini.</td></tr>
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
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white">Tambah Jadwal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="/data-jadwal-insert" method="post">
                        @csrf
                        <input type="hidden" name="kelas_id" value="{{ $kelas->_id }}">
                        <div class="row">
                            <div class="col-md-6 pb-2">
                                <label>Hari</label>
                                <select class="form-select" name="hari" required>
                                    <option value="">-- Pilih --</option>
                                    @foreach(['senin','selasa','rabu','kamis','jumat','sabtu'] as $day)
                                        <option value="{{ $day }}">{{ ucfirst($day) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 pb-2">
                                <label>Mapel</label>
                                <select class="form-select" name="mapel_id">
                                    <option value="">-- Pilih --</option>
                                    @foreach($mapel as $item)
                                        <option value="{{ $item->_id }}">{{ $item->nama_mapel }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 pb-2">
                                <label>Jam Mulai</label>
                                <input type="time" name="jam_mulai" class="form-control" required>
                            </div>
                            <div class="col-md-6 pb-2">
                                <label>Jam Selesai</label>
                                <input type="time" name="jam_selesai" class="form-control" required>
                            </div>
                            <div class="col-md-6 pb-2">
                                <label>Guru</label>
                                <select class="form-select" name="guru_id">
                                    <option value="">-- Pilih --</option>
                                    @foreach($guru as $item)
                                        <option value="{{ $item->_id }}">{{ $item->guru_data['nama'] ?? $item->nama_lengkap }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 pb-2">
                                <label>Ruang</label>
                                <select class="form-select" name="ruang_id">
                                    <option value="">-- Pilih --</option>
                                    @foreach($ruangs as $ruang)
                                        <option value="{{ $ruang->_id }}">{{ $ruang->nama_ruang }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 pb-2">
                                <label>Keterangan</label>
                                <input type="text" name="keterangan" class="form-control">
                            </div>
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
@endsection