@extends('layouts.admin')

@section('dashboard')
<link rel="stylesheet" href="{{ asset('css/admin-style.css') }}">
<title>Data Pengguna</title>

<header class="main-header">
    <div class="greeting">
        <h1>Data Pengguna</h1>
        <p class="sub">Kelola data pengguna</p>
    </div>
     
    <div class="search-area">
        <form method="GET" action="{{ route('admin.user.index') }}">
            <input
                id="searchInput"
                name="search"
                type="search"
                placeholder="Cari nama atau email/phone..."
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
                <!-- Filter Role -->
                <div class="filter-group">
                    <i data-feather="filter" width="18" height="18" color="#1f1f1f"></i>
                    <form method="GET" action="{{ route('admin.user.index') }}">
                        <select id="filterRole" name="filterRole" onchange="this.form.submit()">
                            <option value="" disabled {{ request('filterRole') ? '' : 'selected' }}>Filter Role</option>
                            <option value="Semua" {{ request('filterRole') == 'Semua' ? 'selected' : '' }}>Semua</option>
                            <option value="Admin" {{ request('filterRole') == 'Admin' ? 'selected' : '' }}>Admin</option>
                            <option value="User" {{ request('filterRole') == 'User' ? 'selected' : '' }}>User</option>
                        </select>
                    </form>
                </div>

                <div class="filter-group">
                    <i data-feather="sliders" width="18" height="18" color="#1f1f1f"></i>
                    <form method="GET" action="{{ route('admin.user.index') }}">
                        <select id="sortBy" name="sortBy" onchange="this.form.submit()">
                            <option value="" disabled {{ request('sortBy') ? '' : 'selected' }}>Sort By</option>
                            <option value="name" {{ request('sortBy') === 'name' ? 'selected' : '' }}>Nama</option>
                            <option value="role" {{ request('sortBy') === 'role' ? 'selected' : '' }}>Role</option>
                        </select>
                    </form>
                </div>
            </div>

            <button type="button" class="btn primary" onclick="window.location='{{ route('admin.user.create') }}'">
                + Tambah Data
            </button>
        </div>

        <div class="table-responsive">
            <table class="data-table table-wisata">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email/Phone</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="tableUser">
                    @forelse($users as $index => $user)
                        <tr>
                            <td>{{ $users instanceof \Illuminate\Pagination\LengthAwarePaginator ? $users->firstItem() + $index : $loop->iteration }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email_or_phone }}</td>
                            <td>{{ $user->role }}</td>
                            <td class="fitur">
                                <a href="{{ route('admin.user.edit', $user->id) }}" class="btn small">
                                    <i data-feather="edit" width="15" height="15"></i><span>Edit</span>
                                </a>
                                <form action="{{ route('admin.user.destroy', $user->id) }}" method="POST" style="display:inline;">
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
                            <td colspan="5">Tidak ada data pengguna.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $users->links('pagination::bootstrap-5') }}
        </div>
    </div>
</section>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const searchInput   = document.getElementById("searchInput");
    const sortBy        = document.getElementById("sortBy");
    const filterRole  = document.getElementById("filterRole"); // ✨ tambahkan

    function applyFilters() {
        const params = new URLSearchParams(window.location.search);

        if (searchInput && searchInput.value.trim() !== "") {
            params.set("search", searchInput.value.trim());
        } else {
            params.delete("search");
        }

        if (sortBy && sortBy.value) {
            params.set("sortBy", sortBy.value);
        } else {
            params.delete("sortBy");
        }

        if (filterRole && filterRole.value) {
            params.set("filterRole", filterRole.value);
        } else {
            params.delete("filterRole");
        }

        params.delete("page");

        window.location.search = params.toString();
    }

    let searchTimer;
    if (searchInput) {
        searchInput.addEventListener("keyup", function () {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(applyFilters, 400); // ⏳ 400ms debounce
        });
    }

    if (sortBy) {
        sortBy.addEventListener("change", applyFilters);
    }

    if (filterRole) { // ✨ tambahan
        filterRole.addEventListener("change", applyFilters);
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
