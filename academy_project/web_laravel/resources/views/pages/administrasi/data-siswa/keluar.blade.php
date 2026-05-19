@extends('components.main')

@section('breadcrumbs')
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="/administrasi/siswa">Siswa</a></li>
        <li class="breadcrumb-item text-sm text-dark active" aria-current="page"></li>
    </ol>
    <h6 class="font-weight-bolder mb-0">Data Siswa Keluar</h6>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-danger shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Data Siswa Keluar</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive pb-2 px-3">
                        <a href="/administrasi/siswa" class="btn btn-primary font-weight-bold text-xs">Siswa Aktif</a>
                        
                        <table class="table align-items-center mb-0" id="example">
                            <thead>
                                <tr>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">No</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Nama Lengkap</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Status</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Tanggal Keluar</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($siswas->count())
                                    @foreach ($siswas as $siswa)
                                        @php
                                            $siswaData = $siswa->siswa_data ?? [];
                                            $profileData = $siswa->profile ?? [];
                                            $nama = $siswaData['nama'] ?? $profileData['nama_lengkap'] ?? '';
                                            $statusSiswa = $siswaData['status'] ?? '';
                                            $tanggalKeluar = $siswaData['tanggal_keluar'] ?? '-';
                                            $foto = $siswaData['foto'] ?? $profileData['foto'] ?? 'default_img.png';
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td class="text-center">{{ $nama }}</td>
                                            <td class="text-center">{{ $statusSiswa }}</td>
                                            <td class="text-center">{{ $tanggalKeluar }}</td>
                                            <td class="text-center">
                                                <button type="button" data-bs-toggle="modal" data-bs-target="#detail-modal"
                                                    class="btn btn-info font-weight-bold text-sm rounded-circle"
                                                    data-bs-toggle="tooltip" data-bs-placement="bottom" title="Detail"
                                                    onclick="showDetailModal(this)"
                                                    data-nama="{{ $nama }}"
                                                    data-nis="{{ $siswaData['nis'] ?? '' }}"
                                                    data-nisn="{{ $siswaData['nisn'] ?? '' }}"
                                                    data-jk="{{ $siswaData['jenis_kelamin'] ?? '' }}"
                                                    data-nik="{{ $siswaData['nik'] ?? '' }}"
                                                    data-ttl="{{ ($siswaData['tempat_lahir'] ?? '') }}, {{ isset($siswaData['tanggal_lahir']) ? \Carbon\Carbon::parse($siswaData['tanggal_lahir'])->format('d/m/Y') : '' }}"
                                                    data-wali="{{ $siswaData['orang_tua']['nama_wali'] ?? '' }}"
                                                    data-telp="{{ $siswaData['no_telp'] ?? '' }}"
                                                    data-agama="{{ $siswaData['agama'] ?? '' }}"
                                                    data-alamat="{{ $siswaData['alamat'] ?? '' }}"
                                                    data-foto="{{ asset('storage/murid/img/' . $foto) }}">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                <a href="/administrasi/siswa-update/{{ (string)$siswa->_id }}"
                                                    class="btn btn-warning font-weight-bold text-sm rounded-circle"
                                                    data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <a href="/administrasi/siswa-hapus/{{ (string)$siswa->_id }}"
                                                    onclick="return confirm('Anda yakin akan menghapus data ini?')"
                                                    class="btn btn-danger font-weight-bold text-sm rounded-circle"
                                                    data-bs-toggle="tooltip" data-bs-placement="bottom" title="Hapus">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr><td colspan="5" class="text-center">No data found.</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Modal Detail --}}
    <div class="modal fade" id="detail-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white">Detail Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="foto"><img src="" alt="" width="100%" height="auto" id="modal-foto"></div>
                        </div>
                        <div class="col-md-8">
                            <ul class="list-group">
                                <li class="list-group-item"><div class="row"><div class="col-md-5"><span class="fw-bold">Nama</span><div class="float-end">:</div></div><div class="col-md-7" id="modal-nama"></div></div></li>
                                <li class="list-group-item"><div class="row"><div class="col-md-5"><span class="fw-bold">NISN</span><div class="float-end">:</div></div><div class="col-md-7" id="modal-nisn"></div></div></li>
                                <li class="list-group-item"><div class="row"><div class="col-md-5"><span class="fw-bold">NIS</span><div class="float-end">:</div></div><div class="col-md-7" id="modal-nis"></div></div></li>
                                <li class="list-group-item"><div class="row"><div class="col-md-5"><span class="fw-bold">NIK</span><div class="float-end">:</div></div><div class="col-md-7" id="modal-nik"></div></div></li>
                                <li class="list-group-item"><div class="row"><div class="col-md-5"><span class="fw-bold">Jenis Kelamin</span><div class="float-end">:</div></div><div class="col-md-7" id="modal-jk"></div></div></li>
                                <li class="list-group-item"><div class="row"><div class="col-md-5"><span class="fw-bold">TTL</span><div class="float-end">:</div></div><div class="col-md-7" id="modal-ttl"></div></div></li>
                                <li class="list-group-item"><div class="row"><div class="col-md-5"><span class="fw-bold">Wali</span><div class="float-end">:</div></div><div class="col-md-7" id="modal-wali"></div></div></li>
                                <li class="list-group-item"><div class="row"><div class="col-md-5"><span class="fw-bold">No Telepon</span><div class="float-end">:</div></div><div class="col-md-7" id="modal-telp"></div></div></li>
                                <li class="list-group-item"><div class="row"><div class="col-md-5"><span class="fw-bold">Agama</span><div class="float-end">:</div></div><div class="col-md-7" id="modal-agama"></div></div></li>
                                <li class="list-group-item"><div class="row"><div class="col-md-5"><span class="fw-bold">Alamat</span><div class="float-end">:</div></div><div class="col-md-7" id="modal-alamat"></div></div></li>
                            </ul>
                        </div>
                    </div>
                    <br>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function showDetailModal(el) {
            document.getElementById('modal-nama').innerText = el.dataset.nama;
            document.getElementById('modal-nis').innerText = el.dataset.nis;
            document.getElementById('modal-nisn').innerText = el.dataset.nisn;
            document.getElementById('modal-nik').innerText = el.dataset.nik;
            document.getElementById('modal-wali').innerText = el.dataset.wali;
            document.getElementById('modal-ttl').innerText = el.dataset.ttl;
            document.getElementById('modal-alamat').innerText = el.dataset.alamat;
            document.getElementById('modal-agama').innerText = el.dataset.agama;
            document.getElementById('modal-jk').innerText = el.dataset.jk;
            document.getElementById('modal-telp').innerText = el.dataset.telp;
            document.getElementById('modal-foto').src = el.dataset.foto;
        }
    </script>
@endsection