<!DOCTYPE html>
<html>
<head>
    <title>Laporan Hasil Belajar</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
    <center>
        <h5 class="text-uppercase">Laporan Hasil Belajar</h5>
    </center>
    <br>
    <table style="width:100%">
        <tr>
            <td style="width:75%">
                @php
                    $siswaData = $siswa->siswa_data ?? [];
                    $nama = $siswaData['nama'] ?? $siswa->nama_lengkap ?? '';
                    $nisn = $siswaData['nisn'] ?? '-';
                    $kelasNama = $siswaData['kelas']['nama'] ?? '-';
                @endphp
                <h6 style="font-size:15px;">Nama : {{ $nama }}
                    <br>NISN : {{ $nisn }}
                </h6>
            </td>
            <td style="width:25%">
                <h6 style="font-size:15px;"> Kelas : {{ $kelasNama }}
                    <br>Semester : {{ $semester == 1 ? '1/Ganjil' : '2/Genap' }}</h6>
            </td>
        </tr>
    </table>

    <table class="table table-bordered" style="font-size: 13px">
        <thead class="table-primary">
            <tr>
                <th class="text-center">No.</th>
                <th class="text-center">Mata Pelajaran</th>
                <th class="text-center">Nilai</th>
                <th class="text-center">Predikat</th>
            </tr>
        </thead>
        <tbody>
            {{-- $raport adalah array nilai dari academic_records --}}
            @forelse ($raport as $nilai)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center">{{ $nilai['mapel'] ?? '-' }}</td>
                    <td class="text-center">{{ $nilai['nilai_akademik'] ?? 0 }}</td>
                    <td class="text-center">{{ $nilai['nilai_huruf'] ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center">Belum ada nilai</td></tr>
            @endforelse
        </tbody>
    </table>

    <table style="width:100%">
        <tr>
            <td style="width:34%">
                <p class="text-uppercase" style="font-size:13px; font-weight:bolder">Ketidakhadiran</p>
                <table class="table" style="font-size: 13px; border:1px solid black; margin-top:-10px">
                    <tr>
                        <td style="padding-left:15px">Sakit</td>
                        <td class="text-center">:</td>
                        <td class="text-center">{{ $raport_ket ?? 0 }}</td>
                    </tr>
                    <tr>
                        <td style="padding-left:15px">Ijin</td>
                        <td class="text-center">:</td>
                        <td class="text-center">{{ $raport_ket2 ?? 0 }}</td>
                    </tr>
                    <tr>
                        <td style="padding-left:15px">Tanpa Keterangan</td>
                        <td class="text-center">:</td>
                        <td class="text-center">{{ $raport_ket3 ?? 0 }}</td>
                    </tr>
                </table>
            </td>
            <td style="width:65%">
                <p class="text-uppercase" style="font-size:13px; font-weight:bolder">Catatan Wali Kelas</p>
                <table class="table" style="font-size: 13px; border:1px solid black; margin-top:-10px">
                    <tr><td style="padding:27px"></td></tr>
                </table>
            </td>
        </tr>
    </table>

    @if ($semester == 2)
        {{-- Tampilan keputusan naik/tidak naik --}}
        <table style="width:100%">
            <tr>
                <td style="width:45%">
                    <p>Tanggapan Orang tua/wali</p>
                    <table class="table" style="border:1px solid black; margin-top:-10px">
                        <tr><td style="padding:27px"></td></tr>
                    </table>
                </td>
                <td style="width:45%">
                    <p>Keputusan :</p>
                    <table class="table" style="border:1px solid black; margin-top:-10px">
                        <tr>
                            <td style="padding-left:15px">
                                Berdasarkan pencapaian seluruh kompetensi, peserta didik dinyatakan :
                                <br>
                                @if (($status['status'] ?? '') == 'naik')
                                    Naik / <span style="text-decoration: line-through;">Tidak Naik</span>
                                @else
                                    <span style="text-decoration: line-through;">Naik</span> / Tidak Naik
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    @else
        <table style="width:100%">
            <tr>
                <td style="width:100%">
                    <p>Tanggapan Orang tua/wali</p>
                    <table class="table" style="border:1px solid black; margin-top:-10px">
                        <tr><td style="padding:27px"></td></tr>
                    </table>
                </td>
            </tr>
        </table>
    @endif

    <br><br>
    <table style="width:100%; font-size:15px">
        <tr>
            <td style="width:270px">
                <p>Mengetahui</p>
                <p style="margin-top:-20px">Orang Tua/Wali,</p>
                <p style="margin-top: 60px">............................</p>
            </td>
            <td style="width:250px">
                <p>Mengetahui</p>
                <p style="margin-top:-20px">Kepala Sekolah</p>
                @if ($kepsek)
                    <p style="margin-top: 60px; font-weight:bolder; text-decoration: underline">
                        {{ $kepsek->profile['nama_lengkap'] ?? $kepsek->nama_lengkap }}
                    </p>
                    <p style="margin-top: -20px">NIP. {{ $kepsek->guru_data['nip'] ?? '-' }}</p>
                @endif
            </td>
            <td style="width:33%">
                <p>Jember, {{ $tanggal }}</p>
                <p style="margin-top:-20px">Wali Kelas, </p>
                @if ($walikelas)
                    <p style="margin-top: 60px; font-weight:bolder; text-decoration: underline">
                        {{ $walikelas->guru_data['nama'] ?? $walikelas->nama_lengkap }}
                    </p>
                    <p style="margin-top: -20px">NIP. {{ $walikelas->guru_data['nip'] ?? '-' }}</p>
                @endif
            </td>
        </tr>
    </table>
</body>
</html>