@extends('components.main')

@section('title-content')
    Data Guru
@endsection

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
                        <a href="/administrasi/guru-tambah" class="btn btn-primary font-weight-bold text-xs">
                            <i class="material-icons opacity-10">add</i> Tambah
                        </a>
                        <a href="/userguru/export" class="btn btn-success font-weight-bold text-xs">
                            Export Data Guru
                        </a>
                        
                        <table id="example" class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">No</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">NIP</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Nama Lengkap</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">No Telepon</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($gurus as $guru)
                                    @php
                                        // MONGODB: Ambil data dari guru_data
                                        $guruData = $guru->guru_data ?? [];
                                        $profileData = $guru->profile ?? [];
                                        $nama = $guruData['nama'] ?? $profileData['nama_lengkap'] ?? 'Tanpa Nama';
                                        $nip = $guruData['nip'] ?? '-';
                                        $noTelp = $guruData['no_telp'] ?? $profileData['no_telp'] ?? '-';
                                        $foto = $guruData['foto'] ?? $profileData['foto'] ?? 'default_img.png';
                                        $signature = $guruData['signature'] ?? 'default_signature.png';
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td class="text-center">{{ $nip }}</td>
                                        <td class="text-center">{{ $nama }}</td>
                                        <td class="align-middle text-center">{{ $noTelp }}</td>
                                        <td class="text-center" style="display: flex; gap: 10px; justify-content: center">
                                            {{-- Tombol Detail --}}
                                            <button type="button" data-bs-toggle="modal" data-bs-target="#detail-modal-{{ (string)$guru->_id }}"
                                                class="btn btn-info font-weight-bold text-sm rounded-circle"
                                                style="margin: 5px 0;" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Detail"
                                                onclick="showDetailModal('{{ (string)$guru->_id }}')">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                            
                                            {{-- Tombol Edit --}}
                                            <a href="/administrasi/guru-update/{{ (string)$guru->_id }}"
                                                class="btn btn-warning font-weight-bold text-sm rounded-circle"
                                                data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit" style="margin: 5px 0;">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            
                                            {{-- Tombol Hapus --}}
                                            <a href="/administrasi/guru-hapus/{{ (string)$guru->_id }}"
                                                onclick="return confirm('Anda yakin akan menghapus data ini?')"
                                                class="btn btn-danger font-weight-bold text-sm rounded-circle"
                                                data-bs-toggle="tooltip" data-bs-placement="bottom" title="Hapus" style="margin: 5px 0;">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Modal Detail (gunakan JavaScript untuk mengisi) --}}
        <div class="modal fade" id="detail-modal-guru" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title text-white" id="exampleModalLabel">Detail Guru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="foto" id="modal-foto">
                                    <img src="" alt="" width="100%" height="auto">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <ul class="list-group">
                                    <li class="list-group-item"><div class="row"><div class="col-md-5"><span class="float-start fw-bold">NIP</span><div class="float-end">:</div></div><div class="col-md-7" id="modal-nip"></div></div></li>
                                    <li class="list-group-item"><div class="row"><div class="col-md-5"><span class="float-start fw-bold">Nama</span><div class="float-end">:</div></div><div class="col-md-7" id="modal-nama"></div></div></li>
                                    <li class="list-group-item"><div class="row"><div class="col-md-5"><span class="float-start fw-bold">Jenis Kelamin</span><div class="float-end">:</div></div><div class="col-md-7" id="modal-jk"></div></div></li>
                                    <li class="list-group-item"><div class="row"><div class="col-md-5"><span class="float-start fw-bold">Tempat, tanggal lahir</span><div class="float-end">:</div></div><div class="col-md-7" id="modal-ttl"></div></div></li>
                                    <li class="list-group-item"><div class="row"><div class="col-md-5"><span class="float-start fw-bold">No Telepon</span><div class="float-end">:</div></div><div class="col-md-7" id="modal-telp"></div></div></li>
                                    <li class="list-group-item"><div class="row"><div class="col-md-5"><span class="float-start fw-bold">Status</span><div class="float-end">:</div></div><div class="col-md-7" id="modal-status"></div></div></li>
                                    <li class="list-group-item"><div class="row"><div class="col-md-5"><span class="float-start fw-bold">Agama</span><div class="float-end">:</div></div><div class="col-md-7" id="modal-agama"></div></div></li>
                                    <li class="list-group-item"><div class="row"><div class="col-md-5"><span class="float-start fw-bold">Alamat</span><div class="float-end">:</div></div><div class="col-md-7" id="modal-alamat"></div></div></li>
                                    <li class="list-group-item"><div class="row"><div class="col-md-5"><span class="float-start fw-bold">Tanda tangan</span><div class="float-end">:</div></div><div class="col-md-7" id="modal-signature"><img src="" style="width: 100%"></div></div></li>
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
    </div>
    
    <script>
        // Data guru dalam format JSON untuk modal
        const guruDataMap = {};
        @foreach ($gurus as $guru)
            @php
                $guruData = $guru->guru_data ?? [];
                $profileData = $guru->profile ?? [];
            @endphp
            guruDataMap['{{ (string)$guru->_id }}'] = {
                nip: '{{ $guruData['nip'] ?? '' }}',
                nama: '{{ $guruData['nama'] ?? $profileData['nama_lengkap'] ?? '' }}',
                jenis_kelamin: '{{ $guruData['jenis_kelamin'] ?? $profileData['jenis_kelamin'] ?? '' }}',
                ttl: '{{ $guruData['tempat_lahir'] ?? '' }}, {{ isset($guruData['tanggal_lahir']) ? \Carbon\Carbon::parse($guruData['tanggal_lahir'])->format('d/m/Y') : '' }}',
                no_telp: '{{ $guruData['no_telp'] ?? $profileData['no_telp'] ?? '' }}',
                status: '{{ $guruData['status_pegawai'] ?? '' }}',
                agama: '{{ $guruData['agama'] ?? $profileData['agama'] ?? '' }}',
                alamat: '{{ $guruData['alamat'] ?? $profileData['alamat'] ?? '' }}',
                foto: '{{ asset('storage/guru/img/' . ($guruData['foto'] ?? $profileData['foto'] ?? 'default_img.png')) }}',
                signature: '{{ asset('storage/guru/signatures/' . ($guruData['signature'] ?? 'default_signature.png')) }}',
            };
        @endforeach

        function showDetailModal(guruId) {
            const data = guruDataMap[guruId];
            if (!data) return;
            
            document.getElementById('modal-nip').innerText = data.nip;
            document.getElementById('modal-nama').innerText = data.nama;
            document.getElementById('modal-jk').innerText = data.jenis_kelamin;
            document.getElementById('modal-ttl').innerText = data.ttl;
            document.getElementById('modal-telp').innerText = data.no_telp;
            document.getElementById('modal-status').innerText = data.status;
            document.getElementById('modal-agama').innerText = data.agama;
            document.getElementById('modal-alamat').innerText = data.alamat;
            document.getElementById('modal-foto').querySelector('img').src = data.foto;
            document.getElementById('modal-signature').querySelector('img').src = data.signature;
            
            // Tampilkan modal
            const modal = new bootstrap.Modal(document.getElementById('detail-modal-guru'));
            modal.show();
        }
    </script>
@endsection