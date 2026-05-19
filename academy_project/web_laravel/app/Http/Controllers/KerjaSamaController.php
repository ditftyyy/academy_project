<?php

namespace App\Http\Controllers;

use App\Models\MongoDB\Pengumuman as MongoPengumuman;
use Illuminate\Http\Request;

class KerjaSamaController extends Controller
{
    /**
     * Halaman tambah MoU
     */
    public function create()
    {
        return view('pages.humas.data-mou.kerjasama-mou', [
            'title' => 'Tambah Data MoU',
            'mou' => MongoPengumuman::byType('kerjasama')->get(),
        ]);
    }

    /**
     * Simpan data kerjasama
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'nama_mitra' => 'required|string|max:255',
            'asal_mitra' => 'required|string|max:255',
            'deskripsi_singkat_mitra' => 'required|string',
            'tgl_mulai_kerjasama' => 'required|date',
            'tgl_berakhir_kerjasama' => 'required|date',
            'pt_mitra' => 'required|string|max:255',
            'tujuan_mitra' => 'required|string',
            'file_mitra' => 'nullable|mimes:doc,docx,pdf|max:2048',
        ]);

        try {
            // Upload file
            $fileName = null;
            $originalName = '';
            
            if ($request->hasFile('file_mitra')) {
                $file = $request->file('file_mitra');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $originalName = $file->getClientOriginalName();
                $file->move('storage/kerjasama/file/', $fileName);
            }

            // Simpan menggunakan model Pengumuman type kerjasama
            MongoPengumuman::catatKerjasama([
                'nama_mitra' => $request->nama_mitra,
                'asal_mitra' => $request->asal_mitra,
                'deskripsi' => $request->deskripsi_singkat_mitra,
                'pt_mitra' => $request->pt_mitra,
                'tujuan_mitra' => $request->tujuan_mitra,
                'tanggal_mulai' => $request->tgl_mulai_kerjasama,
                'tanggal_berakhir' => $request->tgl_berakhir_kerjasama,
                'file' => $fileName,
                'original_name_file' => $originalName,
            ]);

            return redirect('/mou')
                ->with('toast_success', 'Data Berhasil Ditambah');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('toast_error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Lihat semua data kerjasama
     */
    public function lihat()
    {
        return view('pages.humas.data-mou.data-kerjasama', [
            'title' => 'Data Kerjasama',
            'mou' => MongoPengumuman::byType('kerjasama')
                ->orderBy('created_at', 'desc')
                ->get(),
        ]);
    }

    /**
     * Halaman edit kerjasama
     */
    public function edit($id)
    {
        $mou = MongoPengumuman::findOrFail($id);
        
        return view('pages.humas.data-mou.edit-data-kerjasama', [
            'mou' => $mou,
            'title' => 'Update Data MoU'
        ]);
    }

    /**
     * Update data kerjasama
     */
    public function update(Request $request, $id)
    {
        $mou = MongoPengumuman::findOrFail($id);
        
        $this->validate($request, [
            'nama_mitra' => 'required|string|max:255',
            'asal_mitra' => 'required|string|max:255',
            'deskripsi_singkat_mitra' => 'required|string',
            'tgl_mulai_kerjasama' => 'required|date',
            'tgl_berakhir_kerjasama' => 'required|date',
            'pt_mitra' => 'required|string|max:255',
            'tujuan_mitra' => 'required|string',
            'file_mitra' => 'nullable|mimes:doc,docx,pdf|max:2048',
        ]);

        $dataTambahan = $mou->data_tambahan;
        $dataTambahan['nama_mitra'] = $request->nama_mitra;
        $dataTambahan['asal_mitra'] = $request->asal_mitra;
        $dataTambahan['pt_mitra'] = $request->pt_mitra;
        $dataTambahan['tanggal_mulai'] = $request->tgl_mulai_kerjasama;
        $dataTambahan['tanggal_berakhir'] = $request->tgl_berakhir_kerjasama;
        
        // Upload file baru jika ada
        if ($request->hasFile('file_mitra')) {
            $file = $request->file('file_mitra');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move('storage/kerjasama/file/', $fileName);
            $dataTambahan['file'] = $fileName;
            $dataTambahan['original_name_file'] = $file->getClientOriginalName();
        }
        
        $mou->update([
            'title' => 'MoU: ' . $request->nama_mitra,
            'message' => $request->deskripsi_singkat_mitra,
            'data_tambahan' => $dataTambahan,
        ]);
        
        return redirect('/mou')
            ->with('toast_success', 'Data berhasil diupdate');
    }

    /**
     * Hapus data kerjasama
     */
    public function destroy($id)
    {
        $mou = MongoPengumuman::findOrFail($id);
        $mou->delete();
        
        return redirect('/mou')
            ->with('toast_success', 'Data Kerjasama Berhasil di Hapus');
    }
}