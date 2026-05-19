<!DOCTYPE html>
<html>
<head>
    <title>Laporan Hasil Belajar</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
    <center><h5 class="text-uppercase">Laporan Hasil Belajar</h5></center>
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
                <h6>Nama : {{ $nama }}<br>NISN : {{ $nisn }}</h6>
            </td>
            <td style="width:25%">
                <h6>Kelas : {{ $kelasNama }}<br>Semester : {{ $semester == 1 ? '1/Ganjil' : '2/Genap' }}</h6>
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
                <p style="font-weight:bolder">Ketidakhadiran</p>
                <table class="table" style="border:1px solid black; margin-top:-10px">
                    <tr><td>Sakit</td><td>:</td><td>{{ $raport_ket ?? 0 }}</td></tr>
                    <tr><td>Ijin</td><td>:</td><td>{{ $raport_ket2 ?? 0 }}</td></tr>
                    <tr><td>Tanpa Keterangan</td><td>:</td><td>{{ $raport_ket3 ?? 0 }}</td></tr>
                </table>
            </td>
            <td style="width:65%">
                <p style="font-weight:bolder">Catatan Wali Kelas</p>
                <table class="table" style="border:1px solid black; margin-top:-10px">
                    <tr><td style="padding:27px"></td></tr>
                </table>
            </td>
        </tr>
    </table>

    @if ($semester == 2)
        <table style="width:100%">
            <tr>
                <td style="width:45%">
                    <p>Tanggapan Orang tua/wali</p>
                    <table class="table" style="border:1px solid black; margin-top:-10px"><tr><td style="padding:27px"></td></tr></table>
                </td>
                <td style="width:45%">
                    <p>Keputusan :</p>
                    <table class="table" style="border:1px solid black; margin-top:-10px">
                        <tr><td>@if (($status['status'] ?? '') == 'naik') Naik / <span style="text-decoration: line-through;">Tidak Naik</span> @else <span style="text-decoration: line-through;">Naik</span> / Tidak Naik @endif</td></tr>
                    </table>
                </td>
            </tr>
        </table>
    @endif

    <br><br>
    <table style="width:100%; font-size:15px">
        <tr>
            <td style="width:270px"><p>Orang Tua/Wali,</p><p style="margin-top: 60px">............................</p></td>
            <td style="width:250px">
                <p>Kepala Sekolah</p>
                @if ($kepsek)
                    <p style="font-weight:bolder; text-decoration: underline">{{ $kepsek->profile['nama_lengkap'] ?? $kepsek->nama_lengkap }}</p>
                    <p>NIP. {{ $kepsek->guru_data['nip'] ?? '-' }}</p>
                @endif
            </td>
            <td style="width:33%">
                <p>Jember, {{ $tanggal }}</p>
                <p>Wali Kelas, </p>
                <p style="font-weight:bolder; text-decoration: underline">{{ $walikelasnama ?? '-' }}</p>
                <p>NIP. {{ $walikelasnip ?? '-' }}</p>
            </td>
        </tr>
    </table>
</body>
</html>