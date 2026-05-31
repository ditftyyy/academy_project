@extends('components.main')
@section('title-content', 'Inventaris Barang')
@section('breadcrumbs')
<ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
    <li class="breadcrumb-item text-sm"><a href="/sarana/inventaris">Inventaris</a></li>
    <li class="breadcrumb-item text-sm text-dark active">Inventaris Barang</li>
</ol>
<h6 class="font-weight-bolder mb-0">Persediaan Barang</h6>
<h6 class="font-weight-bolder mb-0">Ruang : {{ $ruang->nama_ruang }}</h6>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card my-4">
            <div class="card-body px-0 pb-2">
                <div class="table-responsive pb-2 px-3">
                    @if(auth()->user()->hasRole('admin'))
                    <button type="button" class="btn btn-primary btn-sm mb-3" data-bs-toggle="modal" data-bs-target="#tambahModal">
                        <i class="material-icons">add</i> Tambah Barang
                    </button>
                    @endif

                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Barang</th>
                                <th>Tahun Pengadaan</th>
                                <th>Jenis</th>
                                <th>Jumlah</th>
                                @if(auth()->user()->hasRole('admin'))<th>Aksi</th>@endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inventaris as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->nama_barang }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->tahun_pengadaan)->format('d/m/Y') }}</td>
                                <td>{{ ucfirst($item->jenis) }}</td>
                                <td>{{ $item->jumlah_baik }}</td>
                                @if(auth()->user()->hasRole('admin'))
                                <td>
                                    <form action="{{ route('delete-inventaris', $item->_id) }}" method="POST" onsubmit="return confirm('Yakin hapus barang ini dari ruang?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm rounded-circle"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                                @endif
                            </tr>
                            @empty
                            <table><td colspan="6" class="text-center">Belum ada barang di ruang ini.
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah Barang --}}
<div class="modal fade" id="tambahModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Tambah Barang ke Ruang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('store-inventaris', $ruang->_id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Pilih Barang dari Master</label>
                        <select name="barang_id" class="form-select" required>
                            <option value="">-- Pilih --</option>
                            @foreach($masterBarang as $barang)
                                <option value="{{ $barang->_id }}">
                                    {{ $barang->nama_barang }} ({{ ucfirst($barang->jenis) }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Barang yang belum pernah ditambahkan ke ruang ini.</small>
                    </div>
                    <div class="mb-3">
                        <label>Jumlah Barang (Baik)</label>
                        <input type="number" name="jumlah_barang" class="form-control" required min="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Tambahkan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection