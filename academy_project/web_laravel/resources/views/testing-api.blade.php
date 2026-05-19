@extends('components.main')

@section('title', 'Testing API')

@section('breadcrumbs')
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Testing API</li>
    </ol>
    <h6 class="font-weight-bolder mb-0">Testing API</h6>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Testing API Endpoints</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="container-fluid px-3">
                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">Pilih Endpoint</label>
                                <select id="endpointSelect" class="form-select">
                                    <option value="">-- Pilih Endpoint --</option>
                                    <!-- 📋 GET -->
                                    <optgroup label="📋 GET (Lihat Data)">
                                        <option value="GET|/get_kelas|">Daftar Kelas</option>
                                        <option value="GET|/get_guru|">Daftar Guru</option>
                                        <option value="GET|/get_gurunames|">Nama Guru</option>
                                        <option value="GET|/get_siswa?kelas=X IPA 1|">Siswa per Kelas</option>
                                        <option value="GET|/data-tamu|">Data Tamu</option>
                                        <option value="GET|/api/events-from-database|">Event Kalender</option>
                                        <option value="GET|/akademik/mapel|">Daftar Mapel (HTML)</option>
                                        <option value="GET|/sarana/barang|">Daftar Barang</option>
                                        <option value="GET|/sarana/kelas|">Daftar Kelas (HTML)</option>
                                        <option value="GET|/sarana/ruang|">Daftar Ruang</option>
                                        <option value="GET|/api/absensi/GANTI_USER_ID|">Absensi User</option>
                                        <option value="GET|/get-username-by-role/guru|">Username Guru</option>
                                    </optgroup>
                                    <!-- ➕ POST -->
                                    <optgroup label="➕ POST (Tambah Data)">
                                        <option value='POST|/akademik/mapel-tambah|{"nama_mapel":"Biologi"}'>Tambah Mapel</option>
                                        <option value='POST|/tamu|{"namaTamu":"John","alamatTamu":"Jl. A","Opsi":"guru","Opsi_Lanjutan":"guru1","keteranganTamu":"Tes"}'>Tambah Tamu</option>
                                        <option value='POST|/sarana/kelas-tambah|{"nama_kelas":"XII IPS 4","id_guru":"ganti_id"}'>Tambah Kelas</option>
                                        <option value='POST|/sarana/ruang-tambah|{"nama_ruang":"Lab Komputer 2","luas":60,"lokasi":"Lantai 1"}'>Tambah Ruang</option>
                                        <option value='POST|/dashboard/buat-pengumuman|{"title":"Libur","message":"Besok libur","roles":["guru","siswa"]}'>Buat Pengumuman</option>
                                        <option value='POST|/peminjaman-tambah|{"ruang_id":"ganti_id","nama_peminjam":"Andi","tgl_peminjaman":"2025-01-10","tgl_pengembalian":"2025-01-12"}'>Peminjaman Ruang</option>
                                        <option value='POST|/data-peminjaman-barang|{"barang_id":"ganti_id","jumlah":2,"nama_peminjam":"Andi","tanggal_peminjaman":"2025-01-10","tanggal_pengembalian":"2025-01-12"}'>Peminjaman Barang</option>
                                        <option value='POST|/data-pegawai-insert|{"nip":"19900101","nama":"Joko","notelp":"081234","tempatlahir":"Jkt","tgllahir":"1990-01-01","alamat":"Jl. A"}'}>Tambah Pegawai</option>
                                    </optgroup>
                                    <!-- ✏️ PUT -->
                                    <optgroup label="✏️ PUT (Update Data)">
                                        <option value='PUT|/akademik/mapel-update/GANTI_ID|{"nama_mapel":"Biologi Update"}'>Update Mapel</option>
                                        <option value='PUT|/tamu-edit/GANTI_ID|{"namaTamu":"John Updated","alamatTamu":"Jl. B","Opsi":"siswa","Opsi_Lanjutan":"siswa1","keteranganTamu":"Ambil rapor"}'>Update Tamu</option>
                                        <option value='PUT|/dashboard/update-pengumuman/GANTI_ID|{"title":"Libur Nasional","message":"Besok libur total","roles":["siswa","guru"]}'>Update Pengumuman</option>
                                        <option value='PUT|/sarana/ruang-update|{"id_ruang":"ganti_ruang","nama_ruang":"Lab Komputer 3","luas":70,"lokasi":"Lantai 2"}'>Update Ruang</option>
                                        <option value='PUT|/data-peminjaman-barang|{"id":"ganti_id","nama_peminjam":"Andi Baru"}'>Update Peminjaman Barang</option>
                                    </optgroup>
                                    <!-- 🗑️ DELETE -->
                                    <optgroup label="🗑️ DELETE (Hapus Data)">
                                        <option value="DELETE|/akademik/mapel-hapus/GANTI_ID|">Hapus Mapel</option>
                                        <option value="DELETE|/tamu-delete/GANTI_ID|">Hapus Tamu</option>
                                        <option value="DELETE|/sarana/kelas-hapus/GANTI_ID|">Hapus Kelas</option>
                                        <option value="DELETE|/sarana/ruang-hapus/GANTI_ID|">Hapus Ruang</option>
                                        <option value="DELETE|/sarana/barang-hapus/GANTI_ID|">Hapus Barang</option>
                                        <option value="DELETE|/data-peminjaman-barang-hapus/GANTI_ID|">Hapus Peminjaman Barang</option>
                                        <option value="DELETE|/peminjaman-hapus/GANTI_ID|">Hapus Peminjaman Ruang</option>
                                        <option value="DELETE|/dashboard/hapus-pengumuman/GANTI_ID|">Hapus Pengumuman</option>
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label">Method</label>
                                <select id="method" class="form-select">
                                    <option value="GET">GET</option>
                                    <option value="POST">POST</option>
                                    <option value="PUT">PUT</option>
                                    <option value="DELETE">DELETE</option>
                                </select>
                            </div>
                            <div class="col-md-7">
                                <label class="form-label">URL</label>
                                <input type="text" id="url" class="form-control" 
                                       placeholder="/api/absensi/userId" value="/get_kelas">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button id="sendRequest" class="btn btn-primary w-100">Send</button>
                            </div>
                        </div>
                        <div id="bodyContainer" class="row mb-3" style="display:none;">
                            <div class="col-12">
                                <label class="form-label">Body (JSON)</label>
                                <textarea id="bodyJson" class="form-control" rows="5" 
                                          placeholder='{"key": "value"}'></textarea>
                                <small class="text-warning" id="idWarning" style="display:none;">⚠️ Jangan lupa ganti <code>GANTI_ID</code> dengan ID yang valid!</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label fw-bold mb-0">Response:</label>
                                    <button id="clearResponse" class="btn btn-outline-secondary btn-sm">🗑️ Clear</button>
                                </div>
                                <pre id="response" class="bg-light p-3 rounded" 
                                     style="max-height: 500px; overflow-y: auto; min-height: 200px;">// Response akan muncul di sini</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // 📌 Dropdown endpoint → isi form
        const endpointSelect = document.getElementById('endpointSelect');
        const methodSelect = document.getElementById('method');
        const urlInput = document.getElementById('url');
        const bodyJson = document.getElementById('bodyJson');
        const bodyContainer = document.getElementById('bodyContainer');
        const idWarning = document.getElementById('idWarning');
        const responseEl = document.getElementById('response');
        const clearBtn = document.getElementById('clearResponse');

        endpointSelect.addEventListener('change', function() {
            const val = this.value;
            if (!val) return;
            const parts = val.split('|');
            methodSelect.value = parts[0];
            urlInput.value = parts[1];
            bodyJson.value = parts[2] || '';
            
            const showBody = (parts[0] === 'POST' || parts[0] === 'PUT');
            bodyContainer.style.display = showBody ? 'block' : 'none';
            
            if (urlInput.value.includes('GANTI_ID') || urlInput.value.includes('ganti_id')) {
                idWarning.style.display = 'block';
            } else {
                idWarning.style.display = 'none';
            }
        });

        methodSelect.addEventListener('change', function() {
            const showBody = (this.value === 'POST' || this.value === 'PUT');
            bodyContainer.style.display = showBody ? 'block' : 'none';
        });

        // Kirim request
        document.getElementById('sendRequest').addEventListener('click', function() {
            const method = methodSelect.value;
            const url = urlInput.value.trim();
            
            if (url.includes('GANTI_ID') || url.includes('ganti_id')) {
                alert('⚠️ Mohon ganti "GANTI_ID" atau "ganti_id" pada URL dengan ID yang valid (contoh: 6601a2c4e3f4b3a2c4e3f4b1).');
                return;
            }
            
            responseEl.textContent = 'Loading...';

            const options = {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            };

            if (method === 'POST' || method === 'PUT') {
                const bodyText = bodyJson.value.trim();
                if (bodyText) {
                    try {
                        JSON.parse(bodyText);
                        options.body = bodyText;
                    } catch (e) {
                        responseEl.textContent = 'Error: Invalid JSON format.';
                        return;
                    }
                }
            }

            fetch(url, options)
                .then(async response => {
                    const text = await response.text();
                    try {
                        return JSON.parse(text);
                    } catch {
                        return { status: response.status, body: text };
                    }
                })
                .then(data => {
                    responseEl.textContent = JSON.stringify(data, null, 2);
                })
                .catch(error => {
                    responseEl.textContent = 'Error: ' + error.message;
                });
        });

        clearBtn.addEventListener('click', function() {
            responseEl.textContent = '// Response akan muncul di sini';
        });
    </script>
@endsection