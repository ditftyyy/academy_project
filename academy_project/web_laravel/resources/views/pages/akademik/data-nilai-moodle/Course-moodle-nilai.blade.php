@extends('components.main')

@section('breadcrumbs')
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="/data-nilai-moodle/course-moodle">Course</a></li>
        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Detail Nilai</li>
    </ol>
    <h6 class="font-weight-bolder mb-0">Detail Nilai Course</h6>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <a href="/data-nilai-moodle/course-moodle" class="btn btn-secondary rounded-pill font-weight-bold text-xs text-white mb-3">
                <i class="material-icons opacity-10">arrow_back</i> Kembali
            </a>
        </div>
    </div>

    @if(!$moodleConnected)
        <div class="alert alert-warning">
            <i class="fa fa-exclamation-triangle"></i>
            {{ $errorMessage ?? 'Tidak dapat mengambil data dari Moodle.' }}
            <br>
            <a href="{{ url()->current() }}" class="btn btn-sm btn-outline-warning mt-2">
                <i class="fa fa-refresh"></i> Coba Lagi
            </a>
        </div>
    @else
        {{-- Search form --}}
        <div class="row mt-3">
            <div class="col-12">
                <form action="{{ route('get.grade.items', ['courseId' => $courseId]) }}" method="GET">
                    <div class="form-group">
                        <label for="search">Cari berdasarkan Nama:</label>
                        <input type="text" class="form-control" id="search" name="search" placeholder="Ketik nama..." value="{{ request('search') }}">
                    </div>
                </form>
            </div>
        </div>

        {{-- Store usergrades data --}}
        @php
            $userGradesData = $gradeItems['usergrades'] ?? [];
        @endphp

        @forelse ($userGradesData as $grade)
            @if (strpos(strtolower($grade['userfullname']), 'pengajar') === false)
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card mb-1" style="background-color: #ffffff; color: #333; border-radius: 14px;">
                            <div class="card-body">
                                <h4 class="card-title" style="font-size: 20px;">{{ $grade['userfullname'] }}</h4>
                                @foreach ($grade['gradeitems'] as $item)
                                    @if (in_array($item['itemmodule'], ['quiz', 'assign']))
                                        <div class="card mb-2" style="background-color: #f8f9fa; border-radius: 6px;">
                                            <div class="card-body">
                                                <h5 class="card-title" style="font-size: 14px; margin-bottom: 0;">{{ $item['itemname'] }}</h5>
                                                <ul class="list-group list-group-flush">
                                                    <li class="list-group-item" style="font-size: 14px; padding: 1px;">
                                                        Nilai: {{ $item['graderaw'] }}
                                                    </li>
                                                    @if (array_key_exists('items', $item))
                                                        @foreach ($item['items'] as $column)
                                                            <li class="list-group-item" style="font-size: 14px; padding: 1px;">
                                                                {{ $column['column1'] }}: {{ $column['column2'] }}
                                                            </li>
                                                        @endforeach
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @empty
            <div class="alert alert-info">Tidak ada data nilai untuk course ini.</div>
        @endforelse
    @endif

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var searchInput = document.getElementById('search');
            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    clearTimeout(this.timer);
                    this.timer = setTimeout(function () {
                        searchInput.closest('form').submit();
                    }, 500);
                });
            }
        });
    </script>
@endsection