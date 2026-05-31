@extends('components.main')
@section('title-content','Data Peminjaman Barang')
@section('breadcrumbs')
<ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="/data-peminjaman-barang">Peminjaman Barang</a></li>
    <li class="breadcrumb-item text-sm text-dark active" aria-current="page"></li>
</ol>
<h6 class="font-weight-bolder mb-0">Peminjaman Barang</h6>
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        @if(count($hariini) > 0)
        <div class="card my-4">
            <div class="card-header bg-success text-white">Sedang Dipinjam Hari Ini</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Barang</th>
                                <th>Jumlah</th>
                                <th>Peminjam</th>
                                <th>Pinjam</th>
                                <th>Kembali</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($hariini as $p)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $p['nama_barang'] }}</td>
                                <td>{{ $p['jumlah'] }}</td>
                                <td>{{ $p['nama_peminjam'] }}</td>
                                <td>{{ $p['tanggal_pinjam'] }}</td>
                                <td>{{ $p['tanggal_kembali'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <div class="card my-4">
            <div class="card-header bg-primary text-white">Daftar Peminjaman Barang</div>
            <div class="card-body">
                @if(auth()->user()->hasRole('admin'))
                    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#insertModal">
                        <i class="material-icons">add</i> Tambah
                    </button>
                @endif
                <div class="table-responsive">
                    <table class="table table-bordered" id="example">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Barang</th>
                                <th>Jumlah</th>
                                <th>Peminjam</th>
                                <th>Pinjam</th>
                                <th>Kembali</th>
                                <th>Status Pengajuan</th>
                                <th>Surat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($peminjaman as $p)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $p['nama_barang'] ?? '-' }}</td>
                                <td>{{ $p['jumlah'] ?? '-' }}</td>
                                <td>{{ $p['nama_peminjam'] ?? '-' }}</td>
                                <td>{{ $p['tanggal_pinjam'] ?? '-' }}</td>
                                <td>{{ $p['tanggal_kembali'] ?? '-' }}</td>
                                <td>
                                    @php
                                        $statusPengajuan = $p['status_pengajuan'] ?? null;
                                        $badgeClass = is_null($statusPengajuan) ? 'warning' : ($statusPengajuan ? 'success' : 'danger');
                                        $statusText = is_null($statusPengajuan) ? 'MENUNGGU' : ($statusPengajuan ? 'DISETUJUI' : 'DITOLAK');
                                    @endphp
                                    <span class="badge bg-{{ $badgeClass }}">{{ $statusText }}</span>
                                </td>
                                <td>
                                    @if(!empty($p['surat']))
                                        <a href="{{ asset($p['surat']) }}" target="_blank" class="btn btn-info btn-sm">LIHAT</a>
                                    @else - @endif
                                </td>
                                <td>
                                    @if(auth()->user()->hasRole('admin'))
                                        <!-- Tombol Edit -->
                                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal" 
                                            onclick="fillEdit('{{ $p['_id'] }}', '{{ $p['nama_peminjam'] }}', '{{ $p['tanggal_kembali'] }}')">
                                            <i class="fa fa-edit"></i> Edit
                                        </button>

                                        <!-- Tombol Hapus -->
                                        <form action="{{ route('peminjamanBarang.destroy', $p['_id']) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Hapus</button>
                                        </form>

                                        <!-- Tombol Setuju / Tolak hanya jika masih MENUNGGU -->
                                        @if(is_null($p['status_pengajuan'] ?? null))
                                            <a href="{{ route('peminjamanBarang.approve', $p['_id']) }}" class="btn btn-success btn-sm" onclick="return confirm('Setujui peminjaman ini?')">SETUJU</a>
                                            <a href="{{ route('peminjamanBarang.decline', $p['_id']) }}" class="btn btn-danger btn-sm" onclick="return confirm('Tolak peminjaman ini?')">TOLAK</a>
                                        @endif

                                        <!-- Tombol Selesai hanya jika barang masih dipinjam dan belum dikembalikan -->
                                        @if(($p['status'] ?? '') === 'dipinjam')
                                            <a href="{{ route('peminjamanBarang.confirm', $p['_id']) }}" class="btn btn-secondary btn-sm" onclick="return confirm('Tandai barang telah dikembalikan?')">SELESAI</a>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center">Tidak ada data peminjaman aktif</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="insertModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title">Tambah Peminjaman Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('peminjamanBarang.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Barang</label>
                            <select name="barang_id" class="form-select" required>
                                <option value="">-- Pilih Barang --</option>
                                @foreach($barang as $b)
                                    <option value="{{ $b->_id }}">{{ $b->nama_barang }} (Stok: {{ $b->jumlah_baik }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Nama Peminjam</label>
                            <input type="text" name="nama_peminjam" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Tanggal Pinjam</label>
                            <input type="date" name="tanggal_peminjaman" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Tanggal Kembali</label>
                            <input type="date" name="tanggal_pengembalian" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Jumlah</label>
                            <input type="number" name="jumlah" class="form-control" required min="1">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Surat (PDF/DOC)</label>
                            <input type="file" name="surat" class="form-control" required accept=".pdf,.doc,.docx">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Edit Peminjaman Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editFormBarang" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="editId">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Nama Peminjam</label>
                            <input type="text" name="nama_peminjam" id="editNamaPeminjam" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Tanggal Kembali</label>
                            <input type="date" name="tanggal_pengembalian" id="editTglKembali" class="form-control" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Surat (biarkan kosong jika tidak mengganti file)</label>
                            <input type="file" name="surat" class="form-control" accept=".pdf,.doc,.docx">
                            <small class="text-muted">Unggah file baru hanya jika ingin mengganti surat lama.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function fillEdit(id, namaPeminjam, tglKembali) {
        document.getElementById('editId').value = id;
        document.getElementById('editNamaPeminjam').value = namaPeminjam;
        document.getElementById('editTglKembali').value = tglKembali;
        // Set action form menggunakan route name
        document.getElementById('editFormBarang').action = '{{ route("peminjamanBarang.update", "") }}/' + id;
    }
</script>
@endsection