@extends('components.main')

@section('breadcrumbs')
<ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="/administrasi/siswa">Siswa</a></li>
    <li class="breadcrumb-item text-sm text-dark active" aria-current="page"></li>
</ol>
<h6 class="font-weight-bolder mb-0">Data Siswa</h6>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                    <h6 class="text-white text-capitalize ps-3">Data Siswa</h6>
                </div>
            </div>
            <div class="card-body px-0 pb-2">
                <div class="table-responsive pb-2 px-3">
                    <!-- Tombol aksi -->
                    <a href="/administrasi/siswa-tambah" class="btn btn-primary btn-sm"><i class="material-icons opacity-10">add</i> Tambah</a>
                    <a href="/usersiswa/export" class="btn btn-success btn-sm">Export Data Siswa</a>

                    <!-- Tabel Data Siswa -->
                    <table id="example" class="table align-items-center mb-0 mt-3">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">NIS</th>
                                <th class="text-center">Nama Lengkap</th>
                                <th class="text-center">Kelas</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($siswas as $siswa)
                                @php
                                    $siswaData = $siswa->siswa_data ?? [];
                                    $profile = $siswa->profile ?? [];
                                    $nis = $siswaData['nis'] ?? '-';
                                    $nama = $siswaData['nama'] ?? $profile['nama_lengkap'] ?? '-';
                                    $kelasNama = $siswaData['kelas']['nama'] ?? '-';
                                    $statusSiswa = $siswaData['status'] ?? '-';
                                    $foto = $siswaData['foto'] ?? $profile['foto'] ?? 'default_img.png';
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="text-center">{{ $nis }}</td>
                                    <td class="text-center">{{ $nama }}</td>
                                    <td class="text-center">{{ $kelasNama }}</td>
                                    <td class="text-center">{{ ucfirst($statusSiswa) }}</td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <!-- Detail -->
                                            <button type="button" class="btn btn-info btn-sm rounded-circle" data-bs-toggle="modal" data-bs-target="#detailModal"
                                                onclick="showDetail('{{ addslashes($nama) }}', '{{ addslashes($nis) }}', '{{ addslashes($siswaData['nisn'] ?? '') }}', '{{ addslashes($siswaData['jenis_kelamin'] ?? '') }}', '{{ addslashes($kelasNama) }}', '{{ addslashes($siswaData['nik'] ?? '') }}', '{{ addslashes(($siswaData['tempat_lahir'] ?? '').', '.(isset($siswaData['tanggal_lahir']) ? \Carbon\Carbon::parse($siswaData['tanggal_lahir'])->format('d/m/Y') : '')) }}', '{{ addslashes($siswaData['orang_tua']['nama_wali'] ?? '') }}', '{{ addslashes($siswaData['no_telp'] ?? '') }}', '{{ addslashes($siswaData['agama'] ?? '') }}', '{{ addslashes($siswaData['alamat'] ?? '') }}', '{{ addslashes($statusSiswa) }}', '{{ asset('storage/murid/img/'.$foto) }}')">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                            <!-- Edit -->
                                            <a href="/administrasi/siswa-update/{{ $siswa->_id }}" class="btn btn-warning btn-sm rounded-circle"><i class="fa fa-edit"></i></a>
                                            <!-- Hapus Permanen -->
                                            <form action="/administrasi/siswa-hapus/{{ $siswa->_id }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin ingin menghapus siswa {{ addslashes($nama) }} secara permanen? Data tidak dapat dikembalikan.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm rounded-circle"><i class="fa fa-trash"></i></button>
                                            </form>
                                            <!-- Pindah status (keluar/lulus) – hanya tampil jika status bukan keluar/lulus -->
                                            @if(!in_array(strtolower($statusSiswa), ['keluar', 'lulus']))
                                                <button type="button" class="btn btn-secondary btn-sm rounded-circle" data-bs-toggle="modal" data-bs-target="#leaveModal"
                                                    onclick="setLeave('{{ $siswa->_id }}', '{{ addslashes($nama) }}', '{{ addslashes($nis) }}', '{{ addslashes($siswaData['nisn'] ?? '') }}', '{{ addslashes($kelasNama) }}')">
                                                    <i class="fa fa-sign-out-alt"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center">Tidak ada data siswa.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail (sama seperti sebelumnya) -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">Detail Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <img id="detailFoto" src="" class="img-fluid rounded shadow-sm" style="max-height: 200px;">
                    </div>
                    <div class="col-md-8">
                        <div class="row mb-2"><div class="col-4 fw-bold">Nama</div><div class="col-8" id="detailNama">-</div></div>
                        <div class="row mb-2"><div class="col-4 fw-bold">NIS</div><div class="col-8" id="detailNis">-</div></div>
                        <div class="row mb-2"><div class="col-4 fw-bold">NISN</div><div class="col-8" id="detailNisn">-</div></div>
                        <div class="row mb-2"><div class="col-4 fw-bold">NIK</div><div class="col-8" id="detailNik">-</div></div>
                        <div class="row mb-2"><div class="col-4 fw-bold">Jenis Kelamin</div><div class="col-8" id="detailJk">-</div></div>
                        <div class="row mb-2"><div class="col-4 fw-bold">Kelas</div><div class="col-8" id="detailKelas">-</div></div>
                        <div class="row mb-2"><div class="col-4 fw-bold">Tempat, Tgl Lahir</div><div class="col-8" id="detailTtl">-</div></div>
                        <div class="row mb-2"><div class="col-4 fw-bold">Wali</div><div class="col-8" id="detailWali">-</div></div>
                        <div class="row mb-2"><div class="col-4 fw-bold">No Telepon</div><div class="col-8" id="detailTelp">-</div></div>
                        <div class="row mb-2"><div class="col-4 fw-bold">Agama</div><div class="col-8" id="detailAgama">-</div></div>
                        <div class="row mb-2"><div class="col-4 fw-bold">Status</div><div class="col-8" id="detailStatus">-</div></div>
                        <div class="row mb-2"><div class="col-4 fw-bold">Alamat</div><div class="col-8" id="detailAlamat">-</div></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Keluar (pindah status) -->
