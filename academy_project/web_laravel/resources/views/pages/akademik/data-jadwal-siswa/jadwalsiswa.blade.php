@extends('components.main')
@section('breadcrumbs')
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Jadwal Pelajaran</li>
    </ol>
    <h6 class="font-weight-bolder mb-0">Jadwal Pelajaran - {{ $kelas->nama_kelas }}</h6>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Jadwal Kelas</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="container mt-5">
                        <div class="row">
                            @foreach ($jadwals as $jadwal)
                                @php
                                    $hari = $jadwal['hari'] ?? '';
                                    $mataPelajaran = $jadwal['mata_pelajaran'] ?? [];
                                @endphp
                                <div class="col-md-4" style="margin-bottom: 20px">
                                    <div class="card">
                                        <div class="card-header {{ $hari == $hari_ini ? 'bg-success' : 'bg-primary' }}"
                                            style="display: flex; justify-content: space-between; align-items: center">
                                            <b style="color: white">{{ ucfirst($hari) }}{{ $hari == $hari_ini ? ' - Hari ini' : '' }}</b>
                                        </div>
                                        <div class="card-body">
                                            @forelse ($mataPelajaran as $mp)
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
                                            @empty
                                                <p class="text-center text-muted">Libur / Tidak ada jadwal</p>
                                            @endforelse
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
@endsection