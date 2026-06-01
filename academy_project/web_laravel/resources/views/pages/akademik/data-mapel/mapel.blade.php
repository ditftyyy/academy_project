@extends('components.main')

@section('breadcrumbs')
<ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="/akademik/mapel">Mapel</a></li>
    <li class="breadcrumb-item text-sm text-dark active" aria-current="page"></li>
</ol>
<h6 class="font-weight-bolder mb-0">Data Mata Pelajaran</h6>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                    <h6 class="text-white text-capitalize ps-3">Data Mata Pelajaran</h6>
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
                                <th class="text-center">Mapel</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($mapels as $mapel)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center">{{ $mapel->nama_mapel }}</td>
                                <td class="text-center">
                                    {{-- Tombol Edit --}}
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#update-modal"
                                        class="btn btn-warning btn-sm rounded-circle"
                                        onclick="showUpdateModal(this)"
                                        data-id-mapel="{{ $mapel->_id }}"
                                        data-nama-mapel="{{ $mapel->nama_mapel }}">
                                        <i class="fa fa-edit"></i>
                                    </button>

                                    {{-- Tombol Hapus (menggunakan form DELETE) --}}
                                    <form action="/akademik/mapel-hapus/{{ $mapel->_id }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Yakin ingin menghapus mata pelajaran {{ $mapel->nama_mapel }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm rounded-circle">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center">Data Pelajaran Kosong!!</td></tr>
                            @endforelse
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
                <h5 class="modal-title text-white">Edit Mata Pelajaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="form-update-mapel" method="post">
                    @method('PUT')
                    @csrf
                    <div class="mb-3">
                        <label for="nama-mapel" class="form-label">Nama Mapel</label>
                        <input type="text" name="nama_mapel" class="form-control" id="nama-mapel" required>
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
                <h5 class="modal-title text-white">Tambah Mata Pelajaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="/akademik/mapel-tambah" method="post">
                    @csrf
                    <div class="mb-3">
                        <label for="nama_mapel_insert" class="form-label">Nama Mapel</label>
                        <input type="text" name="nama_mapel" class="form-control" id="nama_mapel_insert" required>
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
    function showUpdateModal(element) {
        const form = document.getElementById('form-update-mapel');
        const idMapel = element.getAttribute('data-id-mapel');
        const namaMapel = element.getAttribute('data-nama-mapel');
        form.action = '/akademik/mapel-update/' + idMapel;
        document.getElementById('nama-mapel').value = namaMapel;
    }
</script>
@endsection