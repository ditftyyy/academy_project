<?php

namespace App\Models\MongoDB;

use Jenssegers\Mongodb\Eloquent\Model;

class Inventaris extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'inventaris';

    protected $fillable = [
        'nama_barang', 'jenis', 'tahun_pengadaan', 'image',
        'jumlah_seluruh', 'jumlah_baik', 'jumlah_rusak',
        'ruang', 'riwayat_peminjaman',
    ];

    // Casting memastikan field jumlah bertipe integer saat diakses
    protected $casts = [
        'tahun_pengadaan' => 'date',
        'jumlah_seluruh'  => 'int',
        'jumlah_baik'     => 'int',
        'jumlah_rusak'    => 'int',
    ];

    public $timestamps = true;

    // ========== ACCESSORS untuk field yang mungkin masih string JSON ==========
    public function getRuangAttribute($value)
    {
        if (is_null($value)) return null;
        if (is_array($value)) return $value;
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : null;
        }
        return null;
    }

    public function getRiwayatPeminjamanAttribute($value)
    {
        if (is_null($value)) return [];
        if (is_array($value)) return $value;
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }
        return [];
    }

    // ========== SCOPE ==========
    public function scopeByRuangan($query, $ruangId)
    {
        return $query->where('ruang.id', (string) $ruangId);
    }

    // ========== METHODS ==========
    /**
     * Catat peminjaman baru
     */
    public function catatPeminjaman(array $data): void
    {
        // Ambil riwayat peminjaman saat ini
        $riwayat = $this->riwayat_peminjaman;
        
        // Tambahkan peminjaman baru
        $riwayat[] = [
            '_id'              => (string) new \MongoDB\BSON\ObjectId(),
            'nama_peminjam'    => $data['nama_peminjam'],
            'jumlah'           => $data['jumlah'],
            'tanggal_pinjam'   => $data['tanggal_pinjam'],
            'tanggal_kembali'  => $data['tanggal_kembali'] ?? null,
            'surat'            => $data['surat'] ?? null,
            'status'           => 'dipinjam',
            'status_pengajuan' => null,
            'created_at'       => now()->toDateTimeString(),
        ];
        
        $this->riwayat_peminjaman = $riwayat;
        
        // Kurangi stok jumlah_baik (konversi ke integer untuk menghindari error tipe data)
        $jumlahBaikSekarang = (int) $this->jumlah_baik;
        $this->jumlah_baik = $jumlahBaikSekarang - (int) $data['jumlah'];
        $this->save();
    }

    /**
     * Catat pengembalian barang
     */
    public function catatPengembalian(string $peminjamanId, int $jumlahDikembalikan): void
    {
        $riwayat = $this->riwayat_peminjaman;
        foreach ($riwayat as &$p) {
            if (($p['_id'] ?? '') === $peminjamanId && ($p['status'] ?? '') === 'dipinjam') {
                $p['status'] = 'dikembalikan';
                $p['tanggal_kembali_aktual'] = now()->toDateTimeString();
                $this->riwayat_peminjaman = $riwayat;
                
                // Kembalikan stok (konversi ke integer)
                $jumlahBaikSekarang = (int) $this->jumlah_baik;
                $this->jumlah_baik = $jumlahBaikSekarang + $jumlahDikembalikan;
                $this->save();
                break;
            }
        }
    }

    /**
     * Catat kerusakan barang
     */
    public function catatKerusakan(int $jumlahRusak, string $keterangan): void
    {
        $jumlahBaikSekarang = (int) $this->jumlah_baik;
        if ($jumlahBaikSekarang >= $jumlahRusak) {
            $this->jumlah_baik = $jumlahBaikSekarang - $jumlahRusak;
            $this->jumlah_rusak = (int) $this->jumlah_rusak + $jumlahRusak;
            $this->save();
        }
    }
}