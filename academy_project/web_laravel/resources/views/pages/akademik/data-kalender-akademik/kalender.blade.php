@extends('components.main')
@section('breadcrumbs')
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="/akademik/kalender/index">Akademik</a></li>
        <li class="breadcrumb-item text-sm text-dark active" aria-current="page"></li>
    </ol>
    <h6 class="font-weight-bolder mb-0">Kalender Akademik</h6>
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
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Kalender Akademik</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive pb-2 px-3">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal --}}
    <div class="modal fade" id="bookingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kegiatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control" id="kalender-akademik-title" placeholder="Judul kegiatan" autofocus>
                    <span id="titleError" class="text-danger small"></span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" id="saveKalenderBtn" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            var events = @json($events);

            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay',
                },
                events: events,
                selectable: true,
                selectHelper: true,
                select: function(start, end) {
                    // Simpan tanggal ke localStorage
                    localStorage.setItem('kalender-start-date', moment(start).format('YYYY-MM-DD'));
                    localStorage.setItem('kalender-end-date', moment(end).subtract(1, 'day').format('YYYY-MM-DD'));
                    $('#bookingModal').modal('toggle');
                },
                editable: true,
                eventDrop: function(event) {
                    var id = event.id;
                    var start_date = moment(event.start).format('YYYY-MM-DD');
                    var end_date = moment(event.end).format('YYYY-MM-DD');
                    $.ajax({
                        url: "{{ route('calendar.update', '') }}/" + id,
                        type: "PATCH",
                        data: { start_date: start_date, end_date: end_date },
                        success: function() {
                            swal("Berhasil!", "Jadwal diperbarui", "success");
                        },
                        error: function() {
                            swal("Gagal!", "Terjadi kesalahan", "error");
                        }
                    });
                },
                eventClick: function(event) {
                    if (confirm('Hapus event "' + event.title + '" ?')) {
                        $.ajax({
                            url: "{{ route('calendar.destroy', '') }}/" + event.id,
                            type: "DELETE",
                            success: function() {
                                $('#calendar').fullCalendar('removeEvents', event.id);
                                swal("Terhapus!", "Event dihapus", "success");
                            },
                            error: function() {
                                swal("Gagal!", "Event tidak ditemukan", "error");
                            }
                        });
                    }
                },
                selectAllow: function(event) {
                    // Hanya allow seleksi 1 hari (end = start + 1 day di fullcalendar)
                    return moment(event.start).isSame(moment(event.end).subtract(1, 'second'), 'day');
                }
            });

            $('#saveKalenderBtn').click(function() {
                var title = $('#kalender-akademik-title').val().trim();
                if (title === '') {
                    $('#titleError').text('Judul tidak boleh kosong');
                    return;
                }
                $('#titleError').text('');

                var start_date = localStorage.getItem('kalender-start-date');
                var end_date = localStorage.getItem('kalender-end-date');

                $.ajax({
                    url: "{{ route('calendar.store') }}",
                    type: "POST",
                    data: { title: title, start_date: start_date, end_date: end_date },
                    success: function(response) {
                        $('#bookingModal').modal('hide');
                        $('#calendar').fullCalendar('renderEvent', {
                            id: response.id,
                            title: response.title,
                            start: response.start,
                            end: response.end,
                            color: response.color
                        });
                        $('#kalender-akademik-title').val('');
                        swal("Berhasil!", "Kegiatan ditambahkan", "success");
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            $('#titleError').text(xhr.responseJSON.errors.title);
                        } else {
                            swal("Gagal!", "Terjadi kesalahan server", "error");
                        }
                    }
                });
            });
        });
    </script>
@endsection