<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Fonts and icons -->
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <!-- CSS Files -->
    <link id="pagestyle" href="{{ asset('assets/css/material-dashboard.css?v=3.0.5') }}" rel="stylesheet" />
    <style>
        body { background-color: #f5f5f5; margin: 3%; }
        form { background-color: #ffffff; padding: 20px; border-radius: 10px; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); }
        label { display: block; margin-bottom: 5px; font-weight: 500; }
        input, textarea { width: 100%; padding: 8px; border: 1px solid #cccccc; border-radius: 5px; margin-bottom: 15px; }
        button:hover { background-color: #0056b3; }
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
                            <div class="mb-3">
                                <label for="nama_tamu">Nama Tamu</label>
                                <input id="nama_tamu" type="text" name="namaTamu" class="form-control rounded-3" 
                                       maxlength="20" value="{{ old('namaTamu') }}" required>
                                @error('namaTamu') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            {{-- Alamat --}}
                            <div class="mb-3">
                                <label for="input_alamat">Alamat / Asal Instansi</label>
                                <input id="input_alamat" type="text" name="alamatTamu" class="form-control" 
                                       value="{{ old('alamatTamu') }}" required>
                                @error('alamatTamu') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            {{-- Keterangan --}}
                            <div class="mb-3">
                                <label>Keterangan</label>
                                <textarea class="form-control" name="keteranganTamu" rows="4" 
                                          placeholder="Jelaskan tujuan kedatangan" required>{{ old('keteranganTamu') }}</textarea>
                                @error('keteranganTamu') <span class="text-danger">{{ $message }}</span> @enderror
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

<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>