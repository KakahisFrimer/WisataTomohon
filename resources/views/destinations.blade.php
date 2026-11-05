@extends('layouts.app')

@section('content')
<section class="destination-page" style="padding:2rem 7%;">

    <div class="destination-header">
        <div class="search-bar">
            <form action="{{ route('all__des') }}" method="GET" class="search-form">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari destinasi wisata...">
            </form>
            <button type="submit" form="searchForm" class="search-btn">Search</button>
        </div>



        <div class="choose-category">
            <div class="category-list">
                <a href="{{ route('all__des') }}">
                    <button class="category-btn {{ request('category') == 'all' || !request('category') ? 'active' : '' }}">
                        Semua
                    </button>
                </a>
                @foreach($categories as $category)
                    <a href="{{ route('all__des', ['category' => $category]) }}">
                        <button class="category-btn {{ request('category') == $category ? 'active' : '' }}">
                            {{ $category }}
                        </button>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="container destination-list">
        @forelse($destinations as $item)
        
            <div class="item" data-category="{{ $item->category }}">
                <a href="{{ route('wisata.show', $item->slug) }}" class="destination-card">
                    <div class="card-img">
                        @php
                            $photos = json_decode($item->foto__tmpt, true) ?? [];
                            $photoPath = !empty($photos) ? 'storage/' . $photos[0] : 'storage/wisata/default-16x9.png';
                        @endphp
                        <img src="{{ asset($photoPath) }}" alt="{{ $item->name }}" loading="lazy">
                    </div>
                    <div class="card-content">
                        <h3>{{ $item->name }}</h3>
                        <p>{{ Str::limit($item->category, 60) }}</p>
                    </div>
                </a>
            </div>
        @empty
            <p class="empty__state">Tidak ada destinasi yang ditemukan.</p>
        @endforelse
    </div>

    
    
        <div class="pagination">
            {{ $destinations->onEachSide(0)->links('pagination::bootstrap-5') }}
        </div>
</section>

<footer class="footer">
    <p>&copy; {{ date('Y') }} WisataTomohon. All rights reserved. | Developed by
@fri_costan</p>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace(); // aktifkan Feather Icon
    });
</script>

<script>



document.addEventListener("DOMContentLoaded", () => {

    const items = document.querySelectorAll('.item');

    const observer = new IntersectionObserver(
        entries => {

            entries.forEach(entry => {

                if(entry.isIntersecting){
                    entry.target.classList.add('show');
                }

            });

        },
        {
            threshold: 0.1
        }
    );

    items.forEach(item => observer.observe(item));

});




document.addEventListener("DOMContentLoaded", () => {

    const images = document.querySelectorAll('.card-img img');

    images.forEach(img => {

        function finishLoading() {

            img.classList.add('loaded');

            const wrapper = img.closest('.card-img');

            if(wrapper){
                wrapper.classList.add('loaded');
            }

        }

        if(img.complete){
            finishLoading();
        } else {
            img.addEventListener('load', finishLoading);
        }

    });

});

</script>

<style>

  :root {
    --primary: #fdfdfd;
    --sec: #710eff;
    --txt: #1f1f1f;
    --muted: #6b6b6b;
    --card-bg: #ffffff;
    --radius: 12px;
    --shadow: 0 6px 18px rgba(34, 34, 34, 0.06);
    --gap: 18px;
}

.footer {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;

    color: #333;
    text-align: center;

    padding: 12px 20px;
    z-index: 9999;


}


.item {
    opacity: 0;
    transform: translateY(25px);
    transition:
        opacity .6s ease,
        transform .6s ease;
}

.item.show {
    opacity: 1;
    transform: translateY(0);
}



.card-img img {
    opacity: 0;
    transition: opacity .5s ease;
}

.card-img img.loaded {
    opacity: 1;
}




.card-img {
    position: relative;
    overflow: hidden;
}

.card-img::before {
    content: "";
    position: absolute;
    inset: 0;

    background: linear-gradient(
        90deg,
        #eeeeee 25%,
        #f8f8f8 50%,
        #eeeeee 75%
    );

    background-size: 200% 100%;

    animation: skeleton-loading 1.2s infinite;

    z-index: 0;
}

.card-img img {
    position: relative;
    z-index: 1;
}

.card-img.loaded::before {
    display: none;
}

@keyframes skeleton-loading {
    0% {
        background-position: 200% 0;
    }
    100% {
        background-position: -200% 0;
    }
}



.destination-list {
    content-visibility: auto;
    contain-intrinsic-size: 1000px;
}


.destination-header {
  display: flex;
  flex-direction: column;
  gap: 20px;
  margin-bottom: 25px;
}

.search-bar {
  display: flex;
  align-items: center;
  gap: 10px;
  width: 100%;
  max-width: 100%;
  margin: 0 auto;
}

.search-form {
  flex: 1;
  display: flex;
  align-items: center;
  background: #fff;
  padding: 8px 12px;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.search-bar i {
  color: #666;
  margin-right: 8px;
  flex-shrink: 0;
}

.search-bar input {
  border: none;
  outline: none;
  flex: 1;
  font-size: 0.95rem;
  padding: 6px 8px;
  width: 100%;
}


.search-btn {
  background-color: var(--sec);
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: 8px 16px;
  font-size: 0.9rem;
  cursor: pointer;
  transition: background 0.2s ease;
  flex-shrink: 0;
}

.search-btn:hover {
  background-color: var(--sec);
}

@media (max-width: 600px) {
  .search-bar {
    flex-direction: column;
    align-items: stretch;
    max-width: 100%;
  }

  .search-btn {
    width: 100%;
    margin-top: 8px;
  }
}


.container {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  grid-auto-rows: 250px;
  gap: 20px;
  grid-auto-flow: dense;
}

.item:nth-child(1),
.item:nth-child(5),
.item:nth-child(7) {
  grid-column: span 2; 
}

.item:nth-child(2),
.item:nth-child(8) {
  grid-column: span 1;
}

.item:nth-child(3),
.item:nth-child(6),
.item:nth-child(9) {
  grid-column: span 1;
}

.item:nth-child(4) {
  grid-column: span 1;
}

.choose-category h2 {
  color : var(--txt);
}

.destination-card {
  display: flex;
  flex-direction: column;
  background: #fff;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
  height: 100%;
  text-decoration: none;
  color: inherit;
  position: relative;
}

.destination-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
}

.card-img {
  position: relative;
  width: 100%;
  height: 200px;
  overflow: hidden;
}

.card-img img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.destination-card:hover .card-img img {
  transform: scale(1.07);
}

.card-img::after {
  content: "";
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(0,0,0,0.45), rgba(0,0,0,0.05));
  opacity: 0.6;
  transition: opacity 0.3s ease;
}

.destination-card:hover .card-img::after {
  opacity: 0.8;
}

.card-content {
  padding: 15px 18px;
}

.card-content h3 {
  font-size: 1.05rem;
  font-weight: 600;
  color: #1d1d1d;
  margin-bottom: 6px;
  transition: color 0.3s ease;
}

.destination-card:hover .card-content h3 {
  color: var(--sec);
}

.card-content p {
  font-size: 0.9rem;
  color: #6b6b6b;
  line-height: 1.4;
}

@media (max-width: 768px) {
  .card-img {
    height: 180px;
  }
  .card-content {
    padding: 12px 15px;
  }
}


.choose-category {
  margin-bottom: 10px;
  display: flex;
  justify-content: space-around;
}

.category-list {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
  margin-top: 5px;
}

.category-btn {
  padding: 8px 15px;
  background: var(--muted);
  color: #fff;
  border: none;
  border-radius: 20px;
  cursor: pointer;
  font-size: 0.9rem;
  transition: background 0.3s ease;
}

.category-btn.active,
.category-btn:hover {
  background: var(--sec);
}

.destination-page .pagination {
    margin-top: 8px;
    display: flex;
    justify-content: center;
    align-items: center;
    color : var(--txt);
    gap: 6px;
    padding-top : 15px;
}

.destination-page .pagination nav {
    display: flex;
    gap: 4px;
}
.destination-page .pagination .page-link {
    padding: 6px 10px;
    border-radius: 6px;
    border: 1px solid #ddd;
    background: #fff;
    font-size: 13px;
    text-decoration: none;
    color: var(--txt);
}
.destination-page .pagination .page-link:hover {
    background: var(--sec);
    color: #fff;
}
.destination-page .pagination .active .page-link {
    background: var(--sec);
    color: #fff;
    border-color: var(--sec);
}

