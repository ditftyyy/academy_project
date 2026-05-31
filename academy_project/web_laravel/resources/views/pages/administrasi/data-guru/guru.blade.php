@extends('components.main')

@section('title-content', 'Data Guru')

@section('breadcrumbs')
<ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="/administrasi/guru">Guru</a></li>
    <li class="breadcrumb-item text-sm text-dark active" aria-current="page"></li>
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
                    <a href="/administrasi/guru-tambah" class="btn btn-primary btn-sm"><i class="material-icons opacity-10">add</i> Tambah</a>
                    <a href="/userguru/export" class="btn btn-success btn-sm">Export Data Guru</a>

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
                            @foreach ($gurus as $guru)
                                @php
                                    $guruData = $guru->guru_data ?? [];
                                    $profileData = $guru->profile ?? [];
                                    $nama = $guruData['nama'] ?? $profileData['nama_lengkap'] ?? 'Tanpa Nama';
                                    $nip = $guruData['nip'] ?? '-';
                                    $noTelp = $guruData['no_telp'] ?? $profileData['no_telp'] ?? '-';
                                    $jk = $guruData['jenis_kelamin'] ?? $profileData['jenis_kelamin'] ?? '-';
                                    $tempatLahir = $guruData['tempat_lahir'] ?? '-';
                                    $tanggalLahir = isset($guruData['tanggal_lahir']) ? \Carbon\Carbon::parse($guruData['tanggal_lahir'])->format('d/m/Y') : '-';
                                    $agama = $guruData['agama'] ?? $profileData['agama'] ?? '-';
                                    $statusPegawai = $guruData['status_pegawai'] ?? '-';
                                    $alamatRaw = $guruData['alamat'] ?? $profileData['alamat'] ?? [];
                                    if (is_array($alamatRaw)) {
                                        $alamat = implode(', ', array_filter($alamatRaw));
                                    } else {
                                        $alamat = $alamatRaw ?: '-';
                                    }
                                    $foto = $guruData['foto'] ?? $profileData['foto'] ?? 'default_img.png';
                                    $fotoUrl = asset('storage/guru/img/' . $foto);
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="text-center">{{ $nip }}</td>
                                    <td class="text-center">{{ $nama }}</td>
                                    <td class="text-center">{{ $noTelp }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-info btn-sm rounded-circle" data-bs-toggle="modal" data-bs-target="#detailModal" 
                                            onclick="showDetail('{{ addslashes($nip) }}', '{{ addslashes($nama) }}', '{{ addslashes($noTelp) }}', '{{ addslashes($jk) }}', '{{ addslashes($tempatLahir) }}', '{{ addslashes($tanggalLahir) }}', '{{ addslashes($agama) }}', '{{ addslashes($statusPegawai) }}', '{{ addslashes($alamat) }}', '{{ $fotoUrl }}')">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                        
                                        <a href="/administrasi/guru-update/{{ $guru->_id }}" class="btn btn-warning btn-sm rounded-circle">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        
                                        <form action="/administrasi/guru-hapus/{{ $guru->_id }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm rounded-circle" onclick="return confirm('Yakin hapus data guru ini?')">
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

{{-- Modal Detail dengan tampilan rapi (tanpa border tabel yang mengganggu) --}}
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">Detail Guru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <img id="detailFoto" src="" class="img-fluid rounded shadow-sm" style="max-height: 200px; width: auto; object-fit: cover;">
                    </div>
                    <div class="col-md-8">
                        <div class="card border-0">
                            <div class="card-body p-0">
                                <div class="row mb-2">
                                    <div class="col-5 fw-bold">NIP</div>
                                    <div class="col-7" id="detailNip">-</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-5 fw-bold">Nama</div>
                                    <div class="col-7" id="detailNama">-</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-5 fw-bold">Jenis Kelamin</div>
                                    <div class="col-7" id="detailJk">-</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-5 fw-bold">Tempat, Tanggal Lahir</div>
                                    <div class="col-7" id="detailTtl">-</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-5 fw-bold">No Telepon</div>
                                    <div class="col-7" id="detailTelp">-</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-5 fw-bold">Agama</div>
                                    <div class="col-7" id="detailAgama">-</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-5 fw-bold">Status Pegawai</div>
                                    <div class="col-7" id="detailStatus">-</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-5 fw-bold">Alamat</div>
                                    <div class="col-7" id="detailAlamat">-</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    function showDetail(nip, nama, noTelp, jk, tempatLahir, tanggalLahir, agama, statusPegawai, alamat, fotoUrl) {
        document.getElementById('detailNip').innerText = nip;
        document.getElementById('detailNama').innerText = nama;
        document.getElementById('detailJk').innerText = jk;
        document.getElementById('detailTtl').innerText = tempatLahir + ', ' + tanggalLahir;
        document.getElementById('detailTelp').innerText = noTelp;
        document.getElementById('detailAgama').innerText = agama;
        document.getElementById('detailStatus').innerText = statusPegawai;
        document.getElementById('detailAlamat').innerText = alamat;
        document.getElementById('detailFoto').src = fotoUrl;
    }

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