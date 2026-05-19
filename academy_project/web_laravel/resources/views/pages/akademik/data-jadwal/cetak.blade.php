<!DOCTYPE html>
<html>
<head>
    <title>Jadwal Pelajaran Kelas {{ $kelas->nama_kelas ?? '' }}</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
    <style> table tr td, table tr th { font-size: 9pt; } </style>
    <center>
        <h5 class="text-uppercase">Jadwal Pelajaran Kelas {{ $kelas->nama_kelas ?? '' }}</h5>
        <h6 class="text-uppercase">Tahun Pelajaran {{ date('Y') }} / {{ date('Y') + 1 }}</h6>
    </center>
    <br>
    <table class='table table-bordered'>
        <thead>
            <tr>
                <th class="text-center" style="background-color:rgb(88,155,243)">HARI</th>
                <th class="text-center" style="background-color:rgb(88,155,243)">JAM KE-</th>
                <th class="text-center" style="background-color:rgb(88,155,243)">WAKTU</th>
                <th class="text-center" style="background-color:rgb(88,155,243)">MATA PELAJARAN</th>
                <th class="text-center" style="background-color:rgb(88,155,243)">GURU</th>
                <th class="text-center" style="background-color:rgb(88,155,243)">RUANG</th>
                <th class="text-center" style="background-color:rgb(88,155,243)">KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
            {{-- MONGODB: $jadwal adalah array list hari (controller mengirim) --}}
            @foreach($jadwal as $day)
                @php $mps = $day['mata_pelajaran'] ?? []; $hari = $day['hari'] ?? ''; @endphp
                @if(count($mps) > 0)
                    @foreach($mps as $index => $mp)
                        <tr>
                            @if($index == 0)
                                <td class="text-center text-uppercase" rowspan="{{ count($mps ) }}" style="border-color:rgb(27,26,26);"><b>{{ $hari }}</b></td>
                            @endif
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center">{{ $mp['jam_mulai'] }} - {{ $mp['jam_selesai'] }}</td>
                            <td class="text-center">{{ $mp['mapel'] }}</td>
                            <td class="text-center">{{ $mp['guru'] }}</td>
                            <td class="text-center">{{ $mp['ruang'] }}</td>
                            <td class="text-center">{{ $mp['keterangan'] ?? '' }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td class="text-center text-uppercase"><b>{{ $hari }}</b></td>
                        <td colspan="6" class="text-center">Libur / Tidak ada jadwal</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</body>
</html>