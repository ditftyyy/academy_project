<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <!-- Fonts and icons -->
    <link rel="stylesheet" type="text/css"
        href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css" />
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <!-- CSS Files -->
    <link id="pagestyle" href="{{ asset('assets/css/material-dashboard.css?v=3.0.5') }}" rel="stylesheet" />
    <!-- Select2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    
    <style>
        body {
            background-color: #f5f5f5;
            margin: 3%;
        }
        form {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input, select, textarea {
            width: 100%;
            padding: 5px;
            border: 1px solid #cccccc;
            border-radius: 5px;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <main class="container">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <h6 class="text-white text-capitalize ps-3">Tambah Data Tamu</h6>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('kirim-tamu') }}" method="post">
                                @csrf
                                {{-- Nama Tamu --}}
                                <div class="mb-3 col-md-8" style="padding: 0 20px;">
                                    <label for="nama_tamu">Nama Tamu</label>
                                    <input id="nama_tamu" type="text" name="namaTamu" class="form-control rounded-3"
                                        maxlength="20" value="{{ old('namaTamu') }}"
                                        @if($errors->has('namaTamu')) autofocus @endif>
                                    @error('namaTamu')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Alamat --}}
                                <div class="mb-3 col-md-8" style="padding: 0 20px;">
                                    <label for="input_alamat">Alamat / Asal Instansi</label>
                                    <input id="input_alamat" type="text" name="alamatTamu" class="form-control"
                                        value="{{ old('alamatTamu') }}"
                                        @if($errors->has('alamatTamu')) autofocus @endif>
                                    @error('alamatTamu')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Tujuan --}}
                                <div class="mb-3" style="padding: 0 20px;">
                                    <label class="col-form-label">Bertujuan Bertemu Dengan Siapa</label>
                                    <div class="row g-3 py-1">
                                        <div class="col-md-4">
                                            <select onchange="handleTujuan(this)" id="opsi_tujuan" name="Opsi" class="form-select">
                                                <option value="" selected disabled>Pilih Tujuan</option>
                                                @foreach ($userRoles as $role)
                                                    <option value="{{ $role }}">{{ ucwords($role) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <select id="opsi_lanjutan" name="Opsi_Lanjutan" class="form-select">
                                                <option value="">Cari Nama</option>
                                            </select>
                                        </div>
                                    </div>
                                    <span class="form-text">
                                        Keterangan : Silahkan pilih Tujuan, kemudian cari nama yang ingin ditemui.
                                    </span>
                                </div>

                                {{-- Keterangan --}}
                                <div class="mb-3" style="padding: 0 20px;">
                                    <label>Keterangan</label>
                                    <textarea class="form-control" name="keteranganTamu" rows="4" placeholder="Jelaskan tujuan kedatangan">{{ old('keteranganTamu') }}</textarea>
                                    @error('keteranganTamu')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="card-footer d-flex justify-content-end" style="gap: 10px;">
                                    <a href="{{ route('login') }}" class="btn btn-danger text-sm rounded-3">
                                        <i class="fa fa-arrow-left"></i> Kembali
                                    </a>
                                    <button type="submit" class="btn btn-primary text-sm rounded-3"
                                        onclick="return confirm('Apakah anda yakin data sudah benar?')">
                                        <i class="fa fa-save"></i> Kirim
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.0/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            const opsi_lanjutan_dropdown = $('#opsi_lanjutan');
            const opsi_tujuan_dropdown = $('#opsi_tujuan');

            opsi_tujuan_dropdown.select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Pilih Tujuan',
            });
            opsi_lanjutan_dropdown.select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Cari nama',
            });

            const handleTujuan = async (role) => {
                try {
                    const res = await fetch(`/get-username-by-role/${role}`);
                    const result = await res.json();
                    // result = [{nama: '...', username: '...'}, ...]
                    let options = result.map(user => `<option value="${user.username}">${user.nama}</option>`);
                    opsi_lanjutan_dropdown.empty().append(options).trigger('change');
                } catch (error) {
                    console.error('Error:', error);
                }
            };

            opsi_tujuan_dropdown.on('change', function() {
                const selectedRole = $(this).val();
                if (selectedRole) handleTujuan(selectedRole);
            });

            opsi_lanjutan_dropdown.on('select2:opening', function(e) {
                if (!opsi_tujuan_dropdown.val()) {
                    e.preventDefault();
                    alert('Pilih terlebih dahulu tujuan untuk memfilter nama.');
                }
            });
        });
    </script>
</body>
</html>