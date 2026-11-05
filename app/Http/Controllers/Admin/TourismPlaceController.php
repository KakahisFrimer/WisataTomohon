<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TourismPlace;
use App\Models\TourismPlaceDetail;
use App\Models\Review;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TourismPlaceController extends Controller
{

   public function show($slug)
{
    $place = TourismPlace::with('details')->where('slug', $slug)
        ->firstOrFail();

    $photos = json_decode($place->foto__tmpt ?? '[]', true);
    if (!is_array($photos)) {
        $photos = [];
    }
    $place->photoList = count($photos) > 0
        ? collect($photos)->map(fn($p) => asset('storage/' . ltrim($p, '/')))
        : collect([asset('storage/wisata/default-16x9.webp')]);

   
    $place->contact = $place->contact ?: '-';
    $place->keterangan = $place->keterangan ?: '-';

   
    $detail = $place->details->first();
    $place->hariBuka = $detail->hari__buka ?? '-';
    $place->jamBuka = $detail->jam__buka ?? '-';
    $place->jamTutup = $detail->jam__tutup ?? '-';
    $place->hargaTiket = $detail->harga__tiket ?? '-';


    $lat = $place->latitude ? (float) $place->latitude : null;
    $lng = $place->longitude ? (float) $place->longitude : null;

    $currentCategory = strtolower(trim($place->category));

    $isTourism = in_array($currentCategory, [
        'wisata alam',
        'wisata budaya',
        'wisata buatan'
    ]);

    $userReview = \Auth::check()
        ? \App\Models\Review::where('wisata_id', $place->id__tmpt)
                ->where('user_id', \Auth::id())
                ->first()
        : null;


    $calculate = function ($item) use ($lat, $lng) {
        if ($lat && $lng && $item->latitude && $item->longitude) {
            $item->distance = $this->calculateDistance($lat, $lng, $item->latitude, $item->longitude);
        } else {
            $item->distance = null;
        }
        return $item;
    };

    $withPhotos = function ($item) {
        $photos = json_decode($item->foto__tmpt ?? '[]', true);
        if (!is_array($photos)) {
            $photos = [];
        }
        $item->photoList = count($photos) > 0
            ? collect($photos)->map(fn($p) => asset('storage/' . ltrim($p, '/')))
            : collect([asset('storage/wisata/default-16x9.webp')]);
        return $item;
    };

    $akomodasi = TourismPlace::where('category', 'akomodasi')
        ->whereNotNull('latitude')->whereNotNull('longitude')
        ->where('id__tmpt', '!=', $place->id__tmpt)
        ->get()
        ->map($calculate)
        ->map($withPhotos) 
        ->sortBy('distance')
        ->take(3);

    $kuliner = TourismPlace::where('category', 'kuliner')
        ->whereNotNull('latitude')->whereNotNull('longitude')
        ->where('id__tmpt', '!=', $place->id__tmpt)
        ->get()
        ->map($calculate)
        ->map($withPhotos)
        ->sortBy('distance')
        ->take(3);

$tourism = TourismPlace::whereIn(DB::raw('LOWER(category)'), [
        'wisata alam',
        'wisata budaya',
        'wisata buatan'
    ])
    ->where('id__tmpt', '!=', $place->id__tmpt)
    ->whereNotNull('latitude')
    ->whereNotNull('longitude')
    ->get()
    ->map($calculate)
    ->map($withPhotos)
    ->sortBy('distance')
    ->take(3);

   // ✅ Nearby berdasarkan kategori aktif
$nearbyQuery = TourismPlace::select(
    'tbl__wisata.id__tmpt',
    'tbl__wisata.slug',
    'tbl__wisata.name',
    'tbl__wisata.address',
    'tbl__wisata.category',
    'tbl__wisata.contact',
    'tbl__wisata.status',
    'tbl__wisata.foto__tmpt',
    'tbl__wisata.longitude',
    'tbl__wisata.latitude',
    DB::raw('COALESCE(AVG(reviews.rating),0) as average_rating')
)
    ->leftJoin('reviews', 'tbl__wisata.id__tmpt', '=', 'reviews.wisata_id')
    ->where('tbl__wisata.id__tmpt', '!=', $place->id__tmpt)
    ->whereNotNull('tbl__wisata.latitude')
    ->whereNotNull('tbl__wisata.longitude');

$nearbyQuery->whereRaw(
    'LOWER(tbl__wisata.category) = ?',
    [$currentCategory]
);

$nearby = $nearbyQuery
    ->groupBy(
    'tbl__wisata.id__tmpt',
    'tbl__wisata.slug',
    'tbl__wisata.name',
    'tbl__wisata.address',
    'tbl__wisata.category',
    'tbl__wisata.contact',
    'tbl__wisata.status',
    'tbl__wisata.foto__tmpt',
    'tbl__wisata.longitude',
    'tbl__wisata.latitude'
)
    ->get()
    ->map($calculate)
    ->map($withPhotos)
    ->sort(function ($a, $b) {

        if ($a->distance == $b->distance) {
            return $b->average_rating <=> $a->average_rating;
        }

        return $a->distance <=> $b->distance;
    })
    ->take(3);

    return view('detail-wisata', compact('place','userReview','akomodasi','kuliner','tourism','nearby'));
}



private function calculateDistance($lat1, $lng1, $lat2, $lng2)
{
    $earthRadius = 6371; // km
    $lat1 = deg2rad((float) $lat1);
    $lng1 = deg2rad((float) $lng1);
    $lat2 = deg2rad((float) $lat2);
    $lng2 = deg2rad((float) $lng2);

    $dlat = $lat2 - $lat1;
    $dlon = $lng2 - $lng1;

    $a = sin($dlat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($dlon / 2) ** 2;
    $c = 2 * asin(sqrt($a));

    return round($earthRadius * $c, 2);
}




    
    public function index(Request $request)
    {
        $query = TourismPlace::query();

        
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', "%{$request->search}%")
                  ->orWhere('category', 'LIKE', "%{$request->search}%")
                  ->orWhere('status', 'LIKE', "%{$request->search}%");
            });
        }

        
        if ($request->filled('showCategory') && $request->showCategory !== 'Semua') {
            $query->where('category', $request->showCategory);
        }

        
        if ($request->has('filterStatus') && $request->filterStatus !== 'Semua') {
            $query->where('status', $request->filterStatus);
        }

        
        if ($request->filled('sortBy')) {
            switch ($request->sortBy) {
                case 'name':
                    $query->orderBy('name', 'asc'); break;
                case 'category':
                    $query->orderByRaw("FIELD(category, 'Wisata Alam', 'Wisata Buatan', 'Wisata Budaya', 'Kuliner', 'Akomodasi')"); break;
                case 'status':
                    $query->orderByRaw("FIELD(status, 'Beroperasi', 'Berhenti beroperasi')"); break;
            }
        } else {
            $query->orderBy('id__tmpt', 'desc');
        }

        $places = $query->paginate(10)->withQueryString();

        return view('admin.data-wisata', compact('places'));
    }

  
    public function create()
    {
        return view('admin.create-wisata');
    }

  
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'category' => 'required|string',
            'contact' => 'nullable|string|max:255',
            'status' => 'required|string',

            'foto__tmpt.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            'latitude' => 'required',
            'longitude' => 'required',

            // Detail wisata
            'hari__buka.0' => 'required|string',
            'jam__buka.0' => 'required',
            'jam__tutup.0' => 'required',
            'harga__tiket.0' => 'required|numeric|min:0',
        ], [
            'name.required' => 'Nama tempat wajib diisi.',
            'address.required' => 'Alamat wajib diisi.',
            'category.required' => 'Jenis wisata wajib dipilih.',
            'status.required' => 'Status wajib dipilih.',

            'latitude.required' => 'Silakan pilih lokasi pada peta.',
            'longitude.required' => 'Silakan pilih lokasi pada peta.',

            'hari__buka.0.required' => 'Hari buka wajib diisi.',
            'jam__buka.0.required' => 'Jam buka wajib diisi.',
            'jam__tutup.0.required' => 'Jam tutup wajib diisi.',
            'harga__tiket.0.required' => 'Harga tiket wajib diisi.',
            'harga__tiket.0.numeric' => 'Harga tiket harus berupa angka.',
        ]);

        $wisata = new TourismPlace();

        $slug = Str::slug($request->name);
        
        $count = TourismPlace::where('slug', 'LIKE', "{$slug}%")->count();
        
        $wisata->name = $request->name;
        $wisata->slug = $count ? "{$slug}-".($count + 1) : $slug;
        
        $wisata->address = $request->address ?: '-';
        $wisata->category = $request->category;
        $wisata->contact = $request->contact ?: '-';
        $wisata->status = $request->status;
        $wisata->latitude = $request->latitude ?: '0';
        $wisata->longitude = $request->longitude ?: '0';

        // Foto
        $fotoPaths = [];
        if ($request->hasFile('foto__tmpt')) {
            foreach ($request->file('foto__tmpt') as $file) {
                $filename = time().'_'.Str::random(10).'.'.$file->getClientOriginalExtension();
                $destination = $_SERVER['DOCUMENT_ROOT'] . '/storage/wisata';

                if (!file_exists($destination)) {
                    mkdir($destination, 0755, true);
                }
                
                $file->move($destination, $filename);
                
                $path = 'wisata/' . $filename;
                $fotoPaths[] = $path;
            }
            $wisata->foto__tmpt = json_encode($fotoPaths);
        } else {
            $wisata->foto__tmpt = json_encode(['wisata/default-16x9.webp']);
        }

        $wisata->save();

        // Detail
        if ($request->hari__buka && is_array($request->hari__buka)) {
            foreach ($request->hari__buka as $i => $hari) {
                if ($hari || $request->jam__buka[$i] || $request->jam__tutup[$i] || $request->harga__tiket[$i]) {
                    TourismPlaceDetail::create([
                        'wisata_id' => $wisata->id__tmpt,
                        'hari__buka' => $hari,
                        'jam__buka' => $request->jam__buka[$i] ?? null,
                        'jam__tutup' => $request->jam__tutup[$i] ?? null,
                        'harga__tiket' => $request->harga__tiket[$i] ?: '-',
                    ]);
                }
            }
        }

        return redirect()
    ->route('admin.wisata.index')
    ->with('success', 'Data wisata berhasil ditambahkan');
    }

   
    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:Beroperasi,Berhenti beroperasi']);

        $place = TourismPlace::findOrFail($id);
        $place->status = $request->status;
        $place->save();

        return back()->with('success', 'Status berhasil diperbarui!');
    }

    // Edit
    public function edit($id)
    {
        $wisata = TourismPlace::with('details')->findOrFail($id);
        return view('admin.edit-wisata', compact('wisata'));
    }

    public function update(Request $request, $id)
    {
         $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'category' => 'required|string',
            'contact' => 'nullable|string|max:255',
            'status' => 'required|string',

            'foto__tmpt.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            'latitude' => 'required',
            'longitude' => 'required',

            // Detail wisata
            'hari__buka.0' => 'required|string',
            'jam__buka.0' => 'required',
            'jam__tutup.0' => 'required',
            'harga__tiket.0' => 'required|numeric|min:0',
        ], [
            'name.required' => 'Nama tempat wajib diisi.',
            'address.required' => 'Alamat wajib diisi.',
            'category.required' => 'Jenis wisata wajib dipilih.',
            'status.required' => 'Status wajib dipilih.',

            'latitude.required' => 'Silakan pilih lokasi pada peta.',
            'longitude.required' => 'Silakan pilih lokasi pada peta.',

            'hari__buka.0.required' => 'Hari buka wajib diisi.',
            'jam__buka.0.required' => 'Jam buka wajib diisi.',
            'jam__tutup.0.required' => 'Jam tutup wajib diisi.',
            'harga__tiket.0.required' => 'Harga tiket wajib diisi.',
            'harga__tiket.0.numeric' => 'Harga tiket harus berupa angka.',
        ]);

        $wisata = TourismPlace::findOrFail($id);

        // Foto lama
        $fotoLama = $wisata->foto__tmpt ? json_decode($wisata->foto__tmpt, true) : [];

        // Hapus foto
        if ($request->filled('hapus_foto')) {
            foreach ($request->hapus_foto as $hapus) {
                if (($key = array_search($hapus, $fotoLama)) !== false) {
                    unset($fotoLama[$key]);
                    $filePath = public_path('storage/' . $hapus);

                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
            }
        }

        // Upload foto baru
        if ($request->hasFile('foto__tmpt')) {
            foreach ($request->file('foto__tmpt') as $file) {
                $filename = time().'_'.Str::random(10).'.'.$file->getClientOriginalExtension();
               $destination = $_SERVER['DOCUMENT_ROOT'] . '/storage/wisata';

                if (!file_exists($destination)) {
                    mkdir($destination, 0755, true);
                }
                
                $file->move($destination, $filename);
                
                $fotoLama[] = 'wisata/' . $filename;
             
            }
        }

        $wisata->foto__tmpt = json_encode(array_values($fotoLama));

        $slug = Str::slug($request->name);

        $count = TourismPlace::where('slug', 'LIKE', "{$slug}%")
            ->where('id__tmpt', '!=', $wisata->id__tmpt)
            ->count();
        
        $wisata->name = $request->name;
        $wisata->slug = $count ? "{$slug}-".($count + 1) : $slug;
        
        $wisata->address = $request->address;
        $wisata->category = $request->category;
        $wisata->contact = $request->contact ?: '-';
        $wisata->status = $request->status;
        $wisata->latitude = $request->latitude;
        $wisata->longitude = $request->longitude;
        $wisata->save();

        // Update detail
        TourismPlaceDetail::where('wisata_id', $wisata->id__tmpt)->delete();
        if ($request->hari__buka && is_array($request->hari__buka)) {
            foreach ($request->hari__buka as $i => $hari) {
                if ($hari || $request->jam__buka[$i] || $request->jam__tutup[$i] || $request->harga__tiket[$i]) {
                    TourismPlaceDetail::create([
                        'wisata_id' => $wisata->id__tmpt,
                        'hari__buka' => $hari,
                        'jam__buka' => $request->jam__buka[$i] ?? null,
                        'jam__tutup' => $request->jam__tutup[$i] ?? null,
                        'harga__tiket' => $request->harga__tiket[$i] ?? '-',
                    ]);
                }
            }
        }

        return redirect()->route('admin.wisata.edit', $wisata->id__tmpt)
                 ->with('success', 'Data berhasil diupdate!');
    }

    
    public function destroy($id)
{
    $wisata = TourismPlace::findOrFail($id);

    $photos = json_decode($wisata->foto__tmpt, true);

    if ($photos) {
        foreach ($photos as $photo) {

            if ($photo === 'wisata/default-16x9.webp') {
                continue;
            }

            $filePath = public_path('storage/' . $photo);

            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }

    TourismPlaceDetail::where(
        'wisata_id',
        $wisata->id__tmpt
    )->delete();

    Review::where(
        'wisata_id',
        $wisata->id__tmpt
    )->delete();

    $wisata->delete();

    return redirect()
        ->route('admin.wisata.index')
        ->with('success', 'Data berhasil dihapus!');
}
}
