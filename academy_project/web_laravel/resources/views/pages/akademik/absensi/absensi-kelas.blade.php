@extends('components.main')

@section('breadcrumbs')
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Absensi Kelas</li>
    </ol>
    <h6 class="font-weight-bolder mb-0">Data Absensi Kelas</h6>
@endsection

@php
    // Fungsi helper untuk parse tanggal (didefinisikan SEKALI di luar loop)
    function safeParseDateAbsensi($dateStr) {
        if (empty($dateStr)) return now();
        if (is_numeric($dateStr)) {
            $timestamp = (strlen((string)$dateStr) > 10) ? (int)($dateStr / 1000) : (int)$dateStr;
            return \Carbon\Carbon::createFromTimestamp($timestamp);
        }
        try {
            return \Carbon\Carbon::parse($dateStr);
        } catch (\Exception $e) {
            return now();
        }
    }
@endphp

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Absensi</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive pb-2 px-3">
                        {{-- Form Filter Kelas & Semester --}}
                        <form action="" method="GET">
                            @csrf
                            <div class="d-flex align-items-center justify-content-end my-3" style="flex-direction: column; row-gap: 10px; max-width: 350px">
                                <div style="display: flex; gap: 10px; width: 100%;">
                                    <div class="form-group mr-2" style="flex-grow: 1">
                                        <select class="form-control" name="selected_kelas" required style="text-transform: capitalize; width: 100%;">
                                            @foreach ($kelas_list as $kelas)
                                                <option value="{{ (string)$kelas->_id }}" @if ($selected_kelas == (string)$kelas->_id) selected @endif>
                                                    {{ strtoupper($kelas->nama_kelas) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 10px">
                                        @foreach (['ganjil', 'genap'] as $semester)
                                            <label style="margin: 0">
                                                <input type="radio" name="selected_semester" value="{{ $semester }}"
                                                    @if ($semester == $selected_semester) checked @endif required>
                                                {{ ucfirst($semester) }}
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-outline-primary btn-sm ml-2" style="width: 100%">Cari</button>
                            </div>
                        </form>

                        {{-- Tabel Absensi --}}
                        <table id="example" class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">No</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Nama Siswa</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Tanggal</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Status</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($absensis as $absensi)
                                    @php
                                        $tanggalRaw = $absensi['created_at'] ?? $absensi['tanggal'] ?? null;
                                        $tanggalObj = safeParseDateAbsensi($tanggalRaw);
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td class="text-center">{{ $absensi['nama'] ?? '-' }}</td>
                                        <td class="text-center">{{ $tanggalObj->format('d-m-Y H:i:s') }}</td>
                                        <td class="text-center">
                                            {{ ucfirst($absensi['status'] ?? '-') }}
                                            @if(($absensi['status'] ?? '') == 'izin' && !empty($absensi['keterangan']))
                                                ({{ $absensi['keterangan'] }})
                                            @endif
                                        </td>
                                        <td class="text-center" style="width: 20%;">
                                            <button type="button" data-bs-toggle="modal" data-bs-target="#update-modal"
                                                class="btn btn-warning font-weight-bold btn--edit text-sm rounded-circle"
                                                data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit"
                                                onclick="showUpdateModal(this)"
                                                data-user-id="{{ $absensi['user_id'] ?? '' }}"
                                                data-absensi-index="{{ $absensi['index'] ?? 0 }}"
                                                data-nama="{{ $absensi['nama'] ?? '' }}"
                                                data-status="{{ $absensi['status'] ?? '' }}"
                                                data-keterangan="{{ $absensi['keterangan'] ?? '' }}">
                                                <i class="fa fa-edit"></i>
                                            </button>
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

    {{-- Modal Edit Absensi --}}
    <div class="modal fade" id="update-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white" id="exampleModalLabel">Edit Absensi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <br>
                    <form id="edit-absensi-form" class="row g-3 px-4" method="POST">
                        @method('POST')
                        @csrf
                        <div class="row g-2 align-items-center px-3" style="justify-content: space-between">
                            <div class="col-auto">
                                <label for="update-nama-siswa" class="col-form-label">Nama Siswa</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" name="nama_siswa" class="form-control text-sm" 
                                    id="update-nama-siswa" required disabled readonly>
                            </div>
                        </div>
                        <br>
                        <div class="row g-2 align-items-center px-3" style="justify-content: space-between">
                            <div class="col-auto">
                                <label for="update-status" class="col-form-label">Status</label>
                            </div>
                            <div class="col-md-9">
                                <select class="form-select rounded-3 form-control-lg text-sm" name="status" id="update-status"
                                    onchange="showKeteranganIzin(this.value);">
                                    <option value="" disabled selected>-- Pilih Status --</option>
                                    @foreach ($list_status as $item)
                                        <option value="{{ $item }}">{{ ucfirst($item) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <br>
                        <div class="row g-2 align-items-center px-3" style="justify-content: space-between; display: none" id="update-keterangan"></div>
                        <br>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showUpdateModal(element) {
            const form = document.querySelector('#edit-absensi-form');
            const namaInput = document.querySelector('#update-nama-siswa');
            const statusSelect = document.querySelector('#update-status');
            const keteranganDiv = document.querySelector('#update-keterangan');
            
            // Ambil data dari atribut
            const userId = element.getAttribute('data-user-id');
            const absensiIndex = element.getAttribute('data-absensi-index');
            const nama = element.getAttribute('data-nama');
            const status = element.getAttribute('data-status');
            const keterangan = element.getAttribute('data-keterangan');
            
            // Set form action (MongoDB: pakai userId dan index)
            form.setAttribute('action', `/api/update-absensi/${userId}/${absensiIndex}`);
            
            // Isi form
            namaInput.value = nama;
            statusSelect.value = status;
            
            // Tampilkan/sembunyikan keterangan
            if (status === 'izin') {
                keteranganDiv.style.display = 'flex';
                keteranganDiv.innerHTML = `
                    <div class="col-auto">
                        <label for="update-keterangan-input" class="col-form-label">Keterangan</label>
                    </div>
                    <div class="col-md-9">
                        <input id="update-keterangan-input" type="text" name="keterangan_izin"
                            class="form-control text-sm" value="${keterangan || ''}" required>
                    </div>
                `;
            } else {
                keteranganDiv.style.display = 'none';
                keteranganDiv.innerHTML = '';
            }
        }
        
        function showKeteranganIzin(value) {
            const keteranganDiv = document.querySelector('#update-keterangan');
            if (value === 'izin') {
                keteranganDiv.style.display = 'flex';
                keteranganDiv.innerHTML = `
                    <div class="col-auto">
                        <label for="update-keterangan-input" class="col-form-label">Keterangan</label>
                    </div>
                    <div class="col-md-9">
                        <input id="update-keterangan-input" type="text" name="keterangan_izin"
                            class="form-control text-sm" value="" required>
                    </div>
                `;
            } else {
                keteranganDiv.style.display = 'none';
                keteranganDiv.innerHTML = '';
            }
        }
    </script>
@endsection