<div class="modal fade" id="leaveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white">Pindahkan Siswa ke Status Keluar/Lulus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="leaveForm" method="POST">
                    @csrf
                    @method('PUT')
                    <p>Pilih status untuk siswa <b id="leaveNama"></b> (<span id="leaveNis"></span>)</p>
                    <div class="mb-2">
                        <label>Status Baru</label>
                        <select name="status" class="form-select" required>
                            <option value="keluar">Keluar (tidak lulus)</option>
                            <option value="lulus">Lulus</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Pindahkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function showDetail(nama, nis, nisn, jk, kelas, nik, ttl, wali, telp, agama, alamat, status, fotoUrl) {
        document.getElementById('detailNama').innerText = nama;
        document.getElementById('detailNis').innerText = nis;
        document.getElementById('detailNisn').innerText = nisn;
        document.getElementById('detailJk').innerText = jk;
        document.getElementById('detailKelas').innerText = kelas;
        document.getElementById('detailNik').innerText = nik;
        document.getElementById('detailTtl').innerText = ttl;
        document.getElementById('detailWali').innerText = wali;
        document.getElementById('detailTelp').innerText = telp;
        document.getElementById('detailAgama').innerText = agama;
        document.getElementById('detailAlamat').innerText = alamat;
        document.getElementById('detailStatus').innerText = status;
        document.getElementById('detailFoto').src = fotoUrl;
    }

    function setLeave(id, nama, nis, nisn, kelas) {
        const form = document.getElementById('leaveForm');
        form.action = '/administrasi/siswa-keluar/' + id;
        document.getElementById('leaveNama').innerText = nama;
        document.getElementById('leaveNis').innerText = nis + ' (' + nisn + ')';
    }

    $(document).ready(function() {
        if ($.fn.DataTable.isDataTable('#example')) {
            $('#example').DataTable().destroy();
        }
        $('#example').DataTable({
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                emptyTable: "Tidak ada data siswa"
            }
        });
    });
</script>
@endsection