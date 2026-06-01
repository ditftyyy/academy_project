@extends('components.main')
@section('title-content','Data Peminjaman Ruang')
@section('breadcrumbs')
<ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="/data-peminjaman">Peminjaman</a></li>
    <li class="breadcrumb-item text-sm text-dark active" aria-current="page"></li>
</ol>
<h6 class="font-weight-bolder mb-0">Peminjaman Ruang</h6>
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        @if(count($hariini) > 0)
        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-success shadow-primary border-radius-lg pt-4 pb-3"><h6 class="text-white ps-3">Sedang Dipinjam Hari Ini</h6></div>
            </div>
            <div class="card-body px-0 pb-2">
                <table class="table">
                    <thead><tr><th>No</th><th>Ruang</th><th>Peminjam</th><th>Pinjam</th><th>Kembali</th></tr></thead>
                    <tbody>
                        @foreach($hariini as $p)
                        <tr><td>{{ $loop->iteration }}</td><td>{{ $p['nama_ruang'] }}</td><td>{{ $p['nama_peminjam'] }}</td><td>{{ $p['tanggal_pinjam'] }}</td><td>{{ $p['tanggal_kembali'] }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3"><h6 class="text-white ps-3">Daftar Peminjaman Ruang</h6></div>
            </div>
            <div class="card-body px-0 pb-2">
                <div class="table-responsive pb-2 px-3">
                    @if(auth()->user()->hasRole('admin'))
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#insertModal"><i class="material-icons">add</i> Tambah</button>
                    @endif
                    <table id="example" class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th>No</th><th>Ruang</th><th>Peminjam</th><th>Pinjam</th><th>Kembali</th>
                                <th>Status Pengajuan</th><th>Surat</th><th>Status Peminjaman</th><th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($peminjaman as $p)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $p['nama_ruang'] ?? '' }}</td>
                                <td>{{ $p['nama_peminjam'] ?? '' }}</td>
                                <td>{{ $p['tanggal_pinjam'] ?? '' }}</td>
                                <td>{{ $p['tanggal_kembali'] ?? '' }}</td>
                                <td>
                                    @php $statusPengajuan = $p['status_pengajuan'] ?? null; @endphp
                                    <span class="badge bg-{{ is_null($statusPengajuan) ? 'warning' : ($statusPengajuan ? 'success' : 'danger') }}">
                                        {{ is_null($statusPengajuan) ? 'Menunggu' : ($statusPengajuan ? 'Disetujui' : 'Ditolak') }}
                                    </span>
                                </td>
                                <td>
                                    @if(!empty($p['surat']))
                                        <a href="{{ asset($p['surat']) }}" target="_blank" class="btn btn-info btn-sm">Lihat</a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @php $statusPinjam = $p['status'] ?? 'dipinjam'; @endphp
                                    <span class="badge bg-{{ $statusPinjam === 'dikembalikan' ? 'secondary' : 'primary' }}">
                                        {{ $statusPinjam === 'dikembalikan' ? 'Dikembalikan' : 'Dipinjam' }}
                                    </span>
                                </td>
                                <td>
                                    @if(auth()->user()->hasRole('admin'))
                                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal" 
                                            onclick="fillEdit('{{ $p['_id'] }}', '{{ $p['ruang_id'] }}', '{{ $p['nama_peminjam'] }}', '{{ $p['tanggal_pinjam'] }}', '{{ $p['tanggal_kembali'] }}')">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <form action="{{ url('/peminjaman-hapus/' . $p['ruang_id'] . '/' . $p['_id']) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin hapus peminjaman ini?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                        </form>
                                        @if(is_null($p['status_pengajuan'] ?? null))
                                            <a href="{{ url('/peminjaman-approve/' . $p['_id']) }}" class="btn btn-success btn-sm" onclick="return confirm('Setujui peminjaman?')">Setuju</a>
                                            <a href="{{ url('/peminjaman-decline/' . $p['_id']) }}" class="btn btn-danger btn-sm" onclick="return confirm('Tolak peminjaman?')">Tolak</a>
                                        @endif
                                        @if(($p['status'] ?? 'dipinjam') !== 'dikembalikan')
                                            <a href="{{ url('/peminjaman-complete/' . $p['ruang_id'] . '/' . $p['_id']) }}" class="btn btn-secondary btn-sm" onclick="return confirm('Tandai sebagai dikembalikan?')">Selesai</a>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary">No action</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="9" class="text-center">Tidak ada
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="insertModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary"><h5 class="modal-title text-white">Tambah Peminjaman Ruang</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <form action="/peminjaman-tambah" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3"><label>Ruang</label><select name="ruang" class="form-select" required>@foreach($ruang as $r)<option value="{{ $r->_id }}">{{ $r->nama_ruang }}</option>@endforeach</select></div>
                        <div class="col-md-6 mb-3"><label>Nama Peminjam</label><input type="text" name="nama_peminjam" class="form-control" required></div>
                        <div class="col-md-6 mb-3"><label>Tgl Pinjam</label><input type="date" name="tgl_peminjaman" class="form-control" required></div>
                        <div class="col-md-6 mb-3"><label>Tgl Kembali</label><input type="date" name="tgl_pengembalian" class="form-control" required></div>
                        <div class="col-md-12 mb-3"><label>Surat</label><input type="file" name="surat" class="form-control" required></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning"><h5 class="modal-title">Edit Peminjaman</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <form id="editForm" method="post" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <input type="hidden" name="id" id="editId">
                    <input type="hidden" name="ruang_id" id="editRuangId">
                    <div class="row">
                        <div class="col-md-6 mb-3"><label>Nama Peminjam</label><input type="text" name="nama_peminjam" id="editNama" class="form-control" required></div>
                        <div class="col-md-6 mb-3"><label>Tgl Pinjam</label><input type="date" name="tgl_peminjaman" id="editTglPinjam" class="form-control" required></div>
                        <div class="col-md-6 mb-3"><label>Tgl Kembali</label><input type="date" name="tgl_pengembalian" id="editTglKembali" class="form-control" required></div>
                        <div class="col-md-12 mb-3"><label>Surat (biarkan jika tidak ganti)</label><input type="file" name="surat" class="form-control"></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function fillEdit(id, ruangId, nama, tglPinjam, tglKembali) {
        document.getElementById('editId').value = id;
        document.getElementById('editRuangId').value = ruangId;
        document.getElementById('editNama').value = nama;
        document.getElementById('editTglPinjam').value = tglPinjam;
        document.getElementById('editTglKembali').value = tglKembali;
        document.getElementById('editForm').action = '/peminjaman-update/' + ruangId + '/' + id;
    }
</script>
@endsection