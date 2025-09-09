@extends('layouts.admin')
@section('dashboard')
<link rel="stylesheet" href="{{ asset('css/admin-style.css') }}">
<title>Dashboard</title>
<header class="main-header">
    <div class="greeting">
        <h1>Halo, {{ Auth::user()->name ?? 'Admin' }}!</h1>
        <p class="sub">Selamat datang di dashboard pariwisata Kota Tomohon</p>
    </div>
    <div class="profile-area">
        @auth
            <div class="user-menu" id="userMenu">
                <div class="user-profile" id="userProfile">
                    <div class="user-avatar" style="background-color: {{ Auth::user()->avatar_color ?? '#710EFF' }};">
                        <span class="avatar-initials">{{ Auth::user()->initials ?? substr(Auth::user()->name,0,2) }}</span>
                    </div>
                </div>
            </div>
        @else
            <a href="{{ route('login') }}"><i data-feather="user"></i></a>
        @endauth
    </div>
</header>
<section class="grid">
    <div class="card stats-card">
        <div class="card-header">
            <h3>Total Objek Wisata</h3>
            <div class="big-number">{{ $totalWisata }}</div>
        </div>
        <canvas id="categoryChart" width="400" height="200" style="padding-bottom:40px;"></canvas>
    </div>
    <div class="card map-card">
        <div class="card-header">
            <h3>Titik Lokasi Wisata</h3>
        </div>
        <div id="map" style="height:420px;"></div>
    </div>
    <div class="card notif-card">
        <div class="card-header">
            <h3>Notifikasi Penting</h3>
        </div>
        <ul class="notif-list">
            @forelse($notifications ?? [] as $n)
                <li>
                    <strong>{{ $n->nama__tmpt }}</strong>
                    <span class="warn">@if(!$n->foto__tmpt) tanpa foto @endif @if(!$n->keterangan) tanpa deskripsi @endif</span>
                </li>
            @empty
                <li>Tidak ada notifikasi.</li>
            @endforelse
        </ul>
    </div>
    <div class="card latest-card">
        <div class="card-header">
            <h3>Destinasi Terbaru</h3>
            <a href="{{ Route::has('admin.places.index') ? route('admin.places.index') : '#' }}" class="link">Lihat semua</a>
        </div>
        <div class="latest-list">
            @foreach($latestPlaces ?? [] as $place)
                <div class="latest-item">
                    <img src="{{ $place->foto__tmpt ? asset('storage/' . $place->foto__tmpt) : asset('images/placeholder.png') }}" alt="" />
                    <div class="meta">
                        <div class="title">{{ $place->nama__tmpt }}</div>
                        <div class="sub">{{ $place->category }} • {{ $place->alamat }}</div>
                    </div>
                    <a href="{{ Route::has('admin.places.edit') ? route('admin.places.edit', $place->id__tmpt) : '#' }}" class="btn small">Edit</a>
                </div>
            @endforeach
        </div>
    </div>
    <div class="card calendar-card">
        <div class="card-header">
            <h3>Kalender Event</h3>
            <a href="{{ Route::has('admin.events.create') ? route('admin.events.create') : '#' }}" class="btn">+ Tambah Event</a>
        </div>
        <div class="custom-calendar">
            <div class="calendar-header">
                <button id="prevMonth" aria-label="Previous month">&lt;</button>
                <div id="monthYear"></div>
                <button id="nextMonth" aria-label="Next month">&gt;</button>
            </div>
            <div class="calendar-grid" id="calendarGrid"></div>
            <div id="calendarTooltip" class="calendar-tooltip"></div>
        </div>
        <div class="upcoming-event">
            <div class="card-header">
                <h4>Event Mendatang</h4>
            </div>
            <ul>
                @forelse($upcomingEvents ?? [] as $ev)
                    <li>
                        <strong>{{ $ev->title }}</strong> • {{ \Carbon\Carbon::parse($ev->start_date)->format('d M Y') }}
                        @if($ev->category) <span class="cat">({{ $ev->category }})</span> @endif
                    </li>
                @empty
                @endforelse
            </ul>
        </div>
    </div>
