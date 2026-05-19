@extends('components.main')
@section('title-content','Data Raport')
@section('breadcrumbs')
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item text-sm text-dark active" aria-current="page"></li>
    </ol>
    <h6 class="font-weight-bolder mb-0">Data Siswa </h6>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Data Siswa Kelas ({{ $kelas->nama_kelas }})</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive pb-2 px-3">
                        <table id="example" class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">NISN</th>
                                    <th class="text-center">Nama</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($siswa as $s)
                                    @php
                                        $siswaData = $s->siswa_data ?? [];
                                        $nisn = $siswaData['nisn'] ?? '-';
                                        $nama = $siswaData['nama'] ?? $s->nama_lengkap ?? '';
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td class="text-center">{{ $nisn }}</td>
                                        <td class="text-center">{{ $nama }}</td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button class="btn btn-success btn-sm dropdown-toggle" data-bs-toggle="dropdown">Input Raport</button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="/data-raport-input/{{ $s->_id }}/1">Semester 1</a></li>
                                                    <li><a class="dropdown-item" href="/data-raport-input/{{ $s->_id }}/2">Semester 2</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center">Tidak ada siswa.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection