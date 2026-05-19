@extends('components.main')
@section('title-content','Data Peminjaman Barang')
@section('breadcrumbs')
<ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="/data-peminjaman">Peminjaman Barang</a></li>
    <li class="breadcrumb-item text-sm text-dark active" aria-current="page"></li>
</ol>
<h6 class="font-weight-bolder mb-0">Riwayat Peminjaman</h6>
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-danger shadow-primary border-radius-lg pt-4 pb-3">
                    <h6 class="text-white text-capitalize ps-3">Riwayat Peminjaman Barang</h6>
                </div>
            </div>
            <div class="card-body px-0 pb-2">
                <div class="table-responsive pb-2 px-3">
                    <a href="/data-peminjaman-barang" class="btn btn-primary btn-sm">Kembali</a>

                    <table id="example" class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Barang</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-center">Peminjam</th>
                                <th class="text-center">Pinjam</th>
                                <th class="text-center">Kembali</th>
                                @if(auth()->user()->hasRole('admin'))
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($peminjaman_barang as $p)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center">{{ $p['nama_barang'] ?? '-' }}</td>
                                <td class="text-center">{{ $p['jumlah'] ?? '' }}</td>
                                <td class="text-center">{{ $p['nama_peminjam'] ?? '' }}</td>
                                <td class="text-center">{{ $p['tanggal_pinjam'] ?? '' }}</td>
                                <td class="text-center">{{ $p['tanggal_kembali'] ?? '' }}</td>
                                @if(auth()->user()->hasRole('admin'))
                                <td class="text-center">
                                    <span class="badge {{ ($p['status'] ?? '') == 'dikembalikan' ? 'text-bg-success' : 'text-bg-warning' }}">
                                        {{ ($p['status'] ?? '') == 'dikembalikan' ? 'Dikembalikan' : 'Belum Dikembalikan' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if(($p['status'] ?? '') != 'dikembalikan')
                                    <a href="/data-peminjaman-barang-confirm/{{ $p['_id'] }}" class="btn btn-success btn-sm rounded-circle" onclick="return confirm('Konfirmasi pengembalian?')">
                                        <i class="fa fa-calendar-check"></i>
                                    </a>
                                    @endif
                                    <a href="/data-peminjaman-barang-hapus/{{ $p['_id'] }}" class="btn btn-danger btn-sm rounded-circle" onclick="return confirm('Hapus?')">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </td>
                                @endif
                            </tr>
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

<script>
    $(document).ready(function() {
        if ($('#example').length && $('#example thead th').length > 0) {
            if ($.fn.DataTable.isDataTable('#example')) {
                $('#example').DataTable().destroy();
            }
            $('#example').DataTable({
                searching: true,
                ordering: true,
                paging: true,
                select: true,
                responsive: true
            });
        }
    });
</script>
@endsection