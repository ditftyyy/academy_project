@extends('components.main')
@section('breadcrumbs')
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Dashboard</a></li>
        <li class="breadcrumb-item text-sm text-dark active" aria-current="page"></li>
    </ol>
    <h6 class="font-weight-bolder mb-0">Dashboard</h6>
@endsection

@section('additional-js-top')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endsection

@section('content')
    @include('components.dashboard.statistic')

    {{-- ========== SECTION AI CLUSTERING (Hanya untuk Guru & Admin) ========== --}}
    @if(auth()->user()->hasRole('guru') || auth()->user()->hasRole('admin'))
    <div class="card mt-4">
        <div class="card-header bg-gradient-primary text-white">
            <h5 class="mb-0">🧠 Analisis AI - Pengelompokan Siswa (K-Means)</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('ai.analyze') }}" method="POST" class="d-inline-block mb-3">
                @csrf
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-robot"></i> Jalankan Analisis AI (Cluster Semua Siswa)
                </button>
            </form>

            {{-- Hasil Clustering --}}
            @if(session('ai_result'))
                @php $result = session('ai_result'); @endphp
                <div class="alert alert-success mt-3">
                    <h5>📊 Hasil Clustering Siswa</h5>
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-bordered table-striped">
                            <thead class="sticky-top bg-white">
                                <tr>
                                    <th>Nama Siswa</th>
                                    <th>Cluster</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($result['students'] as $student)
                                <tr>
                                    <td>{{ $student['name'] }}</td>
                                    <td>{{ $student['cluster'] }}</td>
                                    <td>
                                        @if($student['cluster'] == 0)
                                            <span class="badge bg-success text-white">🏆 Berprestasi</span>
                                        @elseif($student['cluster'] == 1)
                                            <span class="badge bg-primary text-white">📘 Rata-rata</span>
                                        @else
                                            <span class="badge bg-danger text-white">⚠️ Butuh Bimbingan</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <h6 class="mt-3">📈 Ringkasan per Cluster</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-group">
                                @foreach($result['summary'] as $clusterName => $stats)
                                    <li class="list-group-item">
                                        <strong>{{ $clusterName }}</strong> ({{ $stats['jumlah'] }} siswa)<br>
                                        Rata-rata: Math {{ $stats['rata_rata_math'] }} | Reading {{ $stats['rata_rata_reading'] }} | Writing {{ $stats['rata_rata_writing'] }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <canvas id="clusterChart" width="400" height="300"></canvas>
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const labels = {!! json_encode(array_keys($result['summary'])) !!};
                        const counts = {!! json_encode(array_column($result['summary'], 'jumlah')) !!};
                        const ctx = document.getElementById('clusterChart').getContext('2d');
                        new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Jumlah Siswa',
                                    data: counts,
                                    backgroundColor: ['#28a745', '#007bff', '#dc3545'],
                                    borderColor: '#fff',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    y: { beginAtZero: true, stepSize: 1, title: { display: true, text: 'Jumlah Siswa' } }
                                }
                            }
                        });
                    });
                </script>
            @endif

            {{-- Form Prediksi Satu Siswa --}}
            <hr>
            <h6>🔮 Prediksi Cluster untuk Siswa Baru</h6>
            <form action="{{ route('ai.predict') }}" method="POST" class="row g-3">
                @csrf
                <div class="col-md-4">
                    <label>Nilai Matematika</label>
                    <input type="number" name="math_score" class="form-control" required step="1" min="0" max="100">
                </div>
                <div class="col-md-4">
                    <label>Nilai Reading</label>
                    <input type="number" name="reading_score" class="form-control" required step="1" min="0" max="100">
                </div>
                <div class="col-md-4">
                    <label>Nilai Writing</label>
                    <input type="number" name="writing_score" class="form-control" required step="1" min="0" max="100">
                </div>
                <div class="col-12 mt-2">
                    <button type="submit" class="btn btn-secondary">Prediksi Sekarang</button>
                </div>
            </form>

            @if(session('prediction_result'))
                <div class="alert alert-info mt-3">
                    <strong>Hasil Prediksi:</strong> Siswa ini masuk ke dalam <strong>{{ session('prediction_result')['cluster_name'] }}</strong> (Cluster {{ session('prediction_result')['cluster'] }}).
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger mt-3">{{ session('error') }}</div>
            @endif
        </div>
    </div>
    @endif
    {{-- ========== END AI CLUSTERING ========== --}}

    {{-- Data Guru / Siswa (existing) --}}
    @if (auth()->user()->hasRole('guru'))
        @php $guru = auth()->user(); $guruData = $guru->guru_data ?? []; @endphp
        @if (empty($guruData))
            <div class="row"><div class="col-12"><h4 class="text-center p-4">Anda tidak memiliki informasi pribadi</h4></div></div>
        @else
            <div class="row">
                <div class="col-12">
                    <div class="card z-index-2">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1">
                                <h6 class="text-white text-capitalize ps-3">Data Guru</h6>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <img src="{{ asset('storage/guru/img/' . ($guruData['foto'] ?? 'default_img.png')) }}" width="100%">
                                </div>
                                <div class="col-md-8">
                                    <ul class="list-group">
                                        <li class="list-group-item"><div class="row"><div class="col-md-5 fw-bold">NIP</div><div class="col-md-7">: {{ $guruData['nip'] ?? '-' }}</div></div></li>
                                        <li class="list-group-item"><div class="row"><div class="col-md-5 fw-bold">Nama</div><div class="col-md-7">: {{ $guruData['nama'] ?? $guru->nama_lengkap }}</div></div></li>
                                        <li class="list-group-item"><div class="row"><div class="col-md-5 fw-bold">Jenis Kelamin</div><div class="col-md-7">: {{ $guruData['jenis_kelamin'] ?? '-' }}</div></div></li>
                                        <li class="list-group-item"><div class="row"><div class="col-md-5 fw-bold">TTL</div><div class="col-md-7">: {{ $guruData['tempat_lahir'] ?? '' }}, {{ isset($guruData['tanggal_lahir']) ? \Carbon\Carbon::parse($guruData['tanggal_lahir'])->format('d-m-Y') : '' }}</div></div></li>
                                        <li class="list-group-item"><div class="row"><div class="col-md-5 fw-bold">No. Telepon</div><div class="col-md-7">: {{ $guruData['no_telp'] ?? '-' }}</div></div></li>
                                        <li class="list-group-item"><div class="row"><div class="col-md-5 fw-bold">Agama</div><div class="col-md-7">: {{ $guruData['agama'] ?? '-' }}</div></div></li>
                                        <li class="list-group-item"><div class="row"><div class="col-md-5 fw-bold">Alamat</div><div class="col-md-7">: {{ $guruData['alamat'] ?? '-' }}</div></div></li>
                                        <li class="list-group-item"><div class="row"><div class="col-md-5 fw-bold">Wali Kelas</div><div class="col-md-7">: {{ $guruData['kelas_wali']['nama'] ?? 'Bukan wali kelas' }}</div></div></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @elseif(auth()->user()->hasRole('siswa'))
        @php $siswa = auth()->user(); $siswaData = $siswa->siswa_data ?? []; @endphp
        @if (empty($siswaData))
            <div class="row"><div class="col-12"><h4 class="text-center p-4">Anda tidak memiliki informasi pribadi</h4></div></div>
        @else
            <div class="row">
                <div class="col-12">
                    <div class="card z-index-2">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1">
                                <h6 class="text-white text-capitalize ps-3">Data Siswa</h6>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <img src="{{ asset('storage/murid/img/' . ($siswaData['foto'] ?? 'default_img.png')) }}" width="100%">
                                </div>
                                <div class="col-md-8">
                                    <ul class="list-group">
                                        <li class="list-group-item"><div class="row"><div class="col-md-5 fw-bold">NISN</div><div class="col-md-7">: {{ $siswaData['nisn'] ?? '-' }}</div></div></li>
                                        <li class="list-group-item"><div class="row"><div class="col-md-5 fw-bold">Nama</div><div class="col-md-7">: {{ $siswaData['nama'] ?? $siswa->nama_lengkap }}</div></div></li>
                                        <li class="list-group-item"><div class="row"><div class="col-md-5 fw-bold">Kelas</div><div class="col-md-7">: {{ $siswaData['kelas']['nama'] ?? '-' }}</div></div></li>
                                        <li class="list-group-item"><div class="row"><div class="col-md-5 fw-bold">Jenis Kelamin</div><div class="col-md-7">: {{ $siswaData['jenis_kelamin'] ?? '-' }}</div></div></li>
                                        <li class="list-group-item"><div class="row"><div class="col-md-5 fw-bold">No. Telepon</div><div class="col-md-7">: {{ $siswaData['no_telp'] ?? '-' }}</div></div></li>
                                        <li class="list-group-item"><div class="row"><div class="col-md-5 fw-bold">Agama</div><div class="col-md-7">: {{ $siswaData['agama'] ?? '-' }}</div></div></li>
                                        <li class="list-group-item"><div class="row"><div class="col-md-5 fw-bold">Alamat</div><div class="col-md-7">: {{ $siswaData['alamat'] ?? '-' }}</div></div></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif

    {{-- Pengumuman --}}
    <div class="card mt-4">
        <div class="card-header"><h4>Pengumuman</h4></div>
        <div class="card-body">
            @forelse ($pengumumans as $pengumuman)
                <div class="alert alert-info">
                    <h5>{{ $pengumuman->title }}</h5>
                    <p>{{ $pengumuman->message }}</p>
                </div>
            @empty
                <p class="text-muted">Tidak ada pengumuman.</p>
            @endforelse
        </div>
    </div>

    {{-- Kalender --}}
    @if (auth()->user()->hasRole('guru') || auth()->user()->hasRole('siswa'))
        <div class="card mt-4">
            <div class="card-header"><h4>Kalender Akademik</h4></div>
            <div class="card-body"><div id="calendar"></div></div>
        </div>
        <script>
            $(document).ready(function() {
                $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
                var booking = @json($events);
                $('#calendar').fullCalendar({
                    header: { left: 'prev,next today', center: 'title', right: 'month,agendaWeek,agendaDay' },
                    events: booking,
                    selectable: true,
                    selectHelper: true,
                    editable: true,
                });
            });
        </script>
    @endif
@endsection