@extends('layouts.admin')

@section('dashboard')
<link rel="stylesheet" href="{{ asset('css/admin-style.css') }}">
<title>Data Wisata</title>

<header class="main-header">
    <div class="greeting">
        <h1>Data Wisata</h1>
        <p class="sub">Kelola destinasi wisata Kota Tomohon</p>
    </div>
     
    <div class="search-area">
        <form method="GET" action="{{ route('admin.wisata.index') }}">
            <input
                id="searchDest"
                name="search"
                type="search"
                placeholder="Cari destinasi..."
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
                <!-- Show -->
                <div class="filter-group">
                    <i data-feather="list" width="18" height="18" color="#1f1f1f"></i>
                    <form method="GET" action="{{ route('admin.wisata.index') }}">
                        <select id="showCategory" name="showCategory" onchange="this.form.submit()">
                            <option value="" disabled {{ request('showCategory') ? '' : 'selected' }}>Show</option>
                            <option value="Semua" {{ request('showCategory') == 'Semua' ? 'selected' : '' }}>Semua</option>
                            <option value="Wisata Alam" {{ request('showCategory') == 'Wisata Alam' ? 'selected' : '' }}>Wisata Alam</option>
                            <option value="Wisata Buatan" {{ request('showCategory') == 'Wisata Buatan' ? 'selected' : '' }}>Wisata Buatan</option>
                            <option value="Wisata Budaya" {{ request('showCategory') == 'Wisata Budaya' ? 'selected' : '' }}>Wisata Budaya</option>
                            <option value="Kuliner" {{ request('showCategory') == 'Kuliner' ? 'selected' : '' }}>Kuliner</option>
                            <option value="Akomodasi" {{ request('showCategory') == 'Akomodasi' ? 'selected' : '' }}>Akomodasi</option>
                        </select>
                    </form>

                </div>

                <!-- Sort By -->
                <div class="filter-group">
                    <i data-feather="sliders" width="18" height="18" color="#1f1f1f"></i>
                    <select id="sortBy" name="sortBy" onchange="this.form.submit()">
                        <option value="" disabled {{ request('sortBy') ? '' : 'selected' }}>Sort By</option>
                        <option value="name" {{ request('sortBy') === 'name' ? 'selected' : '' }}>Nama</option>
                        <option value="category" {{ request('sortBy') === 'category' ? 'selected' : '' }}>Kategori</option>
                    </select>
                </div>


                <!-- Filter -->
                <div class="filter-group">
                    <i data-feather="filter" width="18" height="18" color="#1f1f1f"></i>
                    <form method="GET" action="{{ route('admin.wisata.index') }}">
                        <select id="filterStatus" name="filterStatus" onchange="this.form.submit()">
                            <option value="" disabled {{ request('filterStatus') ? '' : 'selected' }}>Filter</option>
                            <option value="Semua" {{ request('filterStatus') == 'Semua' ? 'selected' : '' }}>Semua Status</option>
                            <option value="Beroperasi" {{ request('filterStatus') == 'Beroperasi' ? 'selected' : '' }}>Beroperasi</option>
                            <option value="Berhenti beroperasi" {{ request('filterStatus') == 'Berhenti beroperasi' ? 'selected' : '' }}>Berhenti Beroperasi</option>
                        </select>
                    </form>
                </div>
            </div>


            <!-- Tambah Data -->
            <button type="button" class="btn primary" onclick="window.location='{{ route('admin.wisata.create') }}'">
                + Tambah Data
            </button>
        </div>

        <div class="table-responsive">
            <table class="data-table table-wisata">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Tempat</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="tablewisata">
                    @forelse($places as $index => $place)
                        <tr data-category="{{ $place->categoty ?? $place->category }}">
                            <td>{{ $places instanceof \Illuminate\Pagination\LengthAwarePaginator ? $places->firstItem() + $index : $loop->iteration }}</td>
                            <td>{{ $place->name ?? $place->name }}</td>
                            <td>{{ $place->category ?? $place->categoty }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.wisata.updateStatus', $place->id__tmpt ?? $place->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" onchange="this.form.submit()">
                                        <option value="Beroperasi" {{ $place->status === 'Beroperasi' ? 'selected' : '' }}>Beroperasi</option>
                                        <option value="Berhenti beroperasi" {{ $place->status === 'Berhenti beroperasi' ? 'selected' : '' }}>Berhenti beroperasi</option>
                                    </select>
                                </form>
                            </td>

                            <td class="fitur">
                                <a href="{{ route('admin.wisata.edit', $place->id__tmpt ?? $place->id) }}" class="btn small">
                                    <i data-feather="edit" width="15" height="15"></i><span>Edit</span>
                                </a>
                                <form action="{{ route('admin.wisata.destroy', $place->id__tmpt ?? $place->id) }}" method="POST" style="display:inline;">
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
                            <td colspan="5">Tidak ada data wisata.</td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination">
            {{ $places->links('pagination::bootstrap-5') }}
        </div>
    </div>
</section>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const searchInput   = document.getElementById("searchInput");
    const showCategory  = document.getElementById("showCategory");
    const sortBy        = document.getElementById("sortBy");
    const filterStatus  = document.getElementById("filterStatus"); // ✨ tambahkan

    function applyFilters() {
        const params = new URLSearchParams(window.location.search);

        // 🔍 search
        if (searchInput && searchInput.value.trim() !== "") {
            params.set("search", searchInput.value.trim());
        } else {
            params.delete("search");
        }

        // 📌 showCategory
        if (showCategory && showCategory.value) {
            params.set("showCategory", showCategory.value);
        } else {
            params.delete("showCategory");
        }

        // 📌 sortBy
        if (sortBy && sortBy.value) {
            params.set("sortBy", sortBy.value);
        } else {
            params.delete("sortBy");
        }

        // 📌 filterStatus ✨ tambahan baru
        if (filterStatus && filterStatus.value) {
            params.set("filterStatus", filterStatus.value);
        } else {
            params.delete("filterStatus");
        }

        // Reset ke page 1 kalau ada perubahan
        params.delete("page");

        window.location.search = params.toString();
    }

    // 🔍 Event search realtime → kasih delay biar nggak spam request
    let searchTimer;
    if (searchInput) {
        searchInput.addEventListener("keyup", function () {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(applyFilters, 400); // ⏳ 400ms debounce
        });
    }

    if (showCategory) {
        showCategory.addEventListener("change", applyFilters);
    }

    if (sortBy) {
        sortBy.addEventListener("change", applyFilters);
    }

    if (filterStatus) { // ✨ tambahan
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
    // otomatis tampil saat ada session success
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