</section>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const map = L.map('map').setView([1.327, 124.842], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19
    }).addTo(map);
    const iconsBase = @json(asset('images/icons')) + '/';
    const iconMap = {
        'Wisata Alam': L.icon({ iconUrl: iconsBase + 'alam.png', iconSize: [28, 40], iconAnchor: [14, 40] }),
        'Wisata Buatan': L.icon({ iconUrl: iconsBase + 'buatan.png', iconSize: [28, 40], iconAnchor: [14, 40] }),
        'Wisata Budaya': L.icon({ iconUrl: iconsBase + 'budaya.png', iconSize: [28, 40], iconAnchor: [14, 40] }),
        'Kuliner': L.icon({ iconUrl: iconsBase + 'kuliner.png', iconSize: [28, 40], iconAnchor: [14, 40] }),
        'Akomodasi': L.icon({ iconUrl: iconsBase + 'akomodasi.png', iconSize: [28, 40], iconAnchor: [14, 40] }),
        'default': L.icon({ iconUrl: iconsBase + 'default.png', iconSize: [28, 40], iconAnchor: [14, 40] })
    };
    const places = @json($places ?? []);
    places.forEach(p => {
        if (!p.latitude || !p.longitude) return;
        const icon = iconMap[p.category] || iconMap['default'];
        const foto = p.foto__tmpt
            ? @json(url('storage')) + '/' + p.foto__tmpt
            : @json(asset('images/placeholder.png'));
        const popupContent = `
            <strong>${p.name}</strong><br>
            ${p.alamat}<br>
            Jenis: ${p.category}<br>
            <img src="${foto}" style="width:120px;height:70px;object-fit:cover;margin-top:6px;" />
        `;
        L.marker([p.latitude, p.longitude], { icon }).addTo(map)
            .bindPopup(popupContent);
    });
    const ctx = document.getElementById('categoryChart').getContext('2d');
    const categories = @json($categories->pluck('category') ?? []);
    const counts = @json($categories->pluck('total') ?? []);
    const baseColors = [
        '#710EFF',
        '#8A33FF',
        '#9F5BFF',
        '#B47AFF',
        '#C8A3FF',
        '#E0C9FF'
    ];
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: categories,
            datasets: [{
                label: 'Jumlah Objek Wisata',
                data: counts,
                backgroundColor: baseColors,
                borderColor: baseColors.map(color => color.replace('FF', 'CC')),
                borderWidth: 2,
                borderRadius: 8,
                hoverBackgroundColor: baseColors.map(color => color.replace('FF', 'CC'))
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: {
                    bottom: 30
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        font: {
                            size: 14,
                            weight: 'bold'
                        },
                        color: '#333'
                    }
                },
                tooltip: {
                    backgroundColor: '#fff',
                    titleColor: '#000',
                    bodyColor: '#000',
                    borderColor: '#ccc',
                    borderWidth: 1
                }
            },
            scales: {
                x: {
                    ticks: {
                        font: {
                            size: 12
                        }
                    },
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                        font: {
                            size: 12
                        }
                    },
                    grid: {
                        color: '#eee'
                    }
                }
            }
        }
    });
    const events = @json($allEvents);
    let currentDate = new Date();
    const monthYearEl = document.getElementById("monthYear");
    const calendarGrid = document.getElementById("calendarGrid");
    const tooltip = document.getElementById("calendarTooltip");
    function renderCalendar(date) {
        calendarGrid.innerHTML = "";
        const year = date.getFullYear();
        const month = date.getMonth();
        const firstDay = new Date(year, month, 1).getDay();
        const lastDate = new Date(year, month + 1, 0).getDate();
        monthYearEl.textContent = date.toLocaleDateString("id-ID", { month: "long", year: "numeric" });
        const dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        dayNames.forEach(day => {
            const dayHeader = document.createElement('div');
            dayHeader.classList.add('calendar-day');
            dayHeader.style.fontWeight = '600';
            dayHeader.style.color = 'var(--muted)';
            dayHeader.style.cursor = 'default';
            dayHeader.textContent = day;
            calendarGrid.appendChild(dayHeader);
        });
        for (let i = 0; i < firstDay; i++) {
            const empty = document.createElement("div");
            empty.classList.add("calendar-day");
            empty.style.visibility = 'hidden';
            calendarGrid.appendChild(empty);
        }
        const today = new Date();
        for (let day = 1; day <= lastDate; day++) {
            const cell = document.createElement("div");
            cell.classList.add("calendar-day");
            const cellDate = new Date(year, month, day);
            if (
                cellDate.getDate() === today.getDate() &&
                cellDate.getMonth() === today.getMonth() &&
                cellDate.getFullYear() === today.getFullYear()
            ) {
                cell.classList.add("today");
            }
            let isEvent = false;
            let isPastEvent = false;
            let multiDay = false;
            events.forEach(event => {
                const start = new Date(event.start_date);
                const end = new Date(event.end_date);
                if (cellDate >= start && cellDate <= end) {
                    isEvent = true;
                    if (cellDate < today) isPastEvent = true;
                    if (end - start > 0) multiDay = true;
                }
            });
            if (isEvent) {
                cell.classList.add("event");
                if (isPastEvent) cell.classList.add("past-event");
                if (multiDay) cell.classList.add("multi-day");
            }
            cell.textContent = day;
            events.forEach(event => {
                const start = new Date(event.start_date);
                const end = new Date(event.end_date);
                if (cellDate >= start && cellDate <= end) {
                    cell.addEventListener("mouseenter", e => {
                        tooltip.style.display = "block";
                        tooltip.textContent = event.title;
                    });
                    cell.addEventListener("mouseleave", () => {
                        tooltip.style.display = "none";
                    });
                    cell.addEventListener("mousemove", e => {
                        tooltip.style.top = e.pageY + 10 + "px";
                        tooltip.style.left = e.pageX + 10 + "px";
                    });
                }
            });
            calendarGrid.appendChild(cell);
        }
    }
    document.getElementById("prevMonth").addEventListener("click", () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar(currentDate);
    });
    document.getElementById("nextMonth").addEventListener("click", () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar(currentDate);
    });
    renderCalendar(currentDate);
});
document.addEventListener('DOMContentLoaded', function () {
    const userMenu = document.getElementById('userMenu');
    const userProfile = document.getElementById('userProfile');
    if (userProfile) {
        userProfile.addEventListener('click', function (e) {
            e.stopPropagation();
            userMenu.classList.toggle('active');
        });
    }
    document.addEventListener('click', function () {
        if (userMenu) userMenu.classList.remove('active');
    });
});
</script>
<style>
.main-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 20px;
    background: #fff;
    border-radius: 12px;
    margin-bottom: 20px;
}
.main-header .greeting h1 {
    font-size: 20px;
    margin: 0;
}
.main-header .greeting .sub {
    font-size: 14px;
    color: #666;
    margin: 2px 0 0;
}
.profile-area {
    display: flex;
    align-items: center;
    gap: 12px;
}
.user-menu {
    position: relative;
    cursor: pointer;
    display : inline-block ;
}
.user-profile {
    display: flex;
    align-items: center;
    gap: 8px;
}
.user-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: bold;
    font-size: 14px;
}
.user-name {
    font-size: 14px;
    font-weight: 600;
    color: #333;
}

</style>
@endsection
