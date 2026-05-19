@extends('components.main')

@section('title-content', 'Input Nilai Siswa')

@section('breadcrumbs')
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Input Nilai</li>
    </ol>
    <h6 class="font-weight-bolder mb-0">Form Input Nilai</h6>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-secondary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Input Nilai - Semester {{ $semester }}</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    @php
                        // MONGODB: Ambil data dari model
                        $siswaData = $siswa->siswa_data ?? [];
                        $siswaId = (string)$siswa->_id;
                        $mapelId = (string)$mapel->_id;
                        $kelasId = (string)$kelas->_id;
                        
                        // Cari nilai yang sudah ada untuk mapel ini
                        $nilaiSaatIni = null;
                        if (isset($siswa->academic_records)) {
                            foreach ($siswa->academic_records as $record) {
                                if (($record['semester'] ?? '') === $semester) {
                                    foreach ($record['nilai'] ?? [] as $n) {
                                        if (($n['mapel_id'] ?? '') === $mapelId || ($n['mapel'] ?? '') === ($mapel->nama_mapel ?? '')) {
                                            $nilaiSaatIni = $n;
                                            break 2;
                                        }
                                    }
                                }
                            }
                        }
                        
                        // Nilai default
                        $tugas = $nilaiSaatIni['tugas'] ?? [0,0,0,0,0];
                        $uts = $nilaiSaatIni['uts'] ?? 0;
                        $uas = $nilaiSaatIni['uas'] ?? 0;
                    @endphp

                    <form action="/data-input-nilai-siswa/{{ $kelasId }}/{{ $siswaId }}/{{ $mapelId }}/{{ $semester }}"
                        class="row g-3 py-1 px-4" method="post" enctype="multipart/form-data">
                        @csrf
                        {{-- Hidden fields --}}
                        <input type="hidden" name="siswa_id" value="{{ $siswaId }}">
                        <input type="hidden" name="kelas_id" value="{{ $kelasId }}">
                        <input type="hidden" name="mapel_id" value="{{ $mapelId }}">
                        <input type="hidden" name="guru_id" value="{{ auth()->id() }}">
                        <input type="hidden" name="semester" value="{{ $semester }}">

                        {{-- Info Siswa --}}
                        <div class="col-md-3">
                            <label class="form-label">NISN</label>
                            <input type="text" class="form-control rounded-3" value="{{ $siswaData['nisn'] ?? '-' }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control rounded-3" value="{{ $siswa->nama_lengkap }}" readonly>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Kelas</label>
                            <input type="text" class="form-control rounded-3" value="{{ $kelas->nama_kelas ?? '-' }}" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Mata Pelajaran</label>
                            <input type="text" class="form-control rounded-3" value="{{ $mapel->nama_mapel ?? '-' }}" readonly>
                        </div>

                        {{-- Nilai Tugas 1-5 --}}
                        @for ($i = 1; $i <= 5; $i++)
                            <div class="col-md-3">
                                <label class="form-label">Nilai Tugas {{ $i }}</label>
                                <input type="number" name="tugas{{ $i }}" class="form-control rounded-3"
                                    value="{{ $tugas[$i-1] ?? 0 }}" min="0" max="100">
                            </div>
                        @endfor

                        {{-- Nilai UTS dan UAS --}}
                        <div class="col-md-3">
                            <label class="form-label">Nilai UTS</label>
                            <input type="number" name="uts" class="form-control rounded-3"
                                value="{{ $uts }}" min="0" max="100">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Nilai UAS</label>
                            <input type="number" name="uas" class="form-control rounded-3"
                                value="{{ $uas }}" min="0" max="100">
                        </div>

                        <div class="col-12 text-right">
                            <button type="submit" onclick="return confirm('Apakah anda yakin data sudah benar?')"
                                class="btn btn-primary ml-5 text-sm rounded-3" style="float:right;">
                                <i class="fa fa-save"></i> Simpan
                            </button>
                            <a href="/data-nilai-atur/{{ $kelasId }}/{{ $mapelId }}/{{ $semester }}"
                                class="btn btn-danger text-sm rounded-3" style="float: right; margin-right:10px">
                                <i class="fa fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection