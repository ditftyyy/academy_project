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
@endsection
@section('content')
    @include('components.dashboard.statistic')
    
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

    {{-- Tamu --}}
    <div class="card mt-4">
        <div class="card-header"><h4>Pengumuman Tamu</h4></div>
        <div class="card-body">
            @php $hasActiveTamu = false; @endphp
            @foreach ($tamu_pesans as $tamu)
                @php $data = $tamu->data_tambahan ?? []; @endphp
                @if(($data['status'] ?? '') !== 'pesan_telah_selesai')
                    @php $hasActiveTamu = true; @endphp
                    <div class="row mb-3 border p-2">
                        <div class="col-md-8">
                            <strong>Nama:</strong> {{ $data['nama_tamu'] ?? '' }}<br>
                            <strong>Alamat:</strong> {{ $data['alamat'] ?? '' }}<br>
                            <strong>Keperluan:</strong> {{ $tamu->message }}
                        </div>
                        <div class="col-md-4 text-end">
                            <form action="{{ route('dashboard.terimaPesan', $tamu->_id) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-success">Terima</button>
                            </form>
                            <form action="{{ route('dashboard.hapusPesan', $tamu->_id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </div>
                    </div>
                @endif
            @endforeach
            @if(!$hasActiveTamu) <p class="text-muted">Tidak ada tamu aktif.</p> @endif
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