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
                                <optgroup label="✅ GET (JSON)">
                                    <option value="/get_kelas">📋 Daftar Kelas</option>
                                    <option value="/get_guru">👨‍🏫 Daftar Guru</option>
                                    <option value="/get_gurunames">📛 Nama Guru</option>
                                    <option value="/api/get-username-by-role/guru">👥 Username Guru (API)</option>
                                    <option value="/api/get-username-by-role/siswa">👩‍🎓 Username Siswa (API)</option>
                                    <option value="/api/events-from-database">📅 Event Kalender</option>
                                </optgroup>
                                <optgroup label="📄 HTML (tampilan penuh)">
                                    <option value="/akademik/mapel">📖 Daftar Mapel</option>
                                    <option value="/sarana/barang">📦 Daftar Barang</option>
                                    <option value="/sarana/kelas">🏫 Daftar Kelas</option>
                                    <option value="/sarana/ruang">🚪 Daftar Ruang</option>
                                    <option value="/data-tamu">📝 Data Tamu</option>
                                </optgroup>
                            </select>
                            <small class="text-muted">* Semua endpoint menggunakan metode GET. Klik "Kirim Request" untuk melihat response.</small>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Method</label>
                            <input type="text" class="form-control" value="GET" readonly disabled>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label">URL</label>
                            <input type="text" id="url" class="form-control" readonly disabled>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button id="sendRequest" class="btn btn-primary w-100">Kirim Request</button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-bold mb-0">Response:</label>
                                <button id="clearResponse" class="btn btn-outline-secondary btn-sm">🗑️ Clear</button>
                            </div>
                            <pre id="response" class="bg-light p-3 rounded" style="max-height: 500px; overflow-y: auto; min-height: 200px; white-space: pre-wrap;">// Pilih endpoint dan klik Kirim Request</pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const endpointSelect = document.getElementById('endpointSelect');
    const urlInput = document.getElementById('url');
    const responseEl = document.getElementById('response');
    const sendBtn = document.getElementById('sendRequest');
    const clearBtn = document.getElementById('clearResponse');

    // Saat memilih endpoint, set URL
    endpointSelect.addEventListener('change', function() {
        const url = this.value;
        if (!url) {
            urlInput.value = '';
            responseEl.textContent = '// Pilih endpoint terlebih dahulu';
            return;
        }
        urlInput.value = url;
        responseEl.textContent = '// Klik "Kirim Request" untuk melihat response';
    });

    // Kirim request
    sendBtn.addEventListener('click', async function() {
        const url = urlInput.value.trim();
        if (!url) {
            alert('Silakan pilih endpoint terlebih dahulu');
            return;
        }

        responseEl.textContent = 'Loading...';
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        try {
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json, text/html, */*',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                const data = await response.json();
                responseEl.textContent = JSON.stringify(data, null, 2);
            } else {
                const text = await response.text();
                // Tampilkan preview untuk HTML
                if (contentType && contentType.includes('text/html')) {
                    const titleMatch = text.match(/<title>(.*?)<\/title>/i);
                    const title = titleMatch ? titleMatch[1] : 'Halaman HTML';
                    const preview = text.length > 1500 ? text.substring(0, 1500) + '... (truncated)' : text;
                    responseEl.textContent = `Response HTML (${title}):\n\n${preview}`;
                } else {
                    responseEl.textContent = text.length > 2000 ? text.substring(0, 2000) + '...' : text;
                }
            }
        } catch (error) {
            responseEl.textContent = 'Error: ' + error.message;
        }
    });

    clearBtn.addEventListener('click', function() {
        responseEl.textContent = '// Response akan muncul di sini';
    });
</script>
@endsection