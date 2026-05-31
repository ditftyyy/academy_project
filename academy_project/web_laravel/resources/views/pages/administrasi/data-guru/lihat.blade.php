@extends('components.main')

@section('breadcrumbs')
<ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Guru</li>
</ol>
<h6 class="font-weight-bolder mb-0">Data Guru</h6>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                    <h6 class="text-white text-capitalize ps-3">Data Guru</h6>
                </div>
            </div>
            <div class="card-body px-0 pb-2">
                <div class="table-responsive pb-2 px-3">
                    <table id="example" class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">NIP</th>
                                <th class="text-center">Nama Lengkap</th>
                                <th class="text-center">No Telepon</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($guru as $g)
                                @php
                                    // Data dari MongoDB
                                    $guruData = $g->guru_data ?? [];
                                    $profile = $g->profile ?? [];
                                    $nip = $guruData['nip'] ?? '-';
                                    $nama = $guruData['nama'] ?? $profile['nama_lengkap'] ?? '-';
                                    $noTelp = $guruData['no_telp'] ?? $profile['no_telp'] ?? '-';
                                    $jk = $guruData['jenis_kelamin'] ?? '-';
                                    $tempatLahir = $guruData['tempat_lahir'] ?? '-';
                                    $tanggalLahir = isset($guruData['tanggal_lahir']) ? \Carbon\Carbon::parse($guruData['tanggal_lahir'])->format('d/m/Y') : '-';
                                    $agama = $guruData['agama'] ?? '-';
                                    $alamatRaw = $guruData['alamat'] ?? $profile['alamat'] ?? [];
                                    // Format alamat jika array
                                    if (is_array($alamatRaw)) {
                                        $alamat = implode(', ', array_filter($alamatRaw));
                                    } else {
                                        $alamat = $alamatRaw;
                                    }
                                    $foto = $guruData['foto'] ?? $profile['foto'] ?? 'default_img.png';
                                    $fotoUrl = asset('storage/guru/img/' . $foto);
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="text-center">{{ $nip }}</td>
                                    <td class="text-center">{{ $nama }}</td>
                                    <td class="text-center">{{ $noTelp }}</td>
                                    <td class="text-center">
                                        <button type="button" data-bs-toggle="modal" data-bs-target="#detail-modal-{{ $g->_id }}"
                                            class="btn btn-info btn-sm rounded" data-bs-toggle="tooltip" title="Detail">
                                            <i class="fa fa-eye"></i> Detail
                                        </button>
                                    </td>
                                </tr>

                                {{-- Modal Detail --}}
                                <div class="modal fade" id="detail-modal-{{ $g->_id }}" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary">
                                                <h5 class="modal-title text-white">Detail Guru</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-4 text-center">
                                                        <img src="{{ $fotoUrl }}" alt="Foto Guru" class="img-fluid rounded" style="max-height: 200px; width: auto;">
                                                    </div>
                                                    <div class="col-md-8">
                                                        <table class="table table-bordered">
                                                            <tr><th>NIP</th><td>{{ $nip }}</td></tr>
                                                            <tr><th>Nama</th><td>{{ $nama }}</td></tr>
                                                            <tr><th>Jenis Kelamin</th><td>{{ $jk }}</td></tr>
                                                            <tr><th>Tempat, Tanggal Lahir</th><td>{{ $tempatLahir }}, {{ $tanggalLahir }}</td></tr>
                                                            <tr><th>No Telepon</th><td>{{ $noTelp }}</td></tr>
                                                            <tr><th>Agama</th><td>{{ $agama }}</td></tr>
                                                            <tr><th>Alamat</th><td>{{ $alamat ?: '-' }}</td></tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#example').DataTable({
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                emptyTable: "Tidak ada data guru"
            }
        });
    });
</script>
@endsection