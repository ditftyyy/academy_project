@extends('components.main')
@section('title-content','Data Jadwal')
@section('breadcrumbs')
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="/data-jadwalmengajar-guru">Data Guru</a></li>
        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Jadwal</li>
    </ol>
    <h6 class="font-weight-bolder mb-0">Cek Jadwal</h6>
@endsection
@section('content')
    @php
        $guruNama = $guru->guru_data['nama'] ?? $guru->nama_lengkap;
        $guruNip = $guru->guru_data['nip'] ?? '-';
    @endphp
    <div class="row">
        <div class="col-12">
            <a href="/data-jadwalmengajar-guru" class="btn btn-secondary rounded-pill font-weight-bold text-xs text-white"><i class="material-icons opacity-10">arrow_back</i> Kembali</a>
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Jadwal: {{ $guruNama }} ({{ $guruNip }})</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive pb-2 px-3">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center">Hari</th><th class="text-center">Jam</th>
                                    <th class="text-center">Kelas</th><th class="text-center">Ruang</th>
                                    <th class="text-center">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($jadwal as $j)
                                    <tr>
                                        <td class="text-center">{{ ucfirst($j['hari']) }}</td>
                                        <td class="text-center">{{ $j['jam_mulai'] }} - {{ $j['jam_selesai'] }}</td>
                                        <td class="text-center">{{ $j['kelas'] ?? '-' }}</td>
                                        <td class="text-center">{{ $j['ruang'] ?? '-' }}</td>
                                        <td class="text-center">{{ $j['keterangan'] ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center">Belum ada jadwal.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection