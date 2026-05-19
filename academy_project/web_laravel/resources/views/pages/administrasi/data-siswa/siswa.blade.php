@extends('components.main')

@section('breadcrumbs')
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="/administrasi/siswa">Siswa</a></li>
        <li class="breadcrumb-item text-sm text-dark active" aria-current="page"></li>
    </ol>
    <h6 class="font-weight-bolder mb-0">Data Siswa</h6>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Data Siswa</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive pb-2 px-3">
                        <a href="/administrasi/siswa-tambah" class="btn btn-primary font-weight-bold text-xs">
                            <i class="material-icons opacity-10">add</i> Tambah
                        </a>
                        <a href="/administrasi/siswa-keluar" class="btn btn-danger font-weight-bold text-xs">Siswa Keluar</a>
                        <a href="/usersiswa/export" class="btn btn-success font-weight-bold text-xs">Export Data Siswa</a>

                        {{-- Filter --}}
                        <form action="/administrasi/siswa" method="get">
                            <div style="display: flex; column-gap: 10px; align-items: center;" class="my-3">
                                <select class="form-select form-select-sm" name="kelas" style="width: 200px">
                                    <option selected value="">-- Pilih Kelas --</option>
                                    @foreach ($kelas as $k)
                                        <option value="{{ (string)$k->_id }}" {{ request('kelas') == (string)$k->_id ? 'selected' : '' }}>
                                            {{ $k->nama_kelas }}
                                        </option>
                                    @endforeach
                                </select>
                                <select class="form-select form-select-sm" name="status" style="width: 200px">
                                    <option value="">-- Pilih Status --</option>
                                    <option value="bukan pindahan" {{ request('status') == 'bukan pindahan' ? 'selected' : '' }}>Bukan Pindahan</option>
                                    <option value="pindahan" {{ request('status') == 'pindahan' ? 'selected' : '' }}>Pindahan</option>
                                </select>
                                <button type="submit" class="btn btn-outline-primary btn-sm" style="margin-bottom: 0">Cari</button>
                            </div>
                        </form>

                        <table id="example" class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">NIS</th>
                                    <th class="text-center">Nama Lengkap</th>
                                    <th class="text-center">Kelas</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($siswas as $siswa)
                                    @php
                                        $siswaData = $siswa->siswa_data ?? [];
                                        $profileData = $siswa->profile ?? [];
                                        $nis = $siswaData['nis'] ?? '-';
                                        $nama = $siswaData['nama'] ?? $profileData['nama_lengkap'] ?? 'Tanpa Nama';
                                        $kelasNama = $siswaData['kelas']['nama'] ?? '-';
                                        $statusSiswa = $siswaData['status'] ?? '-';
                                        $foto = $siswaData['foto'] ?? $profileData['foto'] ?? 'default_img.png';
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td class="text-center">{{ $nis }}</td>
                                        <td class="text-center">{{ $nama }}</td>
                                        <td class="text-center">{{ $kelasNama }}</td>
                                        <td class="text-center">{{ $statusSiswa }}</td>
                                        <td class="text-center">
                                            <div style="display: flex; gap: 5px; justify-content: center;">
                                                <button type="button" data-bs-toggle="modal" data-bs-target="#detail-modal"
                                                    class="btn btn-info btn-sm rounded-circle"
                                                    onclick="showModalDetail(this)"
                                                    data-nama="{{ $nama }}"
                                                    data-nis="{{ $nis }}"
                                                    data-nisn="{{ $siswaData['nisn'] ?? '' }}"
                                                    data-jk="{{ $siswaData['jenis_kelamin'] ?? '' }}"
                                                    data-kelas="{{ $kelasNama }}"
                                                    data-nik="{{ $siswaData['nik'] ?? '' }}"
                                                    data-ttl="{{ ($siswaData['tempat_lahir'] ?? '') }}, {{ isset($siswaData['tanggal_lahir']) ? \Carbon\Carbon::parse($siswaData['tanggal_lahir'])->format('d/M/Y') : '' }}"
                                                    data-wali="{{ $siswaData['orang_tua']['nama_wali'] ?? '' }}"
                                                    data-telp="{{ $siswaData['no_telp'] ?? '' }}"
                                                    data-agama="{{ $siswaData['agama'] ?? '' }}"
                                                    data-alamat="{{ $siswaData['alamat'] ?? '' }}"
                                                    data-status="{{ $statusSiswa }}"
                                                    data-foto="{{ asset('storage/murid/img/' . $foto) }}">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                <a href="/administrasi/siswa-update/{{ (string)$siswa->_id }}"
                                                    class="btn btn-warning btn-sm rounded-circle">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <button type="button" data-bs-toggle="modal" data-bs-target="#leave-modal"
                                                    class="btn btn-danger btn-sm rounded-circle"
                                                    onclick="showModalLeave(this)"
                                                    data-id="{{ (string)$siswa->_id }}"
                                                    data-nama="{{ $nama }}"
                                                    data-nis="{{ $nis }}"
                                                    data-nisn="{{ $siswaData['nisn'] ?? '' }}"
                                                    data-kelas="{{ $kelasNama }}">
                                                    <i class="fa fa-sign-out-alt"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Tidak ada data siswa.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Detail, Modal Keluar, JavaScript --}}
    {{-- ... (sama seperti sebelumnya) ... --}}

    <script>
        // Inisialisasi DataTables setelah halaman selesai dimuat
        window.onload = function() {
            if (document.getElementById('example')) {
                if ($.fn.DataTable.isDataTable('#example')) {
                    $('#example').DataTable().destroy();
                }
                $('#example').DataTable({
                    searching: true,
                    ordering: true,
                    paging: true,
                    responsive: true,
                    language: {
                        search: "Cari:",
                        lengthMenu: "Tampilkan _MENU_ data",
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        emptyTable: "Tidak ada data siswa"
                    }
                });
            }
        };
    </script>
@endsection