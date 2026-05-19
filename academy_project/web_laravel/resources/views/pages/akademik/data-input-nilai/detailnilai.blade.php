@extends('components.main')

@section('title-content', 'Detail Nilai')

@section('breadcrumbs')
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Detail Nilai</li>
    </ol>
    <h6 class="font-weight-bolder mb-0">Detail Nilai Siswa - Semester {{ $semester }}</h6>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-6 col-md-6">
            <div class="card z-index-2">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1">
                        <h6 class="text-white text-capitalize ps-3">Detail Nilai - {{ $mapel->nama_mapel ?? 'Mapel' }}</h6>
                    </div>
                </div>
                <div class="card-body">
                    @php
                        // MONGODB: Ambil data
                        $siswaData = $siswa->siswa_data ?? [];
                        $nisn = $siswaData['nisn'] ?? '-';
                        $nama = $siswa->nama_lengkap;
                        $kelasNama = $siswaData['kelas']['nama'] ?? '-';
                        
                        // Nilai yang sudah ada
                        $tugas = $nilai['tugas'] ?? [0,0,0,0,0];
                        $uts = $nilai['uts'] ?? 0;
                        $uas = $nilai['uas'] ?? 0;
                        $nilaiAkhir = $nilai['nilai_akademik'] ?? 0;
                        $nilaiHuruf = $nilai['nilai_huruf'] ?? '-';
                    @endphp

                    <div class="row">
                        <div class="col-md-12">
                            <ul class="list-group">
                                <li class="list-group-item"><div class="row"><div class="col-md-5"><span class="fw-bold">NISN</span><div class="float-end">:</div></div><div class="col-md-7">{{ $nisn }}</div></div></li>
                                <li class="list-group-item"><div class="row"><div class="col-md-5"><span class="fw-bold">Nama</span><div class="float-end">:</div></div><div class="col-md-7">{{ $nama }}</div></div></li>
                                <li class="list-group-item"><div class="row"><div class="col-md-5"><span class="fw-bold">Kelas</span><div class="float-end">:</div></div><div class="col-md-7">{{ $kelasNama }}</div></div></li>
                                <li class="list-group-item"><div class="row"><div class="col-md-5"><span class="fw-bold">Mata Pelajaran</span><div class="float-end">:</div></div><div class="col-md-7">{{ $mapel->nama_mapel ?? '-' }}</div></div></li>
                                
                                @for ($i = 1; $i <= 5; $i++)
                                    <li class="list-group-item"><div class="row"><div class="col-md-5"><span class="fw-bold">Nilai Tugas {{ $i }}</span><div class="float-end">:</div></div><div class="col-md-7">{{ $tugas[$i-1] ?? 0 }}</div></div></li>
                                @endfor
                                
                                <li class="list-group-item"><div class="row"><div class="col-md-5"><span class="fw-bold">Nilai UTS</span><div class="float-end">:</div></div><div class="col-md-7">{{ $uts }}</div></div></li>
                                <li class="list-group-item"><div class="row"><div class="col-md-5"><span class="fw-bold">Nilai UAS</span><div class="float-end">:</div></div><div class="col-md-7">{{ $uas }}</div></div></li>
                                <li class="list-group-item"><div class="row"><div class="col-md-5"><span class="fw-bold">Rata-rata</span><div class="float-end">:</div></div><div class="col-md-7">{{ $nilaiAkhir }} ({{ $nilaiHuruf }})</div></div></li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="col text-right mt-3">
                        <a href="/data-nilai-atur/{{ (string)$kelas->_id }}/{{ (string)$mapel->_id }}/{{ $semester }}"
                            class="btn btn-danger text-sm rounded-3" style="float: right;margin-right:10px">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection