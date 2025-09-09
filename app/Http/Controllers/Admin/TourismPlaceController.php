<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TourismPlace;
use Illuminate\Support\Facades\Storage;


class TourismPlaceController extends Controller
{
    /**
     * Tampilkan daftar tempat wisata dengan filter & pagination
     */
    public function index(Request $request)
    {
        $query = TourismPlace::query();

        // 🔍 Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', "%{$request->search}%")
                  ->orWhere('category', 'LIKE', "%{$request->search}%")
                  ->orWhere('status', 'LIKE', "%{$request->search}%");
            });
        }

        // 📌 Filter kategori (Show)
        if ($request->filled('showCategory') && $request->showCategory !== 'Semua') {
            $query->where('category', $request->showCategory);
        }

        // 📌 Filter by status
        if ($request->has('filterStatus')) {
            if ($request->filterStatus !== 'Semua') {
                $query->where('status', $request->filterStatus);
            }
        }



        // 📌 Sorting
        if ($request->filled('sortBy')) {
            switch ($request->sortBy) {
                case 'name':
                    $query->orderBy('name', 'asc'); // A-Z
                    break;

                case 'category':
                    $query->orderByRaw("
                        FIELD(category, 'Wisata Alam', 'Wisata Buatan', 'Wisata Budaya', 'Kuliner', 'Akomodasi')
                    ");
                    break;

                case 'status':
                    $query->orderByRaw("
                        FIELD(status, 'Beroperasi', 'Berhenti beroperasi')
                    ");
                    break;
            }
        } else {
            // Default urut berdasarkan id terbaru
            $query->orderBy('id__tmpt', 'desc');
        }

        // 🔢 Pagination (10 per page) + simpan query string
        $places = $query->paginate(10)->withQueryString();

        return view('admin.data-wisata', compact('places'));
    }

    public function create()
{
    return view('admin.create-wisata');
}

public function store(Request $request)
{
    // 1. Validasi input utama
    $request->validate([
        'name'   => 'required|string|max:255',
        'address'       => 'required|string',
        'category'=> 'required|string',
        'contact'      => 'nullable|string|max:255',
        'status'       => 'required|string',
        'foto__tmpt.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'latitude'     => 'required',
        'longitude'    => 'required',
    ]);

    // 2. Simpan data ke tbl__wisata
    $wisata = new \App\Models\TourismPlace();
    $wisata->name   = $request->name;
    $wisata->address       = $request->address ?: '-';
    $wisata->category= $request->category;
    $wisata->contact      = $request->contact ?: '-';
    $wisata->status       = $request->status;
    $wisata->latitude     = $request->latitude ?: '0';
    $wisata->longitude    = $request->longitude ?: '0';

    // 3. Upload foto (bisa banyak)
    $fotoPaths = [];
    if ($request->hasFile('foto__tmpt')) {
        foreach ($request->file('foto__tmpt') as $file) {
            $filename = time().'_'.$file->getClientOriginalName();
            $path = $file->storeAs('wisata', $filename, 'public');
            $fotoPaths[] = $path;
        }
        // Simpan sebagai JSON ke kolom foto__tmpt
        $wisata->foto__tmpt = json_encode($fotoPaths);
    } else {
        // default foto
        $wisata->foto__tmpt = json_encode(['wisata/default-16x9.jpg']);
    }

    $wisata->save();

    // 4. Simpan detail ke tbl__wisata_details
    if ($request->hari__buka && is_array($request->hari__buka)) {
        foreach ($request->hari__buka as $i => $hari) {
            if ($hari || $request->jam__buka[$i] || $request->jam__tutup[$i] || $request->harga__tiket[$i]) {
                \App\Models\TourismPlaceDetail::create([
                    'wisata_id'   => $wisata->id__tmpt,
                    'hari__buka'  => $hari,
                    'jam__buka'   => $request->jam__buka[$i] ?? null,
                    'jam__tutup'  => $request->jam__tutup[$i] ?? null,
                    'harga__tiket'=> $request->harga__tiket[$i] ?: '-',
                ]);
            }
        }
    }       

    return redirect()->route('admin.wisata.create')->with('success', 'Data wisata berhasil ditambahkan');
}



    /**
     * Update status buka/tutup/berhenti
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Beroperasi,Berhenti beroperasi'
        ]);

        $place = TourismPlace::findOrFail($id);
        $place->status = $request->status;
        $place->save();

        return back()->with('success', 'Status berhasil diperbarui!');
    }

    /**
     * Edit
     */
    public function edit($id)
{
    $wisata = \App\Models\TourismPlace::with('details')->findOrFail($id);
    return view('admin.edit-wisata', compact('wisata'));
}

public function update(Request $request, $id)
{
    // Validasi
    $request->validate([
        'name'   => 'required|string|max:255',
        'address'=> 'required|string',
        'category'=> 'required|string',
        'contact'=> 'nullable|string|max:255',
        'status' => 'required|string',
        'foto__tmpt.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'latitude' => 'required',
        'longitude'=> 'required',
    ]);

    $wisata = \App\Models\TourismPlace::findOrFail($id);

    // Ambil foto lama
    $fotoLama = $wisata->foto__tmpt ? json_decode($wisata->foto__tmpt, true) : [];

    // 🔹 Hapus foto yang dipilih
    if ($request->filled('hapus_foto')) {
        foreach ($request->hapus_foto as $hapus) {
            if (($key = array_search($hapus, $fotoLama)) !== false) {
                unset($fotoLama[$key]); // hapus dari array
                // optional: hapus file fisik
                Storage::disk('public')->delete($hapus);
            }
        }
    }

    // 🔹 Upload foto baru
    if ($request->hasFile('foto__tmpt')) {
        foreach ($request->file('foto__tmpt') as $file) {
            $filename = time().'_'.$file->getClientOriginalName();
            $path = $file->storeAs('wisata', $filename, 'public');
            $fotoLama[] = $path;
        }
    }

    // 🔹 Simpan kembali array foto (setelah hapus & tambah)
    $wisata->foto__tmpt = json_encode(array_values($fotoLama));

    // Update data lainnya
    $wisata->name = $request->name;
    $wisata->address = $request->address;
    $wisata->category = $request->category;
    $wisata->contact = $request->contact ?: '-';
    $wisata->status = $request->status;
    $wisata->latitude = $request->latitude;
    $wisata->longitude = $request->longitude;

    $wisata->save();

    // Update detail (hapus lama, insert ulang)
    \App\Models\TourismPlaceDetail::where('wisata_id', $wisata->id__tmpt)->delete();
    if ($request->hari__buka && is_array($request->hari__buka)) {
        foreach ($request->hari__buka as $i => $hari) {
            if ($hari || $request->jam__buka[$i] || $request->jam__tutup[$i] || $request->harga__tiket[$i]) {
                \App\Models\TourismPlaceDetail::create([
                    'wisata_id'   => $wisata->id__tmpt,
                    'hari__buka'  => $hari,
                    'jam__buka'   => $request->jam__buka[$i] ?? null,
                    'jam__tutup'  => $request->jam__tutup[$i] ?? null,
                    'harga__tiket'=> $request->harga__tiket[$i] ?? '-',
                ]);
            }
        }
    }

        return redirect()->route('admin.wisata.edit', $wisata->id__tmpt)
                 ->with('success', 'Data berhasil diupdate!');
}




    /**
     * Hapus
     */
    public function destroy($id)
    {
        $wisata = TourismPlace::findOrFail($id);
        $wisata->delete();

        return redirect()->route('admin.wisata.index')
                        ->with('success', 'Data berhasil dihapus!');
    }

}
