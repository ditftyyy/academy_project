<?php

namespace App\Models\MongoDB;

use Jenssegers\Mongodb\Eloquent\Model;  // <-- GANTI INI

class Inventaris extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'inventaris';

    protected $fillable = [
        // Data barang
        'nama_barang',
        'jenis',
        'tahun_pengadaan',
        'image',
        
        // Stok
        'jumlah_seluruh',
        'jumlah_baik',
        'jumlah_rusak',
        
        // Lokasi
        'ruang',
        
        // Riwayat peminjaman
        'riwayat_peminjaman',
    ];

    protected $casts = [
        'ruang' => 'array',
        'riwayat_peminjaman' => 'array',
        'tahun_pengadaan' => 'date',
    ];

    public $timestamps = true;

    // ========== SCOPES ==========
    
    public function scopeByRuangan($query, $ruangId)
    {
        return $query->where('ruang.id', $ruangId);
    }

    public function scopeByJenis($query, $jenis)
    {
        return $query->where('jenis', $jenis);
    }

    public function scopeStokMenipis($query, $batas = 10)
    {
        return $query->where('jumlah_baik', '<=', $batas);
    }

    // ========== ACCESSORS ==========
    
    public function getTotalBarangAttribute()
    {
        return ($this->jumlah_baik ?? 0) + ($this->jumlah_rusak ?? 0);
    }

    public function getNamaRuanganAttribute()
    {
        return $this->ruang['nama'] ?? 'Tidak diketahui';
    }

    public function getStatusStokAttribute()
    {
        $baik = $this->jumlah_baik ?? 0;
        
        if ($baik == 0) return 'Habis';
        if ($baik <= 5) return 'Menipis';
        if ($baik <= 20) return 'Terbatas';
        return 'Tersedia';
    }

    // ========== METHODS ==========
    
    /**
     * Mencatat peminjaman baru
     */
    public function catatPeminjaman(array $data): void
    {
        $this->push('riwayat_peminjaman', [
            'nama_peminjam' => $data['nama_peminjam'],
            'jumlah' => $data['jumlah'],
            'tanggal_pinjam' => $data['tanggal_pinjam'],
            'tanggal_kembali' => $data['tanggal_kembali'] ?? null,
            'surat' => $data['surat'] ?? null,
            'status' => 'dipinjam',
            'created_at' => now()->toDateTimeString(),
        ]);

        // Kurangi stok
        $this->decrement('jumlah_baik', $data['jumlah']);
    }

    /**
     * Mencatat pengembalian
     */
    public function catatPengembalian(int $peminjamanIndex, int $jumlahDikembalikan): void
    {
        $riwayat = $this->riwayat_peminjaman;
        
        if (isset($riwayat[$peminjamanIndex])) {
            $riwayat[$peminjamanIndex]['status'] = 'dikembalikan';
            $riwayat[$peminjamanIndex]['tanggal_kembali_aktual'] = now()->toDateTimeString();
            
            $this->riwayat_peminjaman = $riwayat;
            $this->increment('jumlah_baik', $jumlahDikembalikan);
            $this->save();
        }
    }

    /**
     * Mencatat kerusakan barang
     */
    public function catatKerusakan(int $jumlahRusak, string $keterangan): void
    {
        if ($this->jumlah_baik >= $jumlahRusak) {
            $this->decrement('jumlah_baik', $jumlahRusak);
            $this->increment('jumlah_rusak', $jumlahRusak);
            $this->save();
        }
    }
}