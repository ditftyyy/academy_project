
<?php $__env->startSection('breadcrumbs'); ?>
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Dashboard</a></li>
        <li class="breadcrumb-item text-sm text-dark active" aria-current="page"></li>
    </ol>
    <h6 class="font-weight-bolder mb-0">Dashboard</h6>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('additional-js-top'); ?>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('components.dashboard.statistic', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    
    <?php if(auth()->user()->hasRole('guru')): ?>
        <?php $guru = auth()->user(); $guruData = $guru->guru_data ?? []; ?>
        <?php if(empty($guruData)): ?>
            <div class="row"><div class="col-12"><h4 class="text-center p-4">Anda tidak memiliki informasi pribadi</h4></div></div>
        <?php else: ?>
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
                                    <img src="<?php echo e(asset('storage/guru/img/' . ($guruData['foto'] ?? 'default_img.png'))); ?>" width="100%">
                                </div>
                                <div class="col-md-8">
                                    <ul class="list-group">
                                        <li class="list-group-item"><div class="row"><div class="col-md-5 fw-bold">NIP</div><div class="col-md-7">: <?php echo e($guruData['nip'] ?? '-'); ?></div></div></li>
                                        <li class="list-group-item"><div class="row"><div class="col-md-5 fw-bold">Nama</div><div class="col-md-7">: <?php echo e($guruData['nama'] ?? $guru->nama_lengkap); ?></div></div></li>
                                        <li class="list-group-item"><div class="row"><div class="col-md-5 fw-bold">Jenis Kelamin</div><div class="col-md-7">: <?php echo e($guruData['jenis_kelamin'] ?? '-'); ?></div></div></li>
                                        <li class="list-group-item"><div class="row"><div class="col-md-5 fw-bold">TTL</div><div class="col-md-7">: <?php echo e($guruData['tempat_lahir'] ?? ''); ?>, <?php echo e(isset($guruData['tanggal_lahir']) ? \Carbon\Carbon::parse($guruData['tanggal_lahir'])->format('d-m-Y') : ''); ?></div></div></li>
                                        <li class="list-group-item"><div class="row"><div class="col-md-5 fw-bold">No. Telepon</div><div class="col-md-7">: <?php echo e($guruData['no_telp'] ?? '-'); ?></div></div></li>
                                        <li class="list-group-item"><div class="row"><div class="col-md-5 fw-bold">Agama</div><div class="col-md-7">: <?php echo e($guruData['agama'] ?? '-'); ?></div></div></li>
                                        <li class="list-group-item"><div class="row"><div class="col-md-5 fw-bold">Alamat</div><div class="col-md-7">: <?php echo e($guruData['alamat'] ?? '-'); ?></div></div></li>
                                        <li class="list-group-item"><div class="row"><div class="col-md-5 fw-bold">Wali Kelas</div><div class="col-md-7">: <?php echo e($guruData['kelas_wali']['nama'] ?? 'Bukan wali kelas'); ?></div></div></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php elseif(auth()->user()->hasRole('siswa')): ?>
        <?php $siswa = auth()->user(); $siswaData = $siswa->siswa_data ?? []; ?>
        <?php if(empty($siswaData)): ?>
            <div class="row"><div class="col-12"><h4 class="text-center p-4">Anda tidak memiliki informasi pribadi</h4></div></div>
        <?php else: ?>
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
                                    <img src="<?php echo e(asset('storage/murid/img/' . ($siswaData['foto'] ?? 'default_img.png'))); ?>" width="100%">
                                </div>
                                <div class="col-md-8">
                                    <ul class="list-group">
                                        <li class="list-group-item"><div class="row"><div class="col-md-5 fw-bold">NISN</div><div class="col-md-7">: <?php echo e($siswaData['nisn'] ?? '-'); ?></div></div></li>
                                        <li class="list-group-item"><div class="row"><div class="col-md-5 fw-bold">Nama</div><div class="col-md-7">: <?php echo e($siswaData['nama'] ?? $siswa->nama_lengkap); ?></div></div></li>
                                        <li class="list-group-item"><div class="row"><div class="col-md-5 fw-bold">Kelas</div><div class="col-md-7">: <?php echo e($siswaData['kelas']['nama'] ?? '-'); ?></div></div></li>
                                        <li class="list-group-item"><div class="row"><div class="col-md-5 fw-bold">Jenis Kelamin</div><div class="col-md-7">: <?php echo e($siswaData['jenis_kelamin'] ?? '-'); ?></div></div></li>
                                        <li class="list-group-item"><div class="row"><div class="col-md-5 fw-bold">No. Telepon</div><div class="col-md-7">: <?php echo e($siswaData['no_telp'] ?? '-'); ?></div></div></li>
                                        <li class="list-group-item"><div class="row"><div class="col-md-5 fw-bold">Agama</div><div class="col-md-7">: <?php echo e($siswaData['agama'] ?? '-'); ?></div></div></li>
                                        <li class="list-group-item"><div class="row"><div class="col-md-5 fw-bold">Alamat</div><div class="col-md-7">: <?php echo e($siswaData['alamat'] ?? '-'); ?></div></div></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    
    <div class="card mt-4">
        <div class="card-header"><h4>Pengumuman</h4></div>
        <div class="card-body">
            <?php $__empty_1 = true; $__currentLoopData = $pengumumans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pengumuman): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="alert alert-info">
                    <h5><?php echo e($pengumuman->title); ?></h5>
                    <p><?php echo e($pengumuman->message); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-muted">Tidak ada pengumuman.</p>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="card mt-4">
        <div class="card-header"><h4>Pengumuman Tamu</h4></div>
        <div class="card-body">
            <?php $hasActiveTamu = false; ?>
            <?php $__currentLoopData = $tamu_pesans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tamu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $data = $tamu->data_tambahan ?? []; ?>
                <?php if(($data['status'] ?? '') !== 'pesan_telah_selesai'): ?>
                    <?php $hasActiveTamu = true; ?>
                    <div class="row mb-3 border p-2">
                        <div class="col-md-8">
                            <strong>Nama:</strong> <?php echo e($data['nama_tamu'] ?? ''); ?><br>
                            <strong>Alamat:</strong> <?php echo e($data['alamat'] ?? ''); ?><br>
                            <strong>Keperluan:</strong> <?php echo e($tamu->message); ?>

                        </div>
                        <div class="col-md-4 text-end">
                            <form action="<?php echo e(route('dashboard.terimaPesan', $tamu->_id)); ?>" method="POST" class="d-inline">
                                <?php echo csrf_field(); ?>
                                <button class="btn btn-sm btn-success">Terima</button>
                            </form>
                            <form action="<?php echo e(route('dashboard.hapusPesan', $tamu->_id)); ?>" method="POST" class="d-inline">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php if(!$hasActiveTamu): ?> <p class="text-muted">Tidak ada tamu aktif.</p> <?php endif; ?>
        </div>
    </div>

    
    <?php if(auth()->user()->hasRole('guru') || auth()->user()->hasRole('siswa')): ?>
        <div class="card mt-4">
            <div class="card-header"><h4>Kalender Akademik</h4></div>
            <div class="card-body"><div id="calendar"></div></div>
        </div>
        <script>
            $(document).ready(function() {
                $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
                var booking = <?php echo json_encode($events, 15, 512) ?>;
                $('#calendar').fullCalendar({
                    header: { left: 'prev,next today', center: 'title', right: 'month,agendaWeek,agendaDay' },
                    events: booking,
                    selectable: true,
                    selectHelper: true,
                    editable: true,
                });
            });
        </script>
    <?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('components.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project Nanda\academy_project\academy_project\web_laravel\resources\views/pages/dashboard/dashboard.blade.php ENDPATH**/ ?>