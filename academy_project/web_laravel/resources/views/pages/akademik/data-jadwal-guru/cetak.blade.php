<!DOCTYPE html>
<html>
<head>
    <title>Jadwal Mengajar {{ $guru->guru_data['nama'] ?? $guru->nama_lengkap }}</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
    <style> table tr td, table tr th { font-size: 9pt; } </style>
    <center>
        <h5 class="text-uppercase">Jadwal Mengajar Guru</h5>
        <h6 class="text-uppercase">Tahun Pelajaran {{ date('Y') }} / {{ date('Y') + 1 }}</h6>
    </center>
    <br>
    <pre><h6>NIP  : {{ $guru->guru_data['nip'] ?? '-' }}
Nama : {{ $guru->guru_data['nama'] ?? $guru->nama_lengkap }}</h6></pre>
    <table class='table table-bordered'>
        <thead>
            <tr>
                <th class="text-center" style="background-color:rgb(88,243,134)">HARI</th>
                <th class="text-center" style="background-color:rgb(88,243,134)">JAM KE-</th>
                <th class="text-center" style="background-color:rgb(88,243,134)">WAKTU</th>
                <th class="text-center" style="background-color:rgb(88,243,134)">KELAS</th>
                <th class="text-center" style="background-color:rgb(88,243,134)">RUANG</th>
                <th class="text-center" style="background-color:rgb(88,243,134)">KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach($jadwal as $day)
                @php $mps = $day['mata_pelajaran'] ?? []; $hari = $day['hari'] ?? ''; @endphp
                @if(count($mps) > 0)
                    @foreach($mps as $index => $mp)
                        <tr>
                            @if($index == 0)
                                <td class="text-center text-uppercase" rowspan="{{ count($mps) }}" style="border-color:rgb(27,26,26);"><b>{{ $hari }}</b></td>
                            @endif
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center">{{ $mp['jam_mulai'] }} - {{ $mp['jam_selesai'] }}</td>
                            <td class="text-center">{{ $mp['kelas'] ?? '-' }}</td>
                            <td class="text-center">{{ $mp['ruang'] ?? '-' }}</td>
                            <td class="text-center">{{ $mp['keterangan'] ?? '' }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td class="text-center text-uppercase"><b>{{ $hari }}</b></td>
                        <td colspan="5" class="text-center">Libur / Tidak ada jadwal</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</body>
</html>