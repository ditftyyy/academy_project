@extends('components.main')
@section('title-content', 'Inventaris')
@section('breadcrumbs')
<ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="/sarana/inventaris">Inventaris</a></li>
    <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Inventaris Barang</li>
</ol>
<h6 class="font-weight-bolder mb-0">Persediaan Barang</h6>
<h6 class="font-weight-bolder mb-0">Ruang : {{$ruangs->nama_ruang}}</h6>
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card my-4">
            <div class="card-body px-0 pb-2">
                <div class="table-responsive pb-2 px-3">
                    @if (auth()->user()->hasRole('admin'))
                    <button type="button" data-bs-toggle="modal" data-bs-target="#tambahBarangModal"
                        class="btn btn-primary btn-sm"><i class="material-icons opacity-10">add</i> Tambah Barang</button>
                    @endif
                    <table id="example" class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Nama Barang</th>
                                <th class="text-center">Tahun Pengadaan</th>
                                <th class="text-center">Jenis</th>
                                <th class="text-center">Gambar</th>
                                <th class="text-center">Jumlah Baik</th>
                                <th class="text-center">Jumlah Rusak</th>
                                @if(auth()->user()->hasRole('admin'))
                                <th class="text-center">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($inventaris as $item)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center">{{ $item->nama_barang }}</td>
                                <td class="text-center">{{ $item->tahun_pengadaan }}</td>
                                <td class="text-center">{{ $item->jenis }}</td>
                                <td>
                                    @if($item->image)
                                        <img src="{{ asset('storage/image/' . $item->image) }}" height="60" width="80">
                                    @endif
                                </td>
                                <td class="text-center">{{ $item->jumlah_baik }}</td>
                                <td class="text-center">{{ $item->jumlah_rusak }}</td>
                                @if(auth()->user()->hasRole('admin'))
                                <td class="text-center">
                                    <form action="{{ route('delete-inventaris', $item->_id) }}" method="POST" onsubmit="return confirm('Yakin hapus?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm rounded-circle"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                                @endif
                            </tr>
                            @empty
                            <tr><td colspan="8" class="text-center">Belum ada barang di ruang ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah Barang --}}
<div class="modal fade" id="tambahBarangModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formBarang" action="{{ route('store-inventaris', $ruangs->_id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label>Cari Barang</label>
                        <input type="text" class="form-control" id="nama_barang_cari" placeholder="Ketik nama barang...">
                    </div>
                    <div class="mb-3">
                        <label>Pilih Barang</label>
                        <select id="nama_barang_dropdown" class="form-select" required></select>
                    </div>
                    <div class="mb-3">
                        <label>Jumlah Barang (Baik)</label>
                        <input type="number" class="form-control" id="jumlah_barang" name="jumlah_barang" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Tambahkan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#nama_barang_cari').keyup(function() {
            var query = $(this).val();
            if (query.length > 2) {
                $.ajax({
                    url: '{{ route("search-barang") }}',
                    method: 'GET',
                    data: { searchTerm: query },
                    success: function(response) {
                        var dropdown = $('#nama_barang_dropdown');
                        dropdown.empty();
                        if (response.length > 0) {
                            response.forEach(function(barang) {
                                dropdown.append($('<option></option>').val(barang._id).text(barang.nama_barang));
                            });
                        } else {
                            dropdown.append($('<option></option>').text('Tidak ditemukan'));
                        }
                    }
                });
            }
        });
    });
</script>
@endsection