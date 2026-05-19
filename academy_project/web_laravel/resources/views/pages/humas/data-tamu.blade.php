@extends('components.main')
@section('title-content', 'Data Tamu')
@section('breadcrumbs')
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item text-sm"><a aria-current="page">Tamu</a></li>
    </ol>
    <h6 class="font-weight-bolder mb-0">Data Tamu</h6>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Daftar Tamu</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive pb-2 px-3">
                        <a href="/tamu" class="btn btn-primary font-weight-bold text-xs">
                            <i class="material-icons opacity-10">add</i> Tambah
                        </a>
                        <table id="example" class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">No</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Nama</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Asal</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Tujuan</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Nama Yang Dituju</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Keterangan</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Status</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tamus as $t)
                                    @php
                                        $data = $t->data_tambahan ?? [];
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td class="text-center">{{ $data['nama_tamu'] ?? '' }}</td>
                                        <td class="text-center">{{ $data['alamat'] ?? '' }}</td>
                                        <td class="text-center">{{ $data['tujuan'] ?? '' }}</td>
                                        <td class="text-center">{{ $data['tujuan_nama'] ?? $data['tujuan_username'] ?? '' }}</td>
                                        <td class="text-center">{{ $t->message ?? '' }}</td>
                                        <td class="text-center">
                                            @php $status = $data['status'] ?? 'menunggu'; @endphp
                                            @if ($status === 'menunggu')
                                                Menunggu
                                            @elseif ($status === 'pesan_telah_diterima')
                                                Pesan Diterima
                                            @elseif ($status === 'pesan_telah_selesai')
                                                Pesan Selesai
                                            @else
                                                {{ $status }}
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <button type="button" data-bs-toggle="modal" data-bs-target="#detail-modal"
                                                class="btn btn-info font-weight-bold btn--edit text-sm rounded-circle"
                                                style="margin: 5px 0;" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                title="Detail"
                                                onclick="showDetail('{{ $t->_id }}')">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                            <a href="/tamu-edit/{{ $t->_id }}"
                                                class="btn btn-warning font-weight-bold text-sm rounded-circle"
                                                data-bs-toggle="tooltip" data-bs-placement="bottom" style="margin: 5px 0;"
                                                title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="/tamu-delete/{{ $t->_id }}"
                                                onclick="return confirm('Anda yakin akan menghapus data ini?')"
                                                class="btn btn-danger font-weight-bold text-sm rounded-circle"
                                                data-bs-toggle="tooltip" data-bs-placement="bottom" style="margin: 5px 0;"
                                                title="Hapus">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="text-center">Tidak ada data tamu.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Detail --}}
        <div class="modal fade" id="detail-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title text-white" id="exampleModalLabel">Detail Tamu</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-8">
                                <ul class="list-group">
                                    <li class="list-group-item"><div class="row"><div class="col-md-5 fw-bold">Nama</div><div class="col-md-7" id="modal-nama"></div></div></li>
                                    <li class="list-group-item"><div class="row"><div class="col-md-5 fw-bold">Asal</div><div class="col-md-7" id="modal-asal"></div></div></li>
                                    <li class="list-group-item"><div class="row"><div class="col-md-5 fw-bold">Tujuan</div><div class="col-md-7" id="modal-tujuan"></div></div></li>
                                    <li class="list-group-item"><div class="row"><div class="col-md-5 fw-bold">Nama Tujuan</div><div class="col-md-7" id="modal-nama-tujuan"></div></div></li>
                                    <li class="list-group-item"><div class="row"><div class="col-md-5 fw-bold">Keterangan</div><div class="col-md-7" id="modal-keterangan"></div></div></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const tamuData = {!! json_encode($tamus->keyBy(function ($t) { return (string) $t->_id; })->map(function ($t) {
            $d = $t->data_tambahan ?? [];
            return [
                'nama' => $d['nama_tamu'] ?? '',
                'asal' => $d['alamat'] ?? '',
                'tujuan' => $d['tujuan'] ?? '',
                'nama_tujuan' => $d['tujuan_nama'] ?? $d['tujuan_username'] ?? '',
                'keterangan' => $t->message ?? ''
            ];
        })) !!};

        function showDetail(id) {
            const data = tamuData[id];
            if (!data) return;
            document.getElementById('modal-nama').innerText = data.nama;
            document.getElementById('modal-asal').innerText = data.asal;
            document.getElementById('modal-tujuan').innerText = data.tujuan;
            document.getElementById('modal-nama-tujuan').innerText = data.nama_tujuan;
            document.getElementById('modal-keterangan').innerText = data.keterangan;
        }

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