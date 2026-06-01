@extends('components.main')
@section('breadcrumbs')
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Dataset Students</li>
    </ol>
    <h6 class="font-weight-bolder mb-0">Dataset Students (Kaggle Performance)</h6>
@endsection

@section('additional-js-top')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>Data Siswa Asli dari Dataset</h5>
        <a href="{{ route('dataset.export.csv') }}" class="btn btn-sm btn-success">Export CSV</a>
    </div>
    <div class="card-body">
        {{-- Grafik distribusi cluster --}}
        @php
            $clusterCounts = [
                'Berprestasi' => \App\Models\Student::where('cluster', 0)->count(),
                'Rata-rata/Cukup' => \App\Models\Student::where('cluster', 1)->count(),
                'Butuh Bimbingan' => \App\Models\Student::where('cluster', 2)->count(),
            ];
        @endphp
        @if(array_sum($clusterCounts) > 0)
        <div class="row mb-4">
            <div class="col-md-6">
                <canvas id="clusterDistChart" style="max-height: 300px;"></canvas>
            </div>
            <div class="col-md-6">
                <ul class="list-group">
                    @foreach($clusterCounts as $name => $count)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ $name }}
                        <span class="badge bg-primary rounded-pill">{{ $count }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('clusterDistChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode(array_keys($clusterCounts)) !!},
                        datasets: [{
                            label: 'Jumlah Siswa',
                            data: {!! json_encode(array_values($clusterCounts)) !!},
                            backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
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

        <form method="GET" class="mb-3 row">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Cari nama, gender..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="per_page" class="form-select">
                    <option value="10" {{ request('per_page')==10?'selected':'' }}>10 per page</option>
                    <option value="25" {{ request('per_page')==25?'selected':'' }}>25 per page</option>
                    <option value="50" {{ request('per_page')==50?'selected':'' }}>50 per page</option>
                    <option value="100" {{ request('per_page')==100?'selected':'' }}>100 per page</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('dataset.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Nama</th>
                        <th>Gender</th>
                        <th>Race/Ethnicity</th>
                        <th>Math</th>
                        <th>Reading</th>
                        <th>Writing</th>
                        <th>Cluster AI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                    <tr>
                        <td>{{ $student->name }}</td>
                        <td>{{ $student->gender ?? '-' }}</td>
                        <td>{{ $student->race_ethnicity ?? '-' }}</td>
                        <td>{{ $student->math_score }}</td>
                        <td>{{ $student->reading_score }}</td>
                        <td>{{ $student->writing_score }}</td>
                        <td>
                            @if($student->cluster === 0)
                                <span class="badge bg-success">🏆 Berprestasi</span>
                            @elseif($student->cluster === 1)
                                <span class="badge bg-primary">📘 Rata-rata</span>
                            @elseif($student->cluster === 2)
                                <span class="badge bg-danger">⚠️ Butuh Bimbingan</span>
                            @else
                                <span class="badge bg-secondary">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                        <tr><td colspan="7" class="text-center">Tidak ada data siswa. Jalankan seeder terlebih dahulu.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $students->appends(request()->query())->links() }}
    </div>
</div>
@endsection