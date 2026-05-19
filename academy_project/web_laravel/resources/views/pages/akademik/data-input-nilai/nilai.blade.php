@extends('components.main')

@section('title-content', 'Data Nilai')

@section('breadcrumbs')
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Input Nilai</li>
    </ol>
    <h6 class="font-weight-bolder mb-0">Input Nilai Siswa</h6>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Pilih Mata Pelajaran & Kelas</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive pb-2 px-3">
                        <table id="example" class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">No</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Mata Pelajaran</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Kelas</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- 
                                    MONGODB: $jadwalList berisi array dari controller.
                                    Format: ['mapel_nama' => '...', 'kelas_nama' => '...', 'kelas_id' => '...', 'mapel_id' => '...']
                                    Data ini diambil dari schedule guru atau jadwal kelas.
                                --}}
                                @forelse ($jadwalList as $item)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td class="text-center">{{ $item['mapel_nama'] ?? '-' }}</td>
                                        <td class="text-center">{{ $item['kelas_nama'] ?? '-' }}</td>
                                        <td class="text-center">
                                            <div class="btn-group dropdown">
                                                <button type="button" class="btn btn-success dropdown-toggle text-sm"
                                                    data-bs-toggle="dropdown" aria-expanded="true" data-boundary="viewport">
                                                    <b>Cek Nilai</b>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    {{-- MONGODB: Gunakan kelas_id, mapel_id, dan semester --}}
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="/data-nilai-atur/{{ $item['kelas_id'] }}/{{ $item['mapel_id'] }}/ganjil">
                                                            Semester Ganjil
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="/data-nilai-atur/{{ $item['kelas_id'] }}/{{ $item['mapel_id'] }}/genap">
                                                            Semester Genap
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Tidak ada data mengajar.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection