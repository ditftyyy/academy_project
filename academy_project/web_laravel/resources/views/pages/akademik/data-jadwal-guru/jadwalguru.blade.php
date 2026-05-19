@extends('components.main')
@section('breadcrumbs')
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Jadwal Mengajar</li>
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
                            $guruNip = auth()->user()->guru_data['nip'] ?? '-';
                        @endphp
                        <h6 class="text-white text-capitalize ps-3">Jadwal Hari Ini: {{ $guruNama }} ({{ $guruNip }})</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive pb-2 px-3">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Hari</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Mapel</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Jam</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Ruang</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($all_jadwal as $jadwal)
                                    @php
                                        // $all_jadwal adalah array dari controller, misal dari $user->schedule
                                        $hari = $jadwal['hari'] ?? '';
                                        $jamMulai = $jadwal['jam_mulai'] ?? '';
                                        $jamSelesai = $jadwal['jam_selesai'] ?? '';
                                        $mapel = $jadwal['mapel'] ?? '';
                                        $ruang = $jadwal['ruang'] ?? '';
                                        $keterangan = $jadwal['keterangan'] ?? 'Tidak ada';
                                    @endphp
                                    <tr style="background-color: {{ $hari == $hari_ini ? '#d4edda' : '' }}">
                                        <td class="text-center">{{ ucfirst($hari) }}</td>
                                        <td class="text-center">{{ $mapel }}</td>
                                        <td class="text-center">{{ $jamMulai }} - {{ $jamSelesai }}</td>
                                        <td class="text-center">{{ $ruang }}</td>
                                        <td class="text-center">{{ $keterangan }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center">Tidak ada jadwal untuk hari ini.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection