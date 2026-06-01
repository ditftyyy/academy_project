<?php

namespace App\Http\Controllers;

use App\Models\MongoDB\Pengumuman as MongoPengumuman;
use Illuminate\Http\Request;

class KerjaSamaController extends Controller
{
    public function create()
    {
        return view('pages.humas.data-mou.kerjasama-mou', [
            'title' => 'Tambah Data Kerja Sama',
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'nama_mitra' => 'required|string|max:255',
            'asal_mitra' => 'required|string|max:255',
            'deskripsi_singkat_mitra' => 'required|string',
            'tgl_mulai_kerjasama' => 'required|date',
            'tgl_berakhir_kerjasama' => 'required|date|after:tgl_mulai_kerjasama',
            'pt_mitra' => 'required|string|max:255',
            'file_mitra' => 'nullable|mimes:doc,docx,pdf|max:2048',
        ]);

        try {
            $fileName = null;
            $originalName = '';
            
            if ($request->hasFile('file_mitra')) {
                $file = $request->file('file_mitra');
                $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
                $originalName = $file->getClientOriginalName();
                $file->move(public_path('storage/kerjasama/file'), $fileName);
            }

            MongoPengumuman::catatKerjasama([
                'nama_mitra' => $request->nama_mitra,
                'asal_mitra' => $request->asal_mitra,
                'deskripsi' => $request->deskripsi_singkat_mitra,
                'pt_mitra' => $request->pt_mitra,
                'tanggal_mulai' => $request->tgl_mulai_kerjasama,
                'tanggal_berakhir' => $request->tgl_berakhir_kerjasama,
                'file' => $fileName,
                'original_name_file' => $originalName,
            ]);

            return redirect('/mou')->with('toast_success', 'Data Berhasil Ditambah');
        } catch (\Exception $e) {
            return redirect()->back()->with('toast_error', 'Error: ' . $e->getMessage());
        }
    }

    public function lihat()
    {
        return view('pages.humas.data-mou.data-kerjasama', [
            'title' => 'Data Kerjasama',
            'mou' => MongoPengumuman::byType('kerjasama')
                ->orderBy('created_at', 'desc')
                ->get(),
        ]);
    }

    public function edit($id)
    {
        $mou = MongoPengumuman::findOrFail($id);
        return view('pages.humas.data-mou.edit-data-kerjasama', [
            'mou' => $mou,
            'title' => 'Update Data Kerja Sama'
        ]);
    }

    public function update(Request $request, $id)
    {
        $mou = MongoPengumuman::findOrFail($id);
        
        $this->validate($request, [
            'nama_mitra' => 'required|string|max:255',
            'asal_mitra' => 'required|string|max:255',
            'deskripsi_singkat_mitra' => 'required|string',
            'tgl_mulai_kerjasama' => 'required|date',
            'tgl_berakhir_kerjasama' => 'required|date|after:tgl_mulai_kerjasama',
            'pt_mitra' => 'required|string|max:255',
            'file_mitra' => 'nullable|mimes:doc,docx,pdf|max:2048',
        ]);

        $dataTambahan = $mou->data_tambahan ?? [];
        $dataTambahan['nama_mitra'] = $request->nama_mitra;
        $dataTambahan['asal_mitra'] = $request->asal_mitra;
        $dataTambahan['pt_mitra'] = $request->pt_mitra;
        $dataTambahan['tanggal_mulai'] = $request->tgl_mulai_kerjasama;
        $dataTambahan['tanggal_berakhir'] = $request->tgl_berakhir_kerjasama;
        
        if ($request->hasFile('file_mitra')) {
            $file = $request->file('file_mitra');
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $file->move(public_path('storage/kerjasama/file'), $fileName);
            $dataTambahan['file'] = $fileName;
            $dataTambahan['original_name_file'] = $file->getClientOriginalName();
        }
        
        $mou->update([
            'title' => 'MoU: ' . $request->nama_mitra,
            'message' => $request->deskripsi_singkat_mitra,
            'data_tambahan' => $dataTambahan,
        ]);
        
        return redirect('/mou')->with('toast_success', 'Data berhasil diupdate');
    }

    public function destroy($id)
    {
        $mou = MongoPengumuman::findOrFail($id);
        $mou->delete();
        return redirect('/mou')->with('toast_success', 'Data Kerjasama Berhasil di Hapus');
    }
}