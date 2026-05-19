@extends('components.main')
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
                        <h6 class="text-white text-capitalize ps-3">Data Raport - {{ $id_siswa }}</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive pb-2 px-3">
                        <form action="" method="GET">
                            <div class="d-flex justify-content-end my-3">
                                <select class="form-select form-select-sm" name="select_raport" style="width:200px" onchange="this.form.submit()">
                                    @foreach ($raport_lists as $list)
                                        <option value="{{ $list['id'] }}" {{ $raport_selected == $list['id'] ? 'selected' : '' }}>
                                            {{ $list['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                        <table id="example" class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">Mapel</th>
                                    <th class="text-center">Nilai</th>
                                    <th class="text-center">Grade</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($raports as $nilai)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td class="text-center">{{ $nilai['mapel'] ?? '-' }}</td>
                                        <td class="text-center">{{ $nilai['nilai_akademik'] ?? 0 }}</td>
                                        <td class="text-center">{{ $nilai['nilai_huruf'] ?? '-' }}</td>
                                        <td class="text-center">
                                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#update-modal"
                                                onclick="setEditValues('{{ $nilai['id'] ?? '' }}', '{{ $nilai['mapel_id'] ?? '' }}', '{{ $nilai['nilai_akademik'] ?? 0 }}')">
                                                Ubah
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center">Belum ada nilai.</td></tr>
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
                <div class="modal-header bg-primary"><h5 class="modal-title text-white">Ubah Nilai</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <form action="/akademik/raport-update/{{ $id_siswa }}" method="post">
                        @csrf
                        <input type="hidden" name="id_detail_nilai" id="edit-detail-id">
                        <div class="mb-3">
                            <label>Mapel</label>
                            <select class="form-select" name="id_mapel" id="edit-mapel" disabled>
                                <option value="">-- Pilih --</option>
                                @foreach ($mapels as $mapel)
                                    <option value="{{ $mapel->_id }}">{{ $mapel->nama_mapel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Nilai</label>
                            <input type="number" name="nilai_akademik" id="edit-nilai" class="form-control">
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
        function setEditValues(id, mapelId, nilai) {
            document.getElementById('edit-detail-id').value = id;
            document.getElementById('edit-mapel').value = mapelId;
            document.getElementById('edit-nilai').value = nilai;
        }
    </script>
@endsection