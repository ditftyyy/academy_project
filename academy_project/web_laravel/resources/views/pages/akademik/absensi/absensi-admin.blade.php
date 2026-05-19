@extends('components.main')

@section('breadcrumbs')
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Absensi Admin</li>
    </ol>
    <h6 class="font-weight-bolder mb-0">Data Absensi</h6>
@endsection

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
                <h5 class="card-title text-center" style="font-weight: bold;">Rekap Data Siswa</h5>
                <div id="chart-siswa" class="chart-lg"></div>
            </div>
        </div>
        <div style="width: 20px;"></div>
        <div class="card" style="flex: 1; margin-left: 10px;">
            <div class="card-body">
                <h5 class="card-title text-center" style="font-weight: bold;">Rekap Data Guru</h5>
                <div id="chart-guru" class="chart-lg"></div>
            </div>
        </div>
      </div>
      
      {{-- Tabel Siswa --}}
      <div class="mb-4 d-flex align-items-center justify-content-center">
        <div class="col-lg-10 pr-4 mr-2">
          <div class="border border-2 rounded p-4 my-4 d-flex flex-column text-md" style="height: 450px; max-height: 450px; position: relative;">
            <h5 class="position-relative" style="font-weight: bold; position: sticky; top: 0; background-color: white; z-index: 100;">
                Data Siswa
            </h5>
            <div class="input-group mb-3" style="position: sticky; top: 40px; background-color: white; z-index: 99;">
                <div style="margin-right: 20px;">
                    <input type="text" class="form-control rounded" placeholder="Search..." id="searchSiswa" style="border: 2px solid lightblue; width: 350px;">
                </div>
                <div class="col-2 mx-3 ml-5">
                    <select class="form-select rounded" id="dropdownkelas" style="border: 2px solid lightblue;">
                        <option value="semua">Semua Siswa</option>
                    </select>
                </div>
                <div class="col-2 mx-3" id="dropdownsiswacontainer" style="display: none;">
                    <select class="form-select rounded" id="dropdownsiswa" style="border: 2px solid lightblue;"></select>
                </div>
            </div>
            <div class="table-responsive small col-lg-12" style="flex: 1; overflow: auto;">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Hari</th>
                            <th>Nama</th>
                            <th>Kelas</th>
                            <th>Jam Absen</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="tableBodySiswa">
                        {{-- MONGODB: Data dari AbsensiController (array) --}}
                        @foreach ($siswaAbsensis as $item)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($item['created_at'] ?? $item['tanggal'] ?? now())->format('d-m-Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($item['created_at'] ?? $item['tanggal'] ?? now())->locale('id')->isoFormat('dddd') }}</td>
                                <td class="nama-siswa">{{ $item['nama'] ?? '-' }}</td>
                                <td class="kelas-siswa">{{ $item['kelas'] ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($item['created_at'] ?? now())->format('H:i:s') }}</td>
                                <td>{{ $item['status'] ?? '-' }}</td>
                                <td>
                                    @if(!empty($item['file_path']))
                                        <a href="{{ asset('storage/' . $item['file_path']) }}" class="btn btn-info btn-sm" target="_blank">File</a>
                                    @endif
                                    <button class="btn btn-warning btn-sm" onclick="showEditModal('{{ $item['user_id'] }}', {{ $item['index'] ?? 0 }})">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="deleteAbsensi('{{ $item['user_id'] }}', {{ $item['index'] ?? 0 }})">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
          </div>
          
          {{-- Tabel Guru --}}
          <div class="border border-2 rounded p-4 my-4 d-flex flex-column text-md" style="height: 450px; max-height: 450px; position: relative;">
            <h5 class="position-relative" style="font-weight: bold; position: sticky; top: 0; background-color: white; z-index: 100;">
                Data Guru
            </h5>
            <div class="input-group mb-3" style="position: sticky; top: 40px; background-color: white; z-index: 99;">
                <div style="margin-right: 20px;">
                    <input type="text" class="form-control rounded" placeholder="Search..." id="searchGuru" style="border: 2px solid lightblue; width: 350px;">
                </div>
                <div class="col-2 mx-3 ml-5">
                    <select class="form-select rounded" id="dropdownkelasGuru" style="border: 2px solid lightblue;"></select>
                </div>
            </div>
            <div class="table-responsive small col-lg-12" style="flex: 1; overflow: auto;">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Hari</th>
                            <th>Nama</th>
                            <th>Jam Absen</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="tableBodyGuru">
                        @foreach ($guruAbsensis as $item)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($item['created_at'] ?? $item['tanggal'] ?? now())->format('d-m-Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($item['created_at'] ?? $item['tanggal'] ?? now())->locale('id')->isoFormat('dddd') }}</td>
                                <td class="nama-guru">{{ $item['nama'] ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($item['created_at'] ?? now())->format('H:i:s') }}</td>
                                <td>{{ $item['status'] ?? '-' }}</td>
                                <td>
                                    @if(!empty($item['file_path']))
                                        <a href="{{ asset('storage/' . $item['file_path']) }}" class="btn btn-info btn-sm" target="_blank">File</a>
                                    @endif
                                    <button class="btn btn-warning btn-sm" onclick="showEditModal('{{ $item['user_id'] }}', {{ $item['index'] ?? 0 }})">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="deleteAbsensi('{{ $item['user_id'] }}', {{ $item['index'] ?? 0 }})">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
          </div>
          
          {{-- Form Tambah Absensi --}}
          <form id="absensiForm" enctype="multipart/form-data">
            <div class="border border-2 rounded p-4 my-4 d-flex flex-column text-md" style="height: auto; max-height: 550px;">
                <h5 class="font-weight-bold mb-3">Tambahkan Absensi</h5>
                
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="roleRadio" id="siswaRadio" value="siswa" onclick="selectRole('siswa')">
                    <label class="form-check-label" for="siswaRadio">Siswa</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="roleRadio" id="guruRadio" value="guru" onclick="selectRole('guru')">
                    <label class="form-check-label" for="guruRadio">Guru</label>
                </div>
                
                <div class="mb-3" id="dropdownContainer" style="display: none;">
                    <label for="dropdown1" class="form-label">Pilih :</label>
                    <select class="form-select" id="dropdown1" name="dropdown1"></select>
                    <div class="mb-3" id="dropdown2Container" style="display: none;">
                        <label for="dropdown2" class="form-label">Pilih Siswa:</label>
                        <select class="form-select" id="dropdown2" name="dropdown2"></select>
                    </div>
                </div>
                
                <div class="d-flex justify-content-center">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="statusOption" id="optMasuk" onclick="selectStatus('masuk')">
                        <label class="form-check-label" for="optMasuk">Masuk</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="statusOption" id="optSakit" onclick="selectStatus('sakit')">
                        <label class="form-check-label" for="optSakit">Sakit</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="statusOption" id="optIzin" onclick="selectStatus('izin')">
                        <label class="form-check-label" for="optIzin">Izin</label>
                    </div>
                </div>
                
                <div class="file-upload-container" id="fileUploadContainer" style="display: none;">
                    <div class="mb-3">
                        <label for="fileInput" class="form-label">Unggah File (PDF):</label>
                        <input type="file" class="form-control" id="fileInput" name="file" accept=".pdf">
                    </div>
                </div>
                
                <input type="hidden" name="status_absen" id="statusInput" value="">
                
                <div class="d-flex justify-content-end mt-3">
                    <button type="button" class="submit-button" onclick="submitAdminAbsensi()" id="submitButton">Submit</button>
                </div>
            </div>
          </form>
          
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Modal Edit --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Absensi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <div class="mb-3">
                        <label class="form-label">Nama:</label>
                        <input type="text" class="form-control" id="editNama" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kelas:</label>
                        <input type="text" class="form-control" id="editKelas" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal:</label>
                        <input type="text" class="form-control" id="editTanggal" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jam Absen:</label>
                        <input type="text" class="form-control" id="editJam" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status:</label>
                        <select class="form-select" id="editStatus" name="status">
                            <option value="masuk">Masuk</option>
                            <option value="sakit">Sakit</option>
                            <option value="izin">Izin</option>
                            <option value="tidak masuk">Tidak Masuk</option>
                        </select>
                    </div>
                    <input type="hidden" id="editUserId">
                    <input type="hidden" id="editIndex">
                    <button type="button" class="btn btn-primary" onclick="submitEditForm()">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.form-check-label { font-size: 20px; margin-right: 10px; }
.form-check { margin-right: 40px; }
.submit-button {
    border: none; border-radius: 5px;
    background-color: #007bff; color: #fff;
    padding: 10px 20px; cursor: pointer;
}
</style>

<script>
    // ============================================
    // CHARTS
    // ============================================
    document.addEventListener("DOMContentLoaded", function() {
        // Hitung data siswa
        let siswaMasuk = 0, siswaSakit = 0, siswaIzin = 0, siswaTidakMasuk = 0;
        @foreach ($siswaAbsensis as $item)
            @php $status = strtolower($item['status'] ?? ''); @endphp
            @if($status == 'masuk') siswaMasuk++;
            @elseif($status == 'sakit') siswaSakit++;
            @elseif($status == 'izin') siswaIzin++;
            @else siswaTidakMasuk++;
            @endif
        @endforeach
        
        // Hitung data guru
        let guruMasuk = 0, guruSakit = 0, guruIzin = 0, guruTidakMasuk = 0;
        @foreach ($guruAbsensis as $item)
            @php $status = strtolower($item['status'] ?? ''); @endphp
            @if($status == 'masuk') guruMasuk++;
            @elseif($status == 'sakit') guruSakit++;
            @elseif($status == 'izin') guruIzin++;
            @else guruTidakMasuk++;
            @endif
        @endforeach
        
        // Render charts
        if (window.ApexCharts) {
            // Chart Siswa
            new ApexCharts(document.getElementById('chart-siswa'), {
                chart: { type: "donut", height: 400 },
                series: [siswaMasuk, siswaSakit, siswaIzin, siswaTidakMasuk],
                labels: ["Masuk", "Sakit", "Izin", "Tidak Masuk"],
                colors: ['#2845ff', '#Feef50', '#20f000', '#ff1818'],
                legend: { position: 'bottom' },
            }).render();
            
            // Chart Guru
            new ApexCharts(document.getElementById('chart-guru'), {
                chart: { type: "donut", height: 400 },
                series: [guruMasuk, guruSakit, guruIzin, guruTidakMasuk],
                labels: ["Masuk", "Sakit", "Izin", "Tidak Masuk"],
                colors: ['#2845ff', '#Feef50', '#20f000', '#ff1818'],
                legend: { position: 'bottom' },
            }).render();
        }
        
        // Inisialisasi dropdown
        initDropdowns();
    });
    
    // ============================================
    // VARIABEL
    // ============================================
    let selectedRole = null;
    let selectedStatus = null;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    
    // ============================================
    // FUNGSI DROPDOWN
    // ============================================
    function initDropdowns() {
        // Isi dropdown kelas
        fetch('/get_kelas')
            .then(r => r.json())
            .then(data => {
                const dropdown = document.getElementById('dropdownkelas');
                data.forEach(kelas => {
                    dropdown.add(new Option(kelas, kelas));
                });
            });
        
        // Isi dropdown guru
        fetch('/get_guru')
            .then(r => r.json())
            .then(data => {
                const dropdown = document.getElementById('dropdownkelasGuru');
                dropdown.add(new Option('Semua Guru', 'semua'));
                data.forEach(guru => {
                    dropdown.add(new Option(guru.nama, guru.nama));
                });
            });
    }
    
    function selectRole(role) {
        selectedRole = role;
        document.getElementById('dropdownContainer').style.display = 'block';
        
        if (role === 'siswa') {
            document.getElementById('dropdown2Container').style.display = 'block';
            // Isi dropdown1 dengan kelas
            fetch('/get_kelas')
                .then(r => r.json())
                .then(data => {
                    const d1 = document.getElementById('dropdown1');
                    d1.innerHTML = '<option value="">Pilih Kelas</option>';
                    data.forEach(k => d1.add(new Option(k, k)));
                });
        } else {
            document.getElementById('dropdown2Container').style.display = 'none';
            // Isi dropdown1 dengan nama guru
            fetch('/get_gurunames')
                .then(r => r.json())
                .then(data => {
                    const d1 = document.getElementById('dropdown1');
                    d1.innerHTML = '<option value="">Pilih Guru</option>';
                    data.forEach(n => d1.add(new Option(n, n)));
                });
        }
    }
    
    // Event: saat kelas dipilih, isi dropdown siswa
    document.getElementById('dropdown1').addEventListener('change', function() {
        if (selectedRole === 'siswa') {
            fetch('/get_siswaadmin?kelas=' + this.value)
                .then(r => r.json())
                .then(data => {
                    const d2 = document.getElementById('dropdown2');
                    d2.innerHTML = '<option value="">Pilih Siswa</option>';
                    data.forEach(n => d2.add(new Option(n, n)));
                });
        }
    });
    
    // Filter tabel
    document.getElementById('dropdownkelas').addEventListener('change', function() {
        const kelas = this.value;
        document.querySelectorAll('#tableBodySiswa tr').forEach(row => {
            const kelasCell = row.querySelector('.kelas-siswa');
            row.style.display = (kelas === 'semua' || kelasCell.textContent.trim() === kelas) ? '' : 'none';
        });
    });
    
    document.getElementById('dropdownkelasGuru').addEventListener('change', function() {
        const guru = this.value;
        document.querySelectorAll('#tableBodyGuru tr').forEach(row => {
            const namaCell = row.querySelector('.nama-guru');
            row.style.display = (guru === 'semua' || namaCell.textContent.trim() === guru) ? '' : 'none';
        });
    });
    
    // Search
    document.getElementById('searchSiswa').addEventListener('input', function() {
        const search = this.value.toLowerCase();
        document.querySelectorAll('#tableBodySiswa tr').forEach(row => {
            const nama = row.querySelector('.nama-siswa').textContent.toLowerCase();
            row.style.display = nama.includes(search) ? '' : 'none';
        });
    });
    
    document.getElementById('searchGuru').addEventListener('input', function() {
        const search = this.value.toLowerCase();
        document.querySelectorAll('#tableBodyGuru tr').forEach(row => {
            const nama = row.querySelector('.nama-guru').textContent.toLowerCase();
            row.style.display = nama.includes(search) ? '' : 'none';
        });
    });
    
    // ============================================
    // FUNGSI STATUS
    // ============================================
    function selectStatus(status) {
        selectedStatus = status;
        document.getElementById('statusInput').value = status;
        document.getElementById('fileUploadContainer').style.display = 
            (status === 'sakit' || status === 'izin') ? 'block' : 'none';
    }
    
    // ============================================
    // FUNGSI SUBMIT ADMIN
    // ============================================
    function submitAdminAbsensi() {
        if (!selectedRole || !selectedStatus) {
            alert('Lengkapi semua data.');
            return;
        }
        
        const nama = selectedRole === 'siswa' 
            ? document.getElementById('dropdown2').value 
            : document.getElementById('dropdown1').value;
        
        if (!nama) {
            alert('Pilih nama.');
            return;
        }
        
        const formData = new FormData();
        formData.append('status_absen', selectedStatus);
        formData.append('role', selectedRole);
        formData.append('nama_siswa', nama);
        
        const fileInput = document.getElementById('fileInput');
        if (fileInput.files.length > 0) {
            formData.append('file', fileInput.files[0]);
        }
        
        fetch('{{ route('absensi.storeAdmin') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            body: formData,
        })
        .then(r => r.json())
        .then(data => {
            alert(data.message || 'Berhasil!');
            location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal menyimpan data.');
        });
    }
    
    // ============================================
    // FUNGSI EDIT
    // ============================================
    function showEditModal(userId, index) {
        fetch(`/api/absensi/${userId}`)
            .then(r => r.json())
            .then(response => {
                const user = response.data.user;
                const absensi = response.data.attendances[index];
                
                document.getElementById('editUserId').value = userId;
                document.getElementById('editIndex').value = index;
                document.getElementById('editNama').value = user.nama || '-';
                document.getElementById('editKelas').value = user.kelas || '-';
                document.getElementById('editStatus').value = absensi.status;
                
                const date = new Date(absensi.created_at);
                document.getElementById('editTanggal').value = date.toLocaleDateString('id-ID');
                document.getElementById('editJam').value = date.toLocaleTimeString('id-ID');
                
                new bootstrap.Modal(document.getElementById('editModal')).show();
            });
    }
    
    function submitEditForm() {
        const userId = document.getElementById('editUserId').value;
        const index = document.getElementById('editIndex').value;
        const status = document.getElementById('editStatus').value;
        
        fetch(`/api/update-absensi/${userId}/${index}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ status_absen: status }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
                location.reload();
            }
        });
    }
    
    // ============================================
    // FUNGSI DELETE
    // ============================================
    function deleteAbsensi(userId, index) {
        if (!confirm('Yakin hapus absensi ini?')) return;
        
        fetch(`/api/delete-absensi/${userId}/${index}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken },
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) location.reload();
        });
    }
</script>
@endsection