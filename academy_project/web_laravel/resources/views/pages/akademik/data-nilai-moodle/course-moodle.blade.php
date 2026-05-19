@extends('components.main')
@section('breadcrumbs')
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="/data-nilai-moodle/course-moodle">Moodle</a></li>
        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Data Course</li>
    </ol>
    <h6 class="font-weight-bolder mb-0">Data Course Moodle</h6>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Daftar Course Moodle</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    @if(!$moodleConnected)
                        <div class="alert alert-warning m-3">
                            <i class="fa fa-exclamation-triangle"></i>
                            {{ $errorMessage ?? 'Tidak dapat terhubung ke Moodle. Silakan coba beberapa saat lagi.' }}
                            <br>
                            <a href="{{ url()->current() }}" class="btn btn-sm btn-outline-warning mt-2">
                                <i class="fa fa-refresh"></i> Coba Lagi
                            </a>
                        </div>
                    @else
                        <div class="table-responsive pb-2 px-3">
                            <table id="example" class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">No</th>
                                        <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Nama Course</th>
                                        <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($courses as $course)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td class="text-center">{{ $course['fullname'] }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('nilai-course', ['courseId' => $course['id']]) }}"
                                                   class="btn btn-info font-weight-bold btn--edit text-sm rounded">
                                                    Detail Nilai
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">Tidak ada course tersedia.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection