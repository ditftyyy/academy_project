@extends('components.main')

@section('breadcrumbs')
<ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="/dashboard">Dashboard</a></li>
    <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Absensi Admin</li>
</ol>
<h6 class="font-weight-bolder mb-0">Data Absensi</h6>
@endsection

@once
@php
    function safeParseDateAdmin($dateStr) {
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
@endonce

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                    <h6 class="text-white text-capitalize ps-3">Absensi Admin</h6>
                </div>
            </div>

            {{-- Charts --}}
            <div class="d-flex justify-content-between mx-8 my-4" style="height: 500px">
                <div class="card" style="flex: 1; margin-right: 10px;">
                    <div class="card-body">
                        <h5 class="card-title text-center fw-bold">Rekap Data Siswa</h5>
                        <div id="chart-siswa" class="chart-lg"></div>
                    </div>
                </div>
                <div class="card" style="flex: 1; margin-left: 10px;">
                    <div class="card-body">
                        <h5 class="card-title text-center fw-bold">Rekap Data Guru</h5>
                        <div id="chart-guru" class="chart-lg"></div>
                    </div>
                </div>
            </div>

            {{-- Tabel Siswa --}}
            <div class="mb-4 d-flex align-items-center justify-content-center">
                <div class="col-lg-10 pr-4 mr-2">
                    <div class="border border-2 rounded p-4 my-4">
                        <h5 class="fw-bold">Data Siswa</h5>
                        <div class="input-group mb-3">
                            <input type="text" id="searchSiswa" class="form-control rounded" placeholder="Cari siswa...">
                            <select id="dropdownkelas" class="form-select rounded ms-2">
                                <option value="semua">Semua Kelas</option>
                            </select>
                        </div>
                        <div class="table-responsive" style="max-height: 400px; overflow: auto;">
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th><th>Hari</th><th>Nama</th><th>Kelas</th>
                                        <th>Jam</th><th>Status</th><th>File</th><th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBodySiswa">
                                    @forelse ($siswaAbsensis as $item)
                                        @php
                                            $tgl = safeParseDateAdmin($item['created_at'] ?? $item['tanggal'] ?? null);
                                            $jam = safeParseDateAdmin($item['created_at'] ?? $item['tanggal'] ?? null);
                                            $filePath = $item['file_path'] ?? null;
                                        @endphp
                                        <tr>
                                            <td>{{ $tgl->format('d-m-Y') }}</td>
                                            <td>{{ $tgl->locale('id')->isoFormat('dddd') }}</td>
                                            <td class="nama-siswa">{{ $item['nama'] ?? '-' }}</td>
                                            <td class="kelas-siswa">{{ $item['kelas'] ?? '-' }}</td>
                                            <td>{{ $jam->format('H:i:s') }}</td>
                                            <td>{{ ucfirst($item['status'] ?? '-') }}</td>
                                            <td>
                                                @if($filePath)
                                                <a href="{{ route('absensi.file', basename($filePath)) }}" target="_blank">Lihat Surat</a>
                                                    <!-- <a href="{{ asset('storage/absensi_files/' . $filePath) }}" target="_blank" class="btn btn-info btn-sm">Lihat Surat</a> -->
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <button class="btn btn-warning btn-sm" onclick="showEditModal('{{ $item['user_id'] }}', {{ $item['index'] ?? 0 }})"><i class="fa fa-edit"></i></button>
                                                <button class="btn btn-danger btn-sm" onclick="deleteAbsensi('{{ $item['user_id'] }}', {{ $item['index'] ?? 0 }})"><i class="fa fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="8" class="text-center">Belum ada data absensi siswa.
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Tabel Guru --}}
                    <div class="border border-2 rounded p-4 my-4">
                        <h5 class="fw-bold">Data Guru</h5>
                        <div class="input-group mb-3">
                            <input type="text" id="searchGuru" class="form-control rounded" placeholder="Cari guru...">
                            <select id="dropdownkelasGuru" class="form-select rounded ms-2">
                                <option value="semua">Semua Guru</option>
                            </select>
                        </div>
                        <div class="table-responsive" style="max-height: 400px; overflow: auto;">
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th><th>Hari</th><th>Nama</th>
                                        <th>Jam</th><th>Status</th><th>File</th><th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBodyGuru">
                                    @forelse ($guruAbsensis as $item)
                                        @php
                                            $tgl = safeParseDateAdmin($item['created_at'] ?? $item['tanggal'] ?? null);
                                            $jam = safeParseDateAdmin($item['created_at'] ?? $item['tanggal'] ?? null);
                                            $filePath = $item['file_path'] ?? null;
                                        @endphp
                                        <tr>
                                            <td>{{ $tgl->format('d-m-Y') }}</td>
                                            <td>{{ $tgl->locale('id')->isoFormat('dddd') }}</td>
                                            <td class="nama-guru">{{ $item['nama'] ?? '-' }}</td>
                                            <td>{{ $jam->format('H:i:s') }}</td>
                                            <td>{{ ucfirst($item['status'] ?? '-') }}</td>
                                            <td>
                                                @if($filePath)
                                                <a href="{{ route('absensi.file', basename($filePath)) }}" target="_blank">Lihat Surat</a>
                                                    <!-- <a href="{{ asset('storage/absensi_files/' . $filePath) }}" target="_blank" class="btn btn-info btn-sm">Lihat Surat</a> -->
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <button class="btn btn-warning btn-sm" onclick="showEditModal('{{ $item['user_id'] }}', {{ $item['index'] ?? 0 }})"><i class="fa fa-edit"></i></button>
                                                <button class="btn btn-danger btn-sm" onclick="deleteAbsensi('{{ $item['user_id'] }}', {{ $item['index'] ?? 0 }})"><i class="fa fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    @empty
                                        <td><td colspan="7" class="text-center">Belum ada data absensi guru.
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Form Tambah Absensi --}}
                    <div class="border border-2 rounded p-4 my-4">
                        <h5 class="fw-bold mb-3">Tambahkan Absensi</h5>
                        <div class="d-flex">
                            <div class="form-check me-4">
                                <input class="form-check-input" type="radio" name="roleRadio" id="siswaRadio" value="siswa" onclick="selectRole('siswa')">
                                <label class="form-check-label" for="siswaRadio">Siswa</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="roleRadio" id="guruRadio" value="guru" onclick="selectRole('guru')">
                                <label class="form-check-label" for="guruRadio">Guru</label>
                            </div>
                        </div>

                        <div id="dropdownContainer" style="display: none;" class="mt-3">
                            <label class="form-label">Pilih :</label>
                            <select id="dropdown1" class="form-select"></select>
                            <div id="dropdown2Container" style="display: none;">
                                <label class="form-label mt-2">Pilih Siswa:</label>
                                <select id="dropdown2" class="form-select"></select>
                            </div>
                        </div>

                        <div class="d-flex mt-3">
                            <div class="form-check me-4">
                                <input class="form-check-input" type="radio" name="statusOption" id="optMasuk" onclick="selectStatus('masuk')">
                                <label class="form-check-label" for="optMasuk">Masuk</label>
                            </div>
                            <div class="form-check me-4">
                                <input class="form-check-input" type="radio" name="statusOption" id="optSakit" onclick="selectStatus('sakit')">
                                <label class="form-check-label" for="optSakit">Sakit</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="statusOption" id="optIzin" onclick="selectStatus('izin')">
                                <label class="form-check-label" for="optIzin">Izin</label>
                            </div>
                        </div>

                        <div id="fileUploadContainer" style="display: none;" class="mt-3">
                            <label for="fileInput" class="form-label">Unggah File (PDF):</label>
                            <input type="file" id="fileInput" class="form-control" accept=".pdf">
                        </div>

                        <input type="hidden" id="statusInput" value="">
                        <div class="d-flex justify-content-end mt-3">
                            <button type="button" class="btn btn-primary" onclick="submitAdminAbsensi()">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Edit Absensi</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="mb-2"><label>Nama:</label><input type="text" id="editNama" class="form-control" readonly></div>
                <div class="mb-2"><label>Kelas:</label><input type="text" id="editKelas" class="form-control" readonly></div>
                <div class="mb-2"><label>Tanggal:</label><input type="text" id="editTanggal" class="form-control" readonly></div>
                <div class="mb-2"><label>Jam:</label><input type="text" id="editJam" class="form-control" readonly></div>
                <div class="mb-2">
                    <label>Status:</label>
                    <select id="editStatus" class="form-select" onchange="toggleEditKeterangan(this.value)">
                        <option value="masuk">Masuk</option>
                        <option value="sakit">Sakit</option>
                        <option value="izin">Izin</option>
                        <option value="tidak masuk">Tidak Masuk</option>
                    </select>
                </div>
                <div id="editKeteranganContainer" style="display: none;" class="mb-2">
                    <label>Keterangan Izin:</label>
                    <input type="text" id="editKeterangan" class="form-control" placeholder="Alasan izin...">
                </div>
                <input type="hidden" id="editUserId">
                <input type="hidden" id="editIndex">
                <button class="btn btn-primary mt-2" onclick="submitEditForm()">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    // Chart data dari server
    let siswaMasuk = 0, siswaSakit = 0, siswaIzin = 0, siswaTidak = 0;
    @foreach ($siswaAbsensis as $item)
        @php $s = strtolower($item['status'] ?? ''); @endphp
        @if($s == 'masuk') siswaMasuk++; @elseif($s == 'sakit') siswaSakit++; @elseif($s == 'izin') siswaIzin++; @else siswaTidak++; @endif
    @endforeach
    let guruMasuk = 0, guruSakit = 0, guruIzin = 0, guruTidak = 0;
    @foreach ($guruAbsensis as $item)
        @php $s = strtolower($item['status'] ?? ''); @endphp
        @if($s == 'masuk') guruMasuk++; @elseif($s == 'sakit') guruSakit++; @elseif($s == 'izin') guruIzin++; @else guruTidak++; @endif
    @endforeach

    new ApexCharts(document.querySelector("#chart-siswa"), {
        chart: { type: "donut", height: 400 }, series: [siswaMasuk, siswaSakit, siswaIzin, siswaTidak],
        labels: ["Masuk", "Sakit", "Izin", "Tidak Masuk"], colors: ['#2845ff','#Feef50','#20f000','#ff1818']
    }).render();
    new ApexCharts(document.querySelector("#chart-guru"), {
        chart: { type: "donut", height: 400 }, series: [guruMasuk, guruSakit, guruIzin, guruTidak],
        labels: ["Masuk", "Sakit", "Izin", "Tidak Masuk"], colors: ['#2845ff','#Feef50','#20f000','#ff1818']
    }).render();

    // Inisialisasi dropdown filter
    fetch('/get_kelas').then(r=>r.json()).then(data=>{
        let d = document.getElementById('dropdownkelas');
        d.innerHTML = '<option value="semua">Semua Kelas</option>';
        data.forEach(k => d.add(new Option(k, k)));
    });
    fetch('/get_guru').then(r=>r.json()).then(data=>{
        let d = document.getElementById('dropdownkelasGuru');
        d.innerHTML = '<option value="semua">Semua Guru</option>';
        data.forEach(g => d.add(new Option(g.nama, g.nama)));
    });

    // Filter tabel
    document.getElementById('dropdownkelas').onchange = function(){
        let val = this.value;
        document.querySelectorAll('#tableBodySiswa tr').forEach(row => {
            let kelas = row.querySelector('.kelas-siswa')?.innerText || '';
            row.style.display = (val === 'semua' || kelas === val) ? '' : 'none';
        });
    };
    document.getElementById('searchSiswa').oninput = function(){
        let keyword = this.value.toLowerCase();
        document.querySelectorAll('#tableBodySiswa tr').forEach(row => {
            let nama = row.querySelector('.nama-siswa')?.innerText.toLowerCase() || '';
            row.style.display = nama.includes(keyword) ? '' : 'none';
        });
    };
    document.getElementById('dropdownkelasGuru').onchange = function(){
        let val = this.value;
        document.querySelectorAll('#tableBodyGuru tr').forEach(row => {
            let nama = row.querySelector('.nama-guru')?.innerText || '';
            row.style.display = (val === 'semua' || nama === val) ? '' : 'none';
        });
    };
    document.getElementById('searchGuru').oninput = function(){
        let keyword = this.value.toLowerCase();
        document.querySelectorAll('#tableBodyGuru tr').forEach(row => {
            let nama = row.querySelector('.nama-guru')?.innerText.toLowerCase() || '';
            row.style.display = nama.includes(keyword) ? '' : 'none';
        });
    };

    let selectedRole = null, selectedStatus = null;

    function selectRole(role) {
        selectedRole = role;
        document.getElementById('dropdownContainer').style.display = 'block';
        if(role === 'siswa'){
            document.getElementById('dropdown2Container').style.display = 'block';
            fetch('/get_kelas').then(r=>r.json()).then(data=>{
                let d1 = document.getElementById('dropdown1');
                d1.innerHTML = '<option value="">Pilih Kelas</option>';
                data.forEach(k => d1.add(new Option(k, k)));
            });
        } else {
            document.getElementById('dropdown2Container').style.display = 'none';
            fetch('/get_gurunames').then(r=>r.json()).then(data=>{
                let d1 = document.getElementById('dropdown1');
                d1.innerHTML = '<option value="">Pilih Guru</option>';
                data.forEach(n => d1.add(new Option(n, n)));
            });
        }
    }

    function selectStatus(status) {
        selectedStatus = status;
        document.getElementById('statusInput').value = status;
        document.getElementById('fileUploadContainer').style.display = (status === 'sakit' || status === 'izin') ? 'block' : 'none';
    }

    document.getElementById('dropdown1').addEventListener('change', function(){
        if(selectedRole === 'siswa'){
            let kelas = this.value;
            if(!kelas) return;
            fetch(`/get_siswaadmin?kelas=${encodeURIComponent(kelas)}`).then(r=>r.json()).then(data=>{
                let d2 = document.getElementById('dropdown2');
                d2.innerHTML = '<option value="">Pilih Siswa</option>';
                data.forEach(n => d2.add(new Option(n, n)));
            });
        }
    });

    function submitAdminAbsensi() {
        if (!selectedRole || !selectedStatus) {
            alert('Pilih role dan status terlebih dahulu.');
            return;
        }
        let nama;
        if(selectedRole === 'siswa'){
            nama = document.getElementById('dropdown2').value;
            if(!nama){ alert('Pilih siswa'); return; }
        } else {
            nama = document.getElementById('dropdown1').value;
            if(!nama){ alert('Pilih guru'); return; }
        }
        let formData = new FormData();
        formData.append('status_absen', selectedStatus);
        formData.append('role', selectedRole);
        formData.append('nama_siswa', nama);
        let fileInput = document.getElementById('fileInput');
        if(fileInput.files.length) formData.append('file', fileInput.files[0]);

        fetch('/akademik/absensi/postAbsensi', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            alert(data.message || 'Berhasil!');
            location.reload();
        })
        .catch(err => alert('Gagal menyimpan.'));
    }

    function showEditModal(userId, index) {
        fetch(`/api/absensi/${userId}`).then(r => r.json()).then(res => {
            let abs = res.data.attendances[index];
            document.getElementById('editUserId').value = userId;
            document.getElementById('editIndex').value = index;
            document.getElementById('editNama').value = res.data.user.nama;
            document.getElementById('editKelas').value = res.data.user.kelas || '-';
            document.getElementById('editTanggal').value = abs.created_at?.substring(0,10);
            document.getElementById('editJam').value = abs.created_at?.substring(11,19);
            document.getElementById('editStatus').value = abs.status;
            let keterangan = abs.keterangan || '';
            if (abs.status === 'izin') {
                document.getElementById('editKeteranganContainer').style.display = 'block';
                document.getElementById('editKeterangan').value = keterangan;
            } else {
                document.getElementById('editKeteranganContainer').style.display = 'none';
            }
            new bootstrap.Modal(document.getElementById('editModal')).show();
        }).catch(err => alert('Gagal mengambil data'));
    }

    function submitEditForm() {
        let userId = document.getElementById('editUserId').value;
        let index = document.getElementById('editIndex').value;
        let status = document.getElementById('editStatus').value;
        let keterangan = document.getElementById('editKeterangan').value;
        let formData = new FormData();
        formData.append('status', status);
        if (status === 'izin') formData.append('keterangan_izin', keterangan);
        fetch(`/api/akademik/absensi-update/${userId}/${index}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) location.reload();
            else alert('Gagal: ' + (data.message || 'Unknown error'));
        })
        .catch(err => alert('Error: ' + err));
    }

    function toggleEditKeterangan(value) {
        let container = document.getElementById('editKeteranganContainer');
        container.style.display = (value === 'izin') ? 'block' : 'none';
    }

    function deleteAbsensi(userId, index) {
        if(confirm('Yakin hapus?')){
            fetch(`/api/delete-absensi/${userId}/${index}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            }).then(() => location.reload());
        }
    }
</script>
@endsection