@extends('components.main')
@section('title-content','Data Kerjasama')
@section('breadcrumbs')
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item text-sm"><a aria-current="page">Kerja Sama</a></li>
    </ol>
    <h6 class="font-weight-bolder mb-0">Data Kerja Sama</h6>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Daftar Kerjasama</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive pb-2 px-3">
                        <a href="/add-mou" class="btn btn-primary btn-sm"><i class="material-icons opacity-10">add</i> Tambah</a>
                        <table id="example" class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">Nama Mitra</th>
                                    <th class="text-center">Asal Mitra</th>
                                    <th class="text-center">Deskripsi</th>
                                    <th class="text-center">Mulai</th>
                                    <th class="text-center">Berakhir</th>
                                    <th class="text-center">PT Mitra</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($mou as $m)
                                    @php $d = $m->data_tambahan ?? []; @endphp
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td class="text-center">{{ $d['nama_mitra'] ?? '' }}</td>
                                        <td class="text-center">{{ $d['asal_mitra'] ?? '' }}</td>
                                        <td class="text-center">{{ $m->message ?? '' }}</td>
                                        <td class="text-center">{{ $d['tanggal_mulai'] ?? '' }}</td>
                                        <td class="text-center">{{ $d['tanggal_berakhir'] ?? '' }}</td>
                                        <td class="text-center">{{ $d['pt_mitra'] ?? '' }}</td>
                                        <td class="text-center">
                                            <button class="btn btn-info btn-sm rounded-circle" data-bs-toggle="modal" data-bs-target="#detailModal{{ $loop->index }}"><i class="fa fa-eye"></i></button>
                                            <a href="/edit-mou/{{ $m->_id }}" class="btn btn-warning btn-sm rounded-circle"><i class="fa fa-edit"></i></a>
                                            <form action="/delete-mou/{{ $m->_id }}" method="POST" style="display: inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm rounded-circle" onclick="return confirm('Yakin hapus data ini?')"><i class="fa fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    {{-- Modal Detail --}}
                                    <div class="modal fade" id="detailModal{{ $loop->index }}" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary"><h5 class="modal-title text-white">Detail Kerjasama</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                                <div class="modal-body">
                                                    <ul class="list-group">
                                                        <li class="list-group-item"><strong>Nama Mitra:</strong> {{ $d['nama_mitra'] ?? '' }}</li>
                                                        <li class="list-group-item"><strong>Asal Mitra:</strong> {{ $d['asal_mitra'] ?? '' }}</li>
                                                        <li class="list-group-item"><strong>Deskripsi:</strong> {{ $m->message ?? '' }}</li>
                                                        <li class="list-group-item"><strong>Mulai:</strong> {{ $d['tanggal_mulai'] ?? '' }}</li>
                                                        <li class="list-group-item"><strong>Berakhir:</strong> {{ $d['tanggal_berakhir'] ?? '' }}</li>
                                                        <li class="list-group-item"><strong>PT Mitra:</strong> {{ $d['pt_mitra'] ?? '' }}</li>
                                                        <li class="list-group-item"><strong>File:</strong> 
                                                            @if(!empty($d['file']))
                                                                <a href="{{ asset('storage/kerjasama/file/'.$d['file']) }}" target="_blank">{{ $d['original_name_file'] ?? 'Lihat' }}</a>
                                                            @else - @endif
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button></div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr><td colspan="8" class="text-center">Tidak ada data.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection