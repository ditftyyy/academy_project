@extends('components.main')

@section('breadcrumbs')
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Absensi</li>
    </ol>
    <h6 class="font-weight-bolder mb-0">Data Absensi Guru</h6>
@endsection

@php
    // Fungsi helper untuk parse tanggal (didefinisikan SEKALI di luar loop)
    function safeParseDate($dateStr) {
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
      
      {{-- Chart --}}
      <div class="d-flex align-items-center justify-content-center text-3xl" style="height: 400px">
        <div class="card">
            <div class="card-body">
                <div id="chart-demo-pie" class="chart-lg"></div>
            </div>
        </div>
      </div>
      
      <div id="notification" class="notification-container"></div>
      
      <div class="mb-4 d-flex align-items-center justify-content-center">
        <div class="col-lg-10 pr-4 mr-2">
          
          {{-- Tabel Presensi Guru --}}
          <div class="border border-2 rounded p-4 my-4 d-flex flex-column text-md" style="height: 300px; max-height: 300px; position: relative;">
            <h5 class="position-relative" style="font-weight: bold; position: sticky; top: 0; background-color: white; z-index: 100;">
                Data Presensi Guru
            </h5>
            <div class="table-responsive small col-lg-12" style="flex: 1; overflow: auto;">
                <table id="absensiTable" class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th scope="col">Tanggal</th>
                            <th scope="col">Hari</th>
                            <th scope="col">Jam Absen</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($absensis as $absen)
                            @php
                                $tanggalRaw = $absen['tanggal'] ?? $absen['created_at'] ?? null;
                                $tanggalObj = safeParseDate($tanggalRaw);
                                $jamObj = safeParseDate($absen['created_at'] ?? $absen['tanggal'] ?? null);
                            @endphp
                            <tr>
                                <td>{{ $tanggalObj->format('d-m-Y') }}</td>
                                <td>{{ $tanggalObj->locale('id')->isoFormat('dddd') }}</td>
                                <td>{{ $jamObj->format('H:i:s') }}</td>
                                <td>{{ $absen['status'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
          </div>
          
          {{-- Form Presensi --}}
          <div class="border border-2 rounded p-4 my-4 d-flex flex-column text-md" style="height: auto; max-height: 300px; position: relative;" id="presensiOptions">
            <h5 class="font-weight-bold mb-3">Presensi Absensi Guru</h5>
            <form id="absensiForm" enctype="multipart/form-data">
                <div class="d-flex justify-content-center">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="statusOption" id="statusMasuk" onclick="selectOption('masuk')">
                        <label class="form-check-label" for="statusMasuk">Masuk</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="statusOption" id="statusSakit" onclick="selectOption('sakit')">
                        <label class="form-check-label" for="statusSakit">Sakit</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="statusOption" id="statusIzin" onclick="selectOption('izin')">
                        <label class="form-check-label" for="statusIzin">Izin</label>
                    </div>
                </div>
                
                <div class="file-upload-container" id="fileUploadContainer" style="display: none;">
                    <div class="mb-3">
                        <label for="fileInput" class="form-label">Unggah File (PDF):</label>
                        <input type="file" class="form-control" id="fileInput" name="file" accept="application/pdf">
                        <small class="text-muted">Maksimal 5MB</small>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end mt-3">
                    <button type="button" class="submit-button" onclick="submitAbsensiGuru()" id="submitButton">Submit</button>
                </div>
            </form>
          </div>
          
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.form-check-label { font-size: 20px; margin-right: 10px; }
.form-check { margin-right: 40px; }
.submit-button {
    border: none; border-radius: 5px;
    background-color: #007bff;
    color: #fff;
    padding: 10px 20px;
    cursor: pointer;
}
.notification-container {
    position: fixed; top: 50%; left: 50%;
    transform: translate(-50%, -50%); z-index: 1000;
}
.notification {
    background-color: #4CAF50;
    color: white;
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
}
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let masukCount = 0, sakitCount = 0, izinCount = 0, tidakMasukCount = 0;
        
        @foreach ($absensis as $absen)
            @php $status = strtolower($absen['status'] ?? ''); @endphp
            @if($status == 'masuk') masukCount++; 
            @elseif($status == 'sakit') sakitCount++;
            @elseif($status == 'izin') izinCount++;
            @else tidakMasukCount++;
            @endif
        @endforeach
        
        if (window.ApexCharts) {
            new ApexCharts(document.getElementById('chart-demo-pie'), {
                chart: {
                    type: "donut", fontFamily: 'inherit', height: 400,
                    sparkline: { enabled: true }, animations: { enabled: false },
                },
                series: [masukCount, sakitCount, izinCount, tidakMasukCount],
                labels: ["Masuk", "Sakit", "Izin", "Tidak Masuk"],
                colors: ['#2845ff', '#Feef50', '#20f000', '#ff1818'],
                legend: {
                    show: true, position: 'bottom', offsetY: 12,
                    markers: { width: 10, height: 10, radius: 100 },
                    itemMargin: { horizontal: 8, vertical: 8 },
                },
            }).render();
        }
        
        checkPresensiHariIni();
    });
    
    let selectedOption = null;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const userId = '{{ auth()->id() }}';
    
    function selectOption(option) {
        selectedOption = option;
        document.getElementById('fileUploadContainer').style.display = 
            (option === 'sakit' || option === 'izin') ? 'block' : 'none';
    }
    
    function submitAbsensiGuru() {
        if (!selectedOption) {
            alert('Pilih opsi absensi terlebih dahulu.');
            return;
        }
        
        const fileInput = document.getElementById('fileInput');
        if ((selectedOption === 'sakit' || selectedOption === 'izin') && fileInput.files.length === 0) {
            alert('Harap unggah file surat (PDF).');
            return;
        }
        
        const submitButton = document.getElementById('submitButton');
        submitButton.innerHTML = 'Mengirim...';
        submitButton.disabled = true;
        
        const formData = new FormData();
        formData.append('status_absen', selectedOption);
        formData.append('role', 'guru');
        formData.append('id_user', userId);
        
        if (fileInput.files.length > 0) {
            formData.append('file', fileInput.files[0]);
        }
        
        fetch('{{ route('absensi.store') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            submitButton.innerHTML = 'Submit';
            submitButton.disabled = false;
            
            if (data.message) {
                const notificationContainer = document.getElementById('notification');
                notificationContainer.innerHTML = `
                    <div class="notification">
                        <p>${data.message}</p>
                        <button onclick="location.reload()">Tutup</button>
                    </div>
                `;
                setTimeout(() => location.reload(), 2000);
            } else if (data.errors) {
                alert(Object.values(data.errors).flat().join('\n'));
            } else {
                alert('Terjadi kesalahan. Silakan coba lagi.');
            }
        })
        .catch(error => {
            submitButton.innerHTML = 'Submit';
            submitButton.disabled = false;
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengirim data absensi.');
        });
    }
    
    function checkPresensiHariIni() {
        const today = '{{ now()->format('Y-m-d') }}';
        let sudahAbsen = false;
        
        @foreach ($absensis as $absen)
            @php
                $tanggalRaw = $absen['tanggal'] ?? $absen['created_at'] ?? '';
                $tanggalCompare = '';
                if (is_numeric($tanggalRaw)) {
                    $timestamp = (strlen((string)$tanggalRaw) > 10) ? (int)($tanggalRaw / 1000) : (int)$tanggalRaw;
                    $tanggalCompare = date('Y-m-d', $timestamp);
                } elseif (!empty($tanggalRaw)) {
                    try {
                        $tanggalCompare = \Carbon\Carbon::parse($tanggalRaw)->format('Y-m-d');
                    } catch (\Exception $e) {}
                }
            @endphp
            @if(!empty($tanggalCompare) && $tanggalCompare === now()->format('Y-m-d'))
                sudahAbsen = true;
            @endif
        @endforeach
        
        if (sudahAbsen) {
            document.getElementById('presensiOptions').innerHTML = 
                '<p class="text-center">Anda telah melakukan presensi hari ini.</p>';
        }
    }
</script>
@endsection