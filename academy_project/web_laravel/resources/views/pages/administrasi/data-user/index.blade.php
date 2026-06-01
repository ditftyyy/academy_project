@extends('components.main')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                    <h6 class="text-white text-capitalize ps-3">Data User</h6>
                </div>
            </div>
            <div class="card-body px-0 pb-2">
                <div class="table-responsive pb-2 px-3">
                    <a href="/administrasi/user/export" class="btn btn-success btn-sm">Export Data User</a>
                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">Import Data User</button>

                    <table id="example" class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Username</th>
                                <th class="text-center">Nama</th>
                                <th class="text-center">Roles</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                @php
                                    $profileData = $user->profile ?? [];
                                    $nama = $profileData['nama_lengkap'] ?? $user->nama_lengkap ?? '-';
                                    // Format role untuk tampilan
                                    $roleDisplay = $user->role;
                                    if (is_string($roleDisplay)) {
                                        $roleDisplay = implode(', ', explode(',', $roleDisplay));
                                    } elseif (is_array($roleDisplay)) {
                                        $roleDisplay = implode(', ', $roleDisplay);
                                    }
                                    // Cek apakah user adalah root (tidak boleh dihapus)
                                    $isRoot = in_array('root', explode(',', $user->role));
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="text-center">{{ $user->username }}</td>
                                    <td class="text-center">{{ $nama }}</td>
                                    <td class="text-center">{{ $roleDisplay }}</td>
                                    <td class="text-center">
                                        {{-- Tombol Reset Password --}}
                                        <button type="button" data-bs-toggle="modal" data-bs-target="#reset-modal"
                                            class="btn btn-danger btn-sm rounded-circle"
                                            onclick="showResetModal(this)"
                                            data-id="{{ $user->_id }}"
                                            data-username="{{ $user->username }}"
                                            data-nama="{{ $nama }}">
                                            <i class="fa fa-key"></i>
                                        </button>

                                        {{-- Tombol Hapus (tidak ditampilkan untuk root) --}}
                                        @if(!$isRoot)
                                            <form action="/administrasi/users/delete/{{ $user->_id }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Yakin ingin menghapus user {{ $user->username }}? Data yang dihapus tidak dapat dikembalikan.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-warning btn-sm rounded-circle">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Import --}}
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title text-white">Import Data User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('users.import') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="excel_file" class="form-label">Pilih File Excel</label>
                        <input type="file" class="form-control" id="excel_file" name="excel_file" required>
                    </div>
                    <button type="submit" class="btn btn-success">Import</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal Reset Password --}}
<div class="modal fade" id="reset-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white">Reset Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="reset-form" method="post">
                    @method('PUT')
                    @csrf
                    <h5>Apakah Anda yakin ingin mereset password</h5>
                    <h5 id="reset-nama" style="display: inline; font-weight: bold;"></h5>
                    <b>(<span id="reset-username" style="font-weight: bold;"></span>)</b>
                    <hr>
                    <h6><b>Note:</b> password baru akan sama dengan <b>username</b></h6>
                    <input type="hidden" name="username" id="reset-username-input">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-secondary">Reset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function showResetModal(el) {
        const form = document.getElementById('reset-form');
        form.action = '/administrasi/users/reset/' + el.dataset.id;
        document.getElementById('reset-nama').innerText = el.dataset.nama;
        document.getElementById('reset-username').innerText = el.dataset.username;
        document.getElementById('reset-username-input').value = el.dataset.username;
    }

    $(document).ready(function() {
        $('#example').DataTable({
            language: { 
                search: "Cari:", 
                lengthMenu: "Tampilkan _MENU_ data", 
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                emptyTable: "Tidak ada data user"
            }
        });
    });
</script>
@endsection