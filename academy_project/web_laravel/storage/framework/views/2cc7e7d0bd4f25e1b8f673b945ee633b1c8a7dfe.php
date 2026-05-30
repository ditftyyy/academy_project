


<?php if(auth()->user()->hasRole('admin', 'kepsek')): ?>
    <div class="row">
        
        
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">people</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize"><b>DATA TEKNISI</b></p>
                        <h4 class="mb-0">0</h4>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">groups</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize"><b>DATA GURU</b></p>
                        <h4 class="mb-0"><?php echo e($total_guru ?? 0); ?></h4>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">groups</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize"><b>DATA KELAS</b></p>
                        <h4 class="mb-0"><?php echo e($total_kelas ?? 0); ?></h4>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-warning shadow-info text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-icons opacity-10">group</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize"><b>DATA SISWA</b></p>
                        <h4 class="mb-0"><?php echo e($total_siswa ?? 0); ?></h4>
                    </div>
                </div>
            </div>
        </div>

        
        
        
        <div class="card mt-4">
            <div class="card-header">
                <h4 class="card-title">Pengumuman</h4>
            </div>
            <div class="card-body">
                <?php if($pengumumans->isEmpty()): ?>
                    <p class="text-muted">Tidak ada pengumuman saat ini.</p>
                <?php else: ?>
                    <ul class="list-group">
                        <?php $__currentLoopData = $pengumumans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pengumuman): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="col-md-4">
                                    <h5><?php echo e($pengumuman->title); ?></h5>
                                </div>
                                <div class="col-md-4">
                                    <p><?php echo e($pengumuman->message); ?></p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <p class="text-muted"><?php echo e($pengumuman->role); ?></p>
                                    <button class="btn btn-sm btn-warning edit-notification" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editPengumuman<?php echo e((string)$pengumuman->_id); ?>">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <a href="/dashboard/hapus-pengumuman/<?php echo e((string)$pengumuman->_id); ?>" 
                                       onclick="return confirm('Anda yakin akan menghapus pengumuman ini?')" 
                                       class="btn btn-sm btn-danger" 
                                       data-bs-toggle="tooltip" 
                                       title="Hapus">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </div>
                            </li>

                            
                            <div class="modal fade" 
                                 id="editPengumuman<?php echo e((string)$pengumuman->_id); ?>" 
                                 tabindex="-1" 
                                 role="dialog" 
                                 aria-labelledby="editPengumumanLabel" 
                                 aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editPengumumanLabel">Edit Pengumuman</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        
                                        <form method="post" action="<?php echo e(route('update-pengumuman', ['id' => (string)$pengumuman->_id])); ?>">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('PUT'); ?>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="notificationTitle" class="form-label">Judul Pengumuman</label>
                                                    <input type="text" 
                                                           class="form-control" 
                                                           id="notificationTitle" 
                                                           name="title" 
                                                           value="<?php echo e($pengumuman->title); ?>" 
                                                           required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="notificationMessage" class="form-label">Isi Pengumuman</label>
                                                    <textarea class="form-control" 
                                                              id="notificationMessage" 
                                                              name="message" 
                                                              rows="4" 
                                                              required><?php echo e($pengumuman->message); ?></textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="notificationRoles" class="form-label">Select Roles</label>
                                                    <?php
                                                        $availableRoles = config('app.DB_user_roles', ['admin', 'guru', 'siswa']);
                                                    ?>
                                                    <?php $__currentLoopData = $availableRoles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $roleOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <div class="form-check">
                                                            <input class="form-check-input" 
                                                                   type="checkbox" 
                                                                   name="roles[]" 
                                                                   value="<?php echo e($roleOption); ?>" 
                                                                   id="role<?php echo e($roleOption); ?>_<?php echo e((string)$pengumuman->_id); ?>"
                                                                   <?php if($roleOption === $pengumuman->role): ?> checked <?php endif; ?>>
                                                            <label class="form-check-label" for="role<?php echo e($roleOption); ?>_<?php echo e((string)$pengumuman->_id); ?>">
                                                                <?php echo e(ucfirst($roleOption)); ?>

                                                            </label>
                                                        </div>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary">Simpan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php endif; ?>

                
                <div class="card-footer" style="padding: 0.5rem;">
                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-xl-0 mb-4">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#buatPengumuman">
                            Buat Pengumuman
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    
    
    
    <div class="modal fade" id="buatPengumuman" tabindex="-1" role="dialog" aria-labelledby="buatPengumumanLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="buatPengumumanLabel">Buat Pengumuman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="post" action="<?php echo e(route('buat-pengumuman')); ?>">
                        <?php echo csrf_field(); ?>

                        <div class="mb-3">
                            <label for="notificationTitle" class="form-label">Judul Pengumuman</label>
                            <input type="text" class="form-control" id="notificationTitle" name="title" required>
                        </div>

                        <div class="mb-3">
                            <label for="notificationMessage" class="form-label">Isi Pengumuman</label>
                            <textarea class="form-control" id="notificationMessage" name="message" rows="4" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="notificationRoles" class="form-label">Pilih Role</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="roles[]" value="guru" id="roleGuru">
                                <label class="form-check-label" for="roleGuru">Guru</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="roles[]" value="siswa" id="roleStudent">
                                <label class="form-check-label" for="roleStudent">Siswa</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="roles[]" value="semua" id="roleSemua">
                                <label class="form-check-label" for="roleSemua">Semua</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Buat</button>
                    </form>
                </div>
            </div>
        </div>
    </div>




<?php elseif(auth()->user()->hasRole('wakasek')): ?>
    <div class="row gap-1 justify-content-evenly">
        <div class="card col-4" style="height: 650px; width: 450px; border-radius: 25px;">
            <div class="card-body d-flex flex-column justify-content-between">
                <div class="text-center mb-3">
                    <img class="rounded-4" src="<?php echo e(asset('assets/img/c.jpg')); ?>" style="width: 100%; border-radius: 25px;" alt="Deskripsi Gambar">
                </div>
                <h5 class="mt-3 text-capitalize text-center" style="color: black; font-size: 30px; font-weight: 700; word-wrap: break-word;">DATA RUANG</h5>
                <div class="text-end">
                    <a href="<?php echo e(route('ruang_main')); ?>" class="btn btn-primary">Daftar Ruang</a>
                </div>
            </div>
        </div>
        <div class="card col-4" style="height: 650px; width: 450px; border-radius: 25px;">
            <div class="card-body d-flex flex-column justify-content-between">
                <div class="text-center mb-3">
                    <img class="rounded-4" src="<?php echo e(asset('assets/img/a.jpg')); ?>" style="width: 100%; border-radius: 25px;" alt="Deskripsi Gambar">
                </div>
                <h5 class="mt-3 text-capitalize text-center" style="color: black; font-size: 30px; font-weight: 700; word-wrap: break-word;">DATA BARANG</h5>
                <div class="text-end">
                    <a href="<?php echo e(route('barang_main')); ?>" class="btn btn-primary">Daftar Barang</a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?><?php /**PATH D:\Project Nanda\academy_project\academy_project\web_laravel\resources\views/components/dashboard/statistic.blade.php ENDPATH**/ ?>