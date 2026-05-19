@extends('components.main')

@section('title-content')
    Data Pegawai
@endsection

@section('breadcrumbs')
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="/data-pegawai">Pegawai</a></li>
        <li class="breadcrumb-item text-sm text-dark active" aria-current="page"></li>
    </ol>
    <h6 class="font-weight-bolder mb-0">Data Pegawai</h6>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Data Pegawai</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive pb-2 px-3">
                        <a href="data-pegawai-add" class="btn btn-primary font-weight-bold text-xs">
                            <i class="material-icons opacity-10">add</i> Tambah
                        </a>
                        
                        <table id="example" class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">No</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">NIP</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Nama Lengkap</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Jabatan</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">No Telepon</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($user as $u)
                                    @php
                                        $profileData = $u->profile ?? [];
                                        $nip = $profileData['nip'] ?? '-';
                                        $nama = $profileData['nama_lengkap'] ?? $u->nama_lengkap;
                                        $jabatan = $profileData['jabatan'] ?? '-';
                                        $noTelp = $profileData['no_telp'] ?? '-';
                                        $foto = $profileData['foto'] ?? null;
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td class="text-center">{{ $nip }}</td>
                                        <td class="text-center">{{ $nama }}</td>
                                        <td class="text-center">
                                            @if ($jabatan == 'waka')
                                                Wakil Kepala Sekolah
                                            @else
                                                {{ $jabatan }}
                                            @endif
                                        </td>
                                        <td class="align-middle text-center">{{ $noTelp }}</td>
                                        <td class="text-center">
                                            <button type="button" data-bs-toggle="modal" data-bs-target="#detail-modal-{{ (string)$u->_id }}"
                                                class="btn btn-info font-weight-bold text-sm rounded-circle"
                                                data-bs-toggle="tooltip" data-bs-placement="bottom" title="Detail">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                            <a href="data-pegawai-edit/{{ (string)$u->_id }}"
                                                class="btn btn-warning font-weight-bold text-sm rounded-circle"
                                                data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="data-pegawai-hapus/{{ (string)$u->_id }}"
                                                onclick="return confirm('Anda yakin akan menghapus data ini?')"
                                                class="btn btn-danger font-weight-bold text-sm rounded-circle"
                                                data-bs-toggle="tooltip" data-bs-placement="bottom" title="Hapus">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    
                                    {{-- Modal Detail --}}
                                    <div class="modal fade" id="detail-modal-{{ (string)$u->_id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary">
                                                    <h5 class="modal-title text-white">Detail Pegawai</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="foto">
                                                                <img src="{{ $foto ? asset('assets/img/pegawai/' . $foto) : asset('assets/img/thumbnail.png') }}"
                                                                    alt="" width="100%" height="auto">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <ul class="list-group">
                                                                @php
                                                                    $ttl = ($profileData['tempat_lahir'] ?? '') . ', ';
                                                                    $ttl .= isset($profileData['tanggal_lahir']) ? \Carbon\Carbon::parse($profileData['tanggal_lahir'])->format('d/m/Y') : '';
                                                                @endphp
                                                                <li class="list-group-item"><div class="row"><div class="col-md-5"><span class="float-start fw-bold">NIP</span><div class="float-end">:</div></div><div class="col-md-7">{{ $nip }}</div></div></li>
                                                                <li class="list-group-item"><div class="row"><div class="col-md-5"><span class="float-start fw-bold">Nama</span><div class="float-end">:</div></div><div class="col-md-7">{{ $nama }}</div></div></li>
                                                                <li class="list-group-item"><div class="row"><div class="col-md-5"><span class="float-start fw-bold">Jabatan</span><div class="float-end">:</div></div><div class="col-md-7">{{ $jabatan == 'waka' ? 'Wakil Kepala Sekolah' : $jabatan }}</div></div></li>
                                                                <li class="list-group-item"><div class="row"><div class="col-md-5"><span class="float-start fw-bold">Jenis Kelamin</span><div class="float-end">:</div></div><div class="col-md-7">{{ $profileData['jenis_kelamin'] ?? '-' }}</div></div></li>
                                                                <li class="list-group-item"><div class="row"><div class="col-md-5"><span class="float-start fw-bold">Tempat, tanggal lahir</span><div class="float-end">:</div></div><div class="col-md-7">{{ $ttl ?: '-' }}</div></div></li>
                                                                <li class="list-group-item"><div class="row"><div class="col-md-5"><span class="float-start fw-bold">No Telepon</span><div class="float-end">:</div></div><div class="col-md-7">{{ $noTelp }}</div></div></li>
                                                                <li class="list-group-item"><div class="row"><div class="col-md-5"><span class="float-start fw-bold">Agama</span><div class="float-end">:</div></div><div class="col-md-7">{{ $profileData['agama'] ?? '-' }}</div></div></li>
                                                                <li class="list-group-item"><div class="row"><div class="col-md-5"><span class="float-start fw-bold">Alamat</span><div class="float-end">:</div></div><div class="col-md-7">{{ $profileData['alamat'] ?? '-' }}</div></div></li>
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
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection