@extends('components.main')

@section('title-content','Data Raport')
@section('breadcrumbs')
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="/data-raport">Data Raport</a></li>
        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Nilai</li>
    </ol>
    <h6 class="font-weight-bolder mb-0">Input nilai </h6>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-6">
            <a href="/data-raport" class="btn btn-secondary btn-sm rounded-pill"><i class="material-icons opacity-10">arrow_back</i> Kembali</a>
        </div>
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-violet shadow-secondary border-radius-lg pt-4 pb-3" style="background-color: #fff;">
                        @php
                            $siswaData = $siswa->siswa_data ?? [];
                            $nama = $siswaData['nama'] ?? $siswa->nama_lengkap ?? '';
                            $nisn = $siswaData['nisn'] ?? '-';
                            $kelasNama = $siswaData['kelas']['nama'] ?? '-';
                        @endphp
                        <h6 class="text-black text-capitalize ps-3">
                            NISN : {{ $nisn }}<br>
                            Nama : {{ $nama }}<br>
                            Kelas : {{ $kelasNama }}<br>
                            Semester : {{ $semester == '1' ? '1/Ganjil' : '2/Genap' }}
                        </h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive pb-2 px-3">
                        <table class="table table-bordered">
                            <thead class="table-primary">
                                <tr>
                                    <th class="text-center">No.</th>
                                    <th class="text-center">Mata Pelajaran</th>
                                    <th class="text-center">Nilai Angka</th>
                                    <th class="text-center">Nilai Huruf</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- $raport adalah array nilai dari academic_records --}}
                                @forelse ($raport as $nilai)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td class="text-center">{{ $nilai['mapel'] ?? '-' }}</td>
                                        <td class="text-center">{{ $nilai['nilai_akademik'] ?? 0 }}</td>
                                        <td class="text-center">{{ $nilai['nilai_huruf'] ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center">Belum ada nilai.</td></tr>
                                @endforelse
                            </tbody>
                        </table>

                        <hr>
                        <form action="/data-raport-insert" class="row g-3 px-4" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="siswa_id" value="{{ $siswa->_id }}">
                            <input type="hidden" name="semester" value="{{ $semester }}">

                            <div class="row">
                                <div class="col-lg-12 col-md-8 col-sm-12">
                                    <div class="form-group row pt-3">
                                        <label class="col-2 text-end control-label col-form-label">Sakit</label>
                                        <div class="col-lg-2 col-md-4 col-sm-4">
                                            <input type="number" name="sakit" class="form-control" value="{{ $raport_ket ?? 0 }}">
                                        </div>
                                    </div>
                                    <div class="form-group row pt-2">
                                        <label class="col-2 text-end control-label col-form-label">Izin</label>
                                        <div class="col-lg-2 col-md-4 col-sm-4">
                                            <input type="number" name="ijin" class="form-control" value="{{ $raport_ket2 ?? 0 }}">
                                        </div>
                                    </div>
                                    <div class="form-group row pt-2">
                                        <label class="col-2 text-end control-label col-form-label">Tanpa Ket</label>
                                        <div class="col-lg-2 col-md-4 col-sm-4">
                                            <input type="number" name="tanpa_ket" class="form-control" value="{{ $raport_ket3 ?? 0 }}">
                                        </div>
                                    </div>
                                    @if ($semester == 2)
                                        <div class="form-group row pt-2">
                                            <label class="col-2 text-end control-label col-form-label">Status Kenaikan</label>
                                            <div class="col-lg-2 col-md-4 col-sm-4">
                                                <select class="form-select" name="status">
                                                    <option value="">-- Pilih --</option>
                                                    <option value="naik">Naik</option>
                                                    <option value="tidaknaik">Tidak Naik</option>
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-right pt-2">
                                    <button type="submit" class="btn btn-primary btn-sm" style="float:right"><i class="material-icons opacity-10">print</i> Simpan dan Cetak</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection