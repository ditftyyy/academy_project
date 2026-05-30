<nav class="navbar navbar-main navbar-expand-sm shadow-none" id="navbarBlur" data-scroll="true">
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <?php echo $__env->yieldContent('breadcrumbs'); ?>
        </nav>
        <div class="collapse navbar-collapse" id="navbar" style="overflow: visible !important;">
            <div class="dropdown show"
                style="justify-self: flex-end; max-width: fit-content; margin: 0; margin-left: auto">
                <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                    id="profile-dropdown" aria-haspopup="true" aria-expanded="false">
                    <strong>
                        <i class="fa fa-user me-sm-1"></i>
                        <span class="d-sm-inline d-none">
                            
                            
                            
                            <?php if(auth()->user()->hasRole('admin')): ?>
                                <?php echo e(auth()->user()->profile['nama_lengkap'] ?? auth()->user()->username); ?>

                            <?php elseif(auth()->user()->hasRole('guru')): ?>
                                <?php echo e(auth()->user()->guru_data['nama'] ?? auth()->user()->profile['nama_lengkap'] ?? auth()->user()->username); ?>

                            <?php elseif(auth()->user()->hasRole('siswa')): ?>
                                <?php echo e(auth()->user()->siswa_data['nama'] ?? auth()->user()->profile['nama_lengkap'] ?? auth()->user()->username); ?>

                            <?php elseif(auth()->user()->hasRole('kepsek')): ?>
                                <?php echo e(auth()->user()->guru_data['nama'] ?? auth()->user()->profile['nama_lengkap'] ?? auth()->user()->username); ?>

                            <?php else: ?>
                                <?php echo e(auth()->user()->profile['nama_lengkap'] ?? auth()->user()->username); ?>

                            <?php endif; ?>
                        </span>
                    </strong>
                </button>
                <ul class="dropdown-menu" aria-labelledby="profile-dropdown">
                    <li><a class="dropdown-item" href="/option/change-password">Edit password</a></li>
                    <?php if(count(array_diff(explode(',', auth()->user()->role), ['root'])) > 1): ?>
                        <li>
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#update-navbar-role-modal"
                                class="dropdown-item" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Detail"
                                onclick="navbarSetCheckedRole()">Ganti Role</a>
                        </li>
                    <?php endif; ?>
                    <li>
                        <a class="dropdown-item" href="/logout"
                            onclick="return confirm('Apakah anda yakin akan keluar?')">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    
    <?php if(count(array_diff(explode(',', auth()->user()->role), ['root'])) > 1): ?>
        <div class="modal fade" id="update-navbar-role-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title text-white" id="exampleModalLabel">Ganti Role</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="<?php echo e(route('set_role')); ?>" class="row g-3 px-4" method="post" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <div style="display: flex;flex-wrap: wrap; column-gap: 10px;row-gap: 5px;">
                                <?php $__currentLoopData = explode(',', auth()->user()->role); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value_role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($value_role != 'root'): ?>
                                        <div style="display: flex; column-gap: 5px;">
                                            <input type="radio" name="role" value="<?php echo e($value_role); ?>"
                                                id="navbar-role-<?php echo e($value_role); ?>"
                                                current-role="<?php echo e(auth()->user()->current_role); ?>">
                                            <label style="margin: 0" for="navbar-role-<?php echo e($value_role); ?>">
                                                <?php echo e(ucfirst($value_role)); ?>

                                            </label>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <script>
            function navbarSetCheckedRole() {
                const input = document.querySelectorAll("#update-navbar-role-modal input[type='radio']");
                input.forEach(function(radio) {
                    if (radio.getAttribute('current-role') == radio.getAttribute('value')) {
                        radio.checked = true;
                        return;
                    }
                });
            }
        </script>
    <?php endif; ?>
</nav><?php /**PATH D:\Project Nanda\academy_project\academy_project\web_laravel\resources\views/components/navbar.blade.php ENDPATH**/ ?>