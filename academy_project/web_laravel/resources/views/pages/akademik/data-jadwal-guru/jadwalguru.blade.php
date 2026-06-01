@extends('components.main')
@section('breadcrumbs')
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item text-sm text-dark active">Jadwal Mengajar</li>
    </ol>
    <h6 class="font-weight-bolder mb-0">Jadwal Mengajar</h6>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                    @php
                        $guruNama = auth()->user()->guru_data['nama'] ?? auth()->user()->nama_lengkap;
                        $guruNip  = auth()->user()->guru_data['nip']  ?? '-';
                    @endphp
                    <h6 class="text-white text-capitalize ps-3">
                        Jadwal Mengajar: {{ $guruNama }} ({{ $guruNip }})
                    </h6>
                </div>
            </div>

            <div class="card-body px-0 pb-2">
                <div class="container mt-3">

                    @php
                        $totalSesi = 0;
                        foreach ($jadwalPerHari as $d) { $totalSesi += count($d['mata_pelajaran']); }
                        $hariLabel = [
                            'senin'  => 'Senin',  'selasa' => 'Selasa',
                            'rabu'   => 'Rabu',   'kamis'  => 'Kamis',
                            'jumat'  => 'Jumat',  'sabtu'  => 'Sabtu',
                        ];
                    @endphp

                    @if ($totalSesi === 0)
                        <div class="alert alert-warning text-center mx-2 mb-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Belum ada jadwal mengajar. Hubungi admin untuk mengatur jadwal Anda.
                        </div>
                    @endif

                    <div class="row">
                        @foreach ($hariLabel as $key => $label)
                            @php
                                $data      = $jadwalPerHari[$key] ?? ['status' => 'masuk', 'mata_pelajaran' => []];
                                $status    = $data['status']         ?? 'masuk';
                                $pelajaran = $data['mata_pelajaran'] ?? [];
                                $isHariIni = ($key === $hariIni);
                                $jumlah    = count($pelajaran);
                            @endphp

                            <div class="col-md-4 mb-4">
                                <div class="card h-100 shadow-sm border-0">
                                    <div class="card-header py-2 px-3 {{ $isHariIni ? 'bg-success' : 'bg-primary' }}">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <b class="text-white">
                                                {{ $label }}
                                                @if ($isHariIni)
                                                    <span class="badge bg-warning text-dark ms-1" style="font-size:0.65rem;">HARI INI</span>
                                                @endif
                                            </b>
                                            @if ($status === 'libur')
                                                <span class="badge bg-danger" style="font-size:0.65rem;">LIBUR</span>
                                            @else
                                                <span class="badge bg-white text-primary" style="font-size:0.65rem;">
                                                    {{ $jumlah }} sesi
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="card-body p-2" style="min-height: 80px;">
                                        @if ($status === 'libur')
                                            <div class="text-center py-3 text-muted">
                                                <i class="fas fa-calendar-times fa-lg mb-1"></i>
                                                <div style="font-size:0.85rem;">Libur</div>
                                            </div>
                                        @elseif ($jumlah === 0)
                                            <div class="text-center py-3 text-muted">
                                                <i class="fas fa-calendar-check fa-lg mb-1"></i>
                                                <div style="font-size:0.85rem;">Tidak ada jadwal</div>
                                            </div>
                                        @else
                                            @foreach ($pelajaran as $mp)
                                                <div class="rounded p-2 mb-2" style="background:#f8f9fa; border-left: 3px solid #5e72e4;">
                                                    {{-- Jam --}}
                                                    <div class="mb-1">
                                                        <i class="fas fa-clock text-primary me-1" style="font-size:0.7rem;"></i>
                                                        <small class="fw-bold text-primary">
                                                            {{ $mp['jam_mulai'] }} – {{ $mp['jam_selesai'] }}
                                                        </small>
                                                    </div>
                                                    {{-- Mapel --}}
                                                    <div class="mb-1">
                                                        <i class="fas fa-book text-info me-1" style="font-size:0.7rem;"></i>
                                                        <strong style="font-size:0.85rem;">{{ $mp['mapel'] }}</strong>
                                                    </div>
                                                    {{-- Kelas --}}
                                                    <div class="mb-1">
                                                        <i class="fas fa-chalkboard text-success me-1" style="font-size:0.7rem;"></i>
                                                        <small class="text-dark">{{ $mp['kelas'] }}</small>
                                                    </div>
                                                    {{-- Ruang --}}
                                                    <div>
                                                        <i class="fas fa-map-marker-alt text-warning me-1" style="font-size:0.7rem;"></i>
                                                        <small class="text-muted">{{ $mp['ruang'] }}</small>
                                                    </div>
                                                    {{-- Keterangan jika ada --}}
                                                    @if (!empty($mp['keterangan']))
                                                        <div class="mt-1">
                                                            <i class="fas fa-info-circle text-secondary me-1" style="font-size:0.7rem;"></i>
                                                            <small class="text-muted fst-italic">{{ $mp['keterangan'] }}</small>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        @endif
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