.destination-page .pagination ul {
    display: flex;
    gap: 6px;
    list-style: none; /* hilangkan bullet hitam */
    padding: 0;
    margin: 0;
    justify-content: center;
    align-items: center;
    padding-top : 20px;
}

.destination-page .pagination li {
    list-style: none;
}

.destination-page .pagination a,
.destination-page .pagination span {
    padding: 6px 10px;
    border-radius: 6px;
    border: 1px solid #ddd;
    background: #fff;
    font-size: 13px;
    text-decoration: none;
    color: var(--txt);
}

.destination-page .pagination a:hover {
    background: var(--sec);
    color: #fff;
}

.destination-page .pagination .active span {
    background: var(--sec);
    color: #fff;
    border-color: var(--sec);
}

/* === Responsif Grid Destination === */
@media (max-width: 1200px) {
  .container {
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
  }
}

@media (max-width: 900px) {
  .container {
    grid-template-columns: repeat(2, 1fr);
    gap: 14px;
  }
}

@media (max-width: 600px) {
  .container {
    grid-template-columns: 1fr;
    grid-auto-rows: auto;
    gap: 16px;
  }

  .item:nth-child(1),
  .item:nth-child(2),
  .item:nth-child(3),
  .item:nth-child(4),
  .item:nth-child(5),
  .item:nth-child(6),
  .item:nth-child(7),
  .item:nth-child(8),
  .item:nth-child(9) {
    grid-column: span 1;
  }
}

@media (max-width: 768px) {
  .destination-header {
    gap: 16px;
    margin-bottom: 20px;
  }

  .choose-category {
    text-align: center;
  }

  .category-list {
    justify-content: center;
  }

  .search-bar {
    flex-direction: column;
    align-items: stretch;
  }

  .search-form {
    width: 100%;
  }

  .search-btn {
    width: 100%;
    margin-top: 10px;
  }
}

@media (max-width: 480px) {
  section.destination-page {
    padding: 1.5rem 5%;
  }

  .card-content h3 {
    font-size: 1rem;
  }

  .card-content p {
    font-size: 0.85rem;
  }

  .category-btn {
    font-size: 0.85rem;
    padding: 6px 12px;
  }

  .search-bar {
    padding: 0;
  }
}
.destination-page .pagination {
    margin-top: 8px;
    display: flex;
    justify-content: center;
    align-items: center;
    color : var(--txt);
    gap: 6px;
    padding-top : 15px;
}

.destination-page .pagination nav {
    display: flex;
    gap: 4px;
}
.destination-page .pagination .page-link {
    padding: 6px 10px;
    border-radius: 6px;
    border: 1px solid #ddd;
    background: #fff;
    font-size: 13px;
    text-decoration: none;
    color: var(--txt);
}
.destination-page .pagination .page-link:hover {
    background: var(--sec);
    color: #fff;
}
.destination-page .pagination .active .page-link {
    background: var(--sec);
    color: #fff;
    border-color: var(--sec);
}

.destination-page .pagination ul {
    display: flex;
    gap: 6px;
    list-style: none; /* hilangkan bullet hitam */
    padding: 0;
    margin: 0;
    justify-content: center;
    align-items: center;
    padding-top : 20px;
}

.destination-page .pagination li {
    list-style: none;
}

.destination-page .pagination a,
.destination-page .pagination span {
    padding: 6px 10px;
    border-radius: 6px;
    border: 1px solid #ddd;
    background: #fff;
    font-size: 13px;
    text-decoration: none;
    color: var(--txt);
}

.destination-page .pagination a:hover {
    background: var(--sec);
    color: #fff;
}

.destination-page .pagination .active span {
    background: var(--sec);
    color: #fff;
    border-color: var(--sec);
}

@media (max-width: 768px) {

    .destination-page .pagination nav {
        width: 100%;
    }

    .destination-page .pagination ul {
        flex-wrap: wrap;
        justify-content: center;
        gap: 4px;
        padding-top: 10px;
    }

    .destination-page .pagination a,
    .destination-page .pagination span {
        padding: 4px 8px;
        font-size: 12px;
    }
}
</style>
@endsection
