@extends('layouts.admin')

@section('dashboard')
<link rel="stylesheet" href="{{ asset('css/admin-style.css') }}">
<title>Data Event</title>

<header class="main-header">
    <div class="greeting">
        <h1>Data Event</h1>
        <p class="sub">Kelola event & kegiatan pariwisata Kota Tomohon</p>
    </div>
     
    <div class="search-area">
        <form method="GET" action="{{ route('admin.events.index') }}">
            <input
                id="searchDest"
                name="search"
                type="search"
                placeholder="Cari event..."
                value="{{ request('search') }}"
            />
            <button type="button" class="clear-btn" id="clearSearch">&times;</button>
            <button type="submit"><i data-feather="search" width="12" height="12"></i></button>
        </form>
    </div>
</header>

<section class="grid wisata-page">
    <div class="card full-width">
        <div class="card-header toolbar">
            <div class="filters">
               
                <div class="filter-group">
                    <i data-feather="list" width="18" height="18" color="#1f1f1f"></i>
                    <form method="GET" action="{{ route('admin.events.index') }}">
                        <select id="showCategory" name="showCategory" onchange="this.form.submit()">
                            <option value="" disabled {{ request('showCategory') ? '' : 'selected' }}>Show</option>
                            <option value="Semua" {{ request('showCategory') == 'Semua' ? 'selected' : '' }}>Semua</option>
                            <option value="Festival & Budaya" {{ request('showCategory') == 'Festival & Budaya' ? 'selected' : '' }}>Festival & Budaya</option>
                            <option value="Musik & Hiburan" {{ request('showCategory') == 'Musik & Hiburan' ? 'selected' : '' }}>Musik & Hiburan</option>
                            <option value="Kuliner & Pasar Rakyat" {{ request('showCategory') == 'Kuliner & Pasar Rakyat' ? 'selected' : '' }}>Kuliner & Pasar Rakyat</option>
                            <option value="Olahraga & Komunitas" {{ request('showCategory') == 'Olahraga & Komunitas' ? 'selected' : '' }}>Olahraga & Komunitas</option>
                            <option value="Edukasi & Religi" {{ request('showCategory') == 'Edukasi & Religi' ? 'selected' : '' }}>Edukasi & Religi</option>
                        </select>
                    </form>
                </div>

                <div class="filter-group">
                    <i data-feather="sliders" width="18" height="18" color="#1f1f1f"></i>
                    <select id="sortBy" name="sortBy" onchange="this.form.submit()">
                        <option value="" disabled {{ request('sortBy') ? '' : 'selected' }}>Sort By</option>
                        <option value="title" {{ request('sortBy') === 'title' ? 'selected' : '' }}>Nama Event</option>
                        <option value="start_date" {{ request('sortBy') === 'start_date' ? 'selected' : '' }}>Tanggal Mulai</option>
                    </select>
                </div>

                <div class="filter-group">
                    <i data-feather="filter" width="18" height="18" color="#1f1f1f"></i>
                    <form method="GET" action="{{ route('admin.events.index') }}">
                        <select id="filterStatus" name="filterStatus" onchange="this.form.submit()">
                            <option value="" disabled {{ request('filterStatus') ? '' : 'selected' }}>Filter</option>
                            <option value="Semua" {{ request('filterStatus') == 'Semua' ? 'selected' : '' }}>Semua Status</option>
                            <option value="Akan Datang" {{ request('filterStatus') == 'Akan Datang' ? 'selected' : '' }}>Akan Datang</option>
                            <option value="Dimulai" {{ request('filterStatus') == 'Dimulai' ? 'selected' : '' }}>Dimulai</option>
                            <option value="Selesai" {{ request('filterStatus') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </form>
                </div>
            </div>

            <button type="button" class="btn primary" onclick="window.location='{{ route('admin.events.create') }}'">
                + Tambah Data
            </button>
        </div>

        <div class="table-responsive">
            <table class="data-table table-wisata">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Event</th>
                        <th>Kategori</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="tablewisata">
                    @forelse($events as $index => $event)
                        <tr data-category="{{ $event->category }}">
                            <td>{{ $events instanceof \Illuminate\Pagination\LengthAwarePaginator ? $events->firstItem() + $index : $loop->iteration }}</td>
                            <td>{{ $event->title }}</td>
                            <td>{{ $event->category }}</td>
                            <td>{{ \Carbon\Carbon::parse($event->start_date)->format('d M Y') }}</td>
                            <td>
                                @if($event->end_date)
                                    {{ \Carbon\Carbon::parse($event->end_date)->format('d M Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @php
                                    $today = \Carbon\Carbon::today();
                                    $start = \Carbon\Carbon::parse($event->start_date);
                                    $end   = $event->end_date ? \Carbon\Carbon::parse($event->end_date) : null;
                                    if ($end && $end->lt($today)) {
                                        $status = "Selesai";
                                    } elseif ($start->gt($today)) {
                                        $status = "Akan Datang";
                                    } elseif ($start->eq($today)) {
                                        $status = "Dimulai";
                                    } else {
                                        $status = "Dimulai";
                                    }
                                @endphp
                                <span>{{ $status }}</span>
                            </td>
                            <td class="fitur">
                                <a href="{{ route('admin.events.edit', $event->id) }}" class="btn small">
                                    <i data-feather="edit" width="15" height="15"></i><span>Edit</span>
                                </a>
                                <form action="{{ route('admin.events.destroy', $event->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn danger small" onclick="return confirm('Yakin ingin menghapus data ini?')">
                                        <i data-feather="trash-2" width="15" height="15"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">Tidak ada data event.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $events->links('pagination::bootstrap-5') }}
        </div>
    </div>
</section>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const searchInput   = document.getElementById("searchDest");
    const showCategory  = document.getElementById("showCategory");
    const sortBy        = document.getElementById("sortBy");
    const filterStatus  = document.getElementById("filterStatus");

    function applyFilters() {
        const params = new URLSearchParams(window.location.search);

        if (searchInput && searchInput.value.trim() !== "") {
            params.set("search", searchInput.value.trim());
        } else {
            params.delete("search");
        }

        if (showCategory && showCategory.value) {
            params.set("showCategory", showCategory.value);
        } else {
            params.delete("showCategory");
        }

        if (sortBy && sortBy.value) {
            params.set("sortBy", sortBy.value);
        } else {
            params.delete("sortBy");
        }

        if (filterStatus && filterStatus.value) {
            params.set("filterStatus", filterStatus.value);
        } else {
            params.delete("filterStatus");
        }

        params.delete("page"); 
        window.location.search = params.toString();
    }

    let searchTimer;
    if (searchInput) {
        searchInput.addEventListener("keyup", function () {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(applyFilters, 400);
        });
    }

    if (showCategory) {
        showCategory.addEventListener("change", applyFilters);
    }
    if (sortBy) {
        sortBy.addEventListener("change", applyFilters);
    }
    if (filterStatus) {
        filterStatus.addEventListener("change", applyFilters);
    }
});
</script>

@if(session('success'))
<div id="popup" class="popup">
    <div class="popup-content">
        <h2>{{ session('success') }}</h2>
        <div style="margin-top: 20px; display: flex; gap: 10px; justify-content: center;">
            <button onclick="closePopup()" class="btn">Tutup</button>
        </div>
    </div>
</div>

<script>
    function closePopup() {
        document.getElementById('popup').style.display = 'none';
    }
    document.getElementById('popup').style.display = 'flex';
</script>

<style>
    .popup {
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.6);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }
    .popup-content {
        background: #fff;
        padding: 20px 30px;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        animation: fadeIn 0.3s ease;
    }
    @keyframes fadeIn {
        from {opacity: 0; transform: scale(0.9);}
        to {opacity: 1; transform: scale(1);}
    }
</style>
@endif

@endsection
