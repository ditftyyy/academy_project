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
                        <a href="/administrasi/user/export" class="btn btn-success font-weight-bold text-xs">Export Data User</a>
                        <button type="button" class="btn btn-success font-weight-bold text-xs" data-bs-toggle="modal" data-bs-target="#importModal">Import Data User</button>
                        
                        <table id="example" class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">No</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Username</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Nama</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Roles</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    @php
                                        $profileData = $user->profile ?? [];
                                        $nama = $profileData['nama_lengkap'] ?? $user->nama_lengkap ?? '-';
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td class="text-center">{{ $user->username }}</td>
                                        <td class="text-center">{{ $nama }}</td>
                                        <td class="text-center">{{ $user->role }}</td>
                                        <td class="text-center">
                                            <button type="button" data-bs-toggle="modal" data-bs-target="#update-modal"
                                                class="btn btn-warning font-weight-bold text-sm rounded mx-1"
                                                data-bs-toggle="tooltip" title="Set Roles"
                                                onclick="showUpdateModal(this)"
                                                data-id="{{ (string)$user->_id }}"
                                                data-username="{{ $user->username }}"
                                                data-roles="{{ $user->role }}">
                                                Set Roles
                                            </button>
                                            <button type="button" data-bs-toggle="modal" data-bs-target="#reset-modal"
                                                class="btn btn-danger font-weight-bold text-sm rounded"
                                                data-bs-toggle="tooltip" title="Reset Password"
                                                onclick="showResetModal(this)"
                                                data-id="{{ (string)$user->_id }}"
                                                data-username="{{ $user->username }}"
                                                data-nama="{{ $nama }}">
                                                Reset Password
                                            </button>
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
    <div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white">Import Data User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('users.import') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="excel_file" class="form-label">Choose Excel File</label>
                            <input type="file" class="form-control" id="excel_file" name="excel_file" required>
                        </div>
                        <button type="submit" class="btn btn-success">Import Data</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Modal Update Roles --}}
    <div class="modal fade" id="update-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white">Update User Roles</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="update-roles-form" class="row g-3 px-4" method="post">
                        @method('PUT')
                        @csrf
                        <div class="g-2 align-items-center px-3">
                            <label class="col-form-label">Username: <b id="update-username-display"></b></label>
                        </div>
                        <div class="g-2 align-items-center px-3">
                            <label class="col-form-label">Roles:</label>
                            <div style="display: flex; flex-wrap: wrap; column-gap: 10px;row-gap: 5px" id="update-roles">
                                @foreach (config('app.DB_user_roles', ['admin', 'guru', 'siswa', 'tamu', 'pegawai']) as $item)
                                    <div style="display: flex; column-gap: 5px;">
                                        <input type="checkbox" name="roles[]" id="role-{{ $item }}" value="{{ $item }}">
                                        <label for="role-{{ $item }}" style="margin: 0">{{ ucfirst($item) }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Modal Reset Password --}}
    <div class="modal fade" id="reset-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white">Reset Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="reset-form" class="row g-3 px-4" method="post">
                        @method('PUT')
                        @csrf
                        <div class="px-3">
                            <h5>Apakah Anda yakin ingin mereset password</h5>
                            <h5 id="reset-nama" style="display: inline; font-weight: bold;"></h5>
                            <b>(<span id="reset-username" style="font-weight: bold;"></span>)</b>
                            <hr>
                            <h6><b>Note:</b> password akan sama dengan <b>username</b></h6>
                        </div>
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
        function showUpdateModal(el) {
            const form = document.getElementById('update-roles-form');
            const display = document.getElementById('update-username-display');
            const rolesContainer = document.getElementById('update-roles');
            
            form.action = '/administrasi/users/' + el.dataset.id;
            display.innerText = el.dataset.username;
            
            const selectedRoles = el.dataset.roles.split(',');
            rolesContainer.querySelectorAll("input[type='checkbox']").forEach(cb => {
                cb.checked = selectedRoles.includes(cb.value);
            });
        }
        
        function showResetModal(el) {
            const form = document.getElementById('reset-form');
            form.action = '/administrasi/users/reset/' + el.dataset.id;
            document.getElementById('reset-nama').innerText = el.dataset.nama;
            document.getElementById('reset-username').innerText = el.dataset.username;
            document.getElementById('reset-username-input').value = el.dataset.username;
        }
    </script>
@endsection