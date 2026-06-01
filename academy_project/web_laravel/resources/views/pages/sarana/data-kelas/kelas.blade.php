@extends('components.main')

@section('breadcrumbs')
<ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="/sarana/kelas">Kelas</a></li>
    <li class="breadcrumb-item text-sm text-dark active" aria-current="page"></li>
</ol>
<h6 class="font-weight-bolder mb-0">Data Kelas</h6>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                    <h6 class="text-white text-capitalize ps-3">Data Kelas</h6>
                </div>
            </div>
            <div class="card-body px-0 pb-2">
                <div class="table-responsive pb-2 px-3">
                    <button type="button" id="btntambah" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#insert-modal">
                        <i class="material-icons opacity-10">add</i> Tambah
                    </button>

                    <table id="example" class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Nama Kelas</th>
                                <th class="text-center">Wali Kelas</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($daftar_kelas as $kelas)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center">{{ $kelas->nama_kelas }}</td>
                                <td class="text-center">{{ $kelas->wali_kelas['nama'] ?? '-' }}</td>
                                <td class="text-center">
                                    {{-- Tombol Edit --}}
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#update-modal"
                                        class="btn btn-warning btn-sm rounded-circle"
                                        onclick="showUpdateModal(this)"
                                        data-id="{{ $kelas->_id }}"
                                        data-nama="{{ $kelas->nama_kelas }}"
                                        data-wali="{{ $kelas->wali_kelas['id'] ?? '' }}">
                                        <i class="fa fa-edit"></i>
                                    </button>

                                    {{-- Tombol Hapus (soft delete) --}}
                                    <form action="{{ route('hapus_kelas', $kelas->_id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Yakin ingin menghapus kelas {{ $kelas->nama_kelas }}? Kelas akan disembunyikan (soft delete).');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm rounded-circle">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal fade" id="update-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">Edit Kelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="form-edit-kelas" method="post">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Nama Kelas</label>
                        <input type="text" name="nama_kelas" id="edit-nama-kelas" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Wali Kelas</label>
                        <select name="id_guru" id="edit-wali-kelas" class="form-select">
                            <option value="">-- Pilih Guru --</option>
                            @foreach ($list_guru as $guru)
                                <option value="{{ $guru->_id }}">{{ $guru->guru_data['nama'] ?? $guru->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="insert-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">Tambah Kelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('tambah_kelas') }}" method="post">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nama Kelas</label>
                        <input type="text" name="nama_kelas" class="form-control" required placeholder="Contoh: X IPA 1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Wali Kelas (Opsional)</label>
                        <select name="id_guru" class="form-select">
                            <option value="">-- Pilih Guru --</option>
                            @foreach ($guruTersedia as $guru)
                                <option value="{{ $guru->_id }}">{{ $guru->guru_data['nama'] ?? $guru->nama_lengkap }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Guru yang sudah menjadi wali kelas tidak ditampilkan.</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function showUpdateModal(el) {
        const id = el.dataset.id;
        const nama = el.dataset.nama;
        const wali = el.dataset.wali;
        const form = document.getElementById('form-edit-kelas');
        form.action = `/sarana/kelas-update/${id}`;
        document.getElementById('edit-nama-kelas').value = nama;
        document.getElementById('edit-wali-kelas').value = wali;
    }
</script>
@endsection