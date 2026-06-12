@php
    $userReview = $userReview ?? null;

    $prefillRating = $userReview->rating ?? '';
    $prefillText   = $userReview->feedback ?? '';
    $prefillImage  = $userReview && $userReview->image 
                     ? asset('storage/' . $userReview->image) 
                     : null;
@endphp

<div id="reviewModal" class="modal hidden">
    <div class="modal-content">
        <div class="modal-header">
            <h2>How Was Your Experience?</h2>
            <p>Your feedback help us to improve</p>
        </div>

        <form id="reviewForm" action="{{ route('reviews.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="wisata_id" value="{{ $place->id__tmpt }}">
            
            <!-- Rating -->
            <div class="rating-section">
                <div class="star-rating" id="starRating">
                    @for ($i = 1; $i <= 5; $i++)
                        <i data-feather="star" class="star" data-value="{{ $i }}"></i>
                    @endfor
                </div>
                <input type="hidden" name="rating" id="ratingInput" value="{{ $prefillRating }}">
            </div>

            <!-- Review Text -->
            <div class="form-group">
                <textarea name="review_text" rows="3" maxlength="500" placeholder="Tulis review di sini...">{{ $prefillText }}</textarea>
            </div>

            <!-- Upload Gambar -->
            <div class="form-group">
                <div id="dropArea" class="drop-area">
                    <i data-feather="image" class="upload-icon"></i>
                    <p>Drag & drop gambar di sini<br>atau klik untuk pilih file<br>(Opsional)</p>
                    <input type="file" name="review_image" id="review_image" accept="image/*" hidden>
                </div>
                <small id="imageRequired" class="text-red-500 hidden">
                    Gambar wajib diunggah jika rating 3 bintang atau kurang.
                </small>
                <div id="imagePreviewContainer" class="{{ $prefillImage ? '' : 'hidden' }} mt-2">
                    <img id="imagePreview" src="{{ $prefillImage ?? '#' }}" alt="Preview" class="rounded-md" style="max-width: 120px;">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" id="cancelReview" class="btn-cancel">Cancel</button>
                <button type="submit" id="submitReview" class="btn-submit" {{ $prefillRating ? '' : 'disabled' }}>Submit</button>
            </div>
        </form>
    </div>
</div>

@if(session('success'))
<div id="popup" class="popup">
    <div class="popup-content">
        <h2>{{ session('success') }}</h2>
        <div style="margin-top: 20px; display: flex; gap: 10px; justify-content: center;">
            <a href="" class="btn">Selesai</a>
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
        z-index: 99999999999999999999;
    }

    .popup h2 {
        color : #1f1f1f
    }
    .btn {
    background: #710eff;
    color: #fff;
    padding: 8px 12px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 13px;
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

<style>
/* Modal Base */
.modal {
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    display: flex; align-items: center; justify-content: center;
    background: rgba(0,0,0,0.5);
    z-index: 9999999999999999999999;

}
.modal.hidden { display: none; }

.modal-content {
    background: white;
    padding: 20px;
    border-radius: 16px;
    width: 420px;
    max-width: 95%;
    box-shadow: 0 8px 24px rgba(0,0,0,0.2);
}

.modal-header {
    display: flex;
    justify-content: center;
    background: transparent !important;
    color: #1f1f1f;
    flex-direction: column;
}

.form-group {
    margin: 12px 0;
    display: flex;
    flex-direction: column;
}

textarea {
    min-height: 80px;
    padding: 8px;
    border-radius: 8px;
    border: 1px solid #ccc;
}

.star {
    width: 28px;
    height: 28px;
    cursor: pointer;
    stroke: #aaa;
    transition: stroke 0.2s ease-in-out, fill 0.2s ease-in-out;
}
.star.selected, .star.hover {
    stroke: #FFB400;
    fill: #FFB400;
}

.btn-cancel, .btn-submit {
    padding: 8px 16px;
    border-radius: 8px;
    cursor: pointer;
    border: none;
}
.btn-cancel { background: #ccc; }
.btn-submit { background: #4f46e5; color: white; }

.star-rating {
    display: flex;
    cursor: pointer;
    flex-wrap: wrap;
    justify-content: space-evenly;
}
.star-rating .star {
    stroke: #fbbc04;
    fill: none;
}
.star-rating .star.selected {
    fill: #fbbc04;
}

/* Drag & Drop Style */
.drop-area {
    border: 2px dashed #d3d3d3;
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    transition: border-color 0.3s ease, background 0.3s ease;
    background: #f9f9ff;
    color : #1f1f1f;
}
.drop-area:hover {
    border-color: #4f46e5;
    background: #f3f3ff;
}
.drop-area.dragover {
    border-color: #4f46e5;
    background: #eef2ff;
}
.upload-icon {
    width: 40px;
    height: 40px;
    color: #6b6b6b;
    margin-bottom: 8px;
}
#imagePreview {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 4px;
}

.navbar {
    transition: opacity 0.3s ease;
}
.navbar.hidden {
    transition: opacity 0.3s ease;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    feather.replace(); // ✅ Pastikan ikon Feather sudah diganti sebelum pasang listener

    const navbar = document.querySelector('nav.navbar');
    const reviewModal = document.getElementById('reviewModal');
    const ratingInput = document.getElementById('ratingInput');
    const submitBtn = document.getElementById('submitReview');
    const reviewImage = document.getElementById('review_image');
    const imageRequired = document.getElementById('imageRequired');
    const imagePreviewContainer = document.getElementById('imagePreviewContainer');
    const imagePreview = document.getElementById('imagePreview');
    const dropArea = document.getElementById('dropArea');
    const stars = document.querySelectorAll('#starRating .star');

    let currentRating = parseInt(ratingInput.value) || 0;

    function highlightStars(rating) {
        stars.forEach(s => s.classList.remove('selected'));
        stars.forEach(s => {
            if (parseInt(s.dataset.value) <= rating) {
                s.classList.add('selected');
            }
        });
    }

    highlightStars(currentRating);

    // Pasang listener klik bintang
    stars.forEach(star => {
        star.addEventListener('click', function () {
            currentRating = parseInt(this.dataset.value);
            ratingInput.value = currentRating;
            highlightStars(currentRating);
            checkRequirements();
        });
    });

    function checkRequirements() {
        if (currentRating === 0) {
            submitBtn.disabled = true;
            return;
        }
        if (currentRating <= 3 && !reviewImage.files.length && !imagePreview.src.includes('data:image')) {
            imageRequired.classList.remove('hidden');
            submitBtn.disabled = true;
        } else {
            imageRequired.classList.add('hidden');
            submitBtn.disabled = false;
        }
    }

    checkRequirements();

    // Drag & Drop behavior
    dropArea.addEventListener('click', () => reviewImage.click());
    dropArea.addEventListener('dragover', (e) => { e.preventDefault(); dropArea.classList.add('dragover'); });
    dropArea.addEventListener('dragleave', () => dropArea.classList.remove('dragover'));
    dropArea.addEventListener('drop', (e) => {
        e.preventDefault();
        dropArea.classList.remove('dragover');
        if (e.dataTransfer.files.length) {
            reviewImage.files = e.dataTransfer.files;
            previewImage(reviewImage.files[0]);
            checkRequirements();
        }
    });

    reviewImage.addEventListener('change', function () {
        if (this.files && this.files[0]) previewImage(this.files[0]);
        checkRequirements();
    });

    function previewImage(file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            imagePreview.src = e.target.result;
            imagePreviewContainer.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }

    // Modal open/close
    const openBtn = document.getElementById('openReviewModal');
    if (openBtn) {
        openBtn.addEventListener('click', () => {
            reviewModal.classList.remove('hidden');
            if (navbar) navbar.classList.add('hidden');
            currentRating = parseInt(ratingInput.value) || 0;
            highlightStars(currentRating);
            checkRequirements();
        });
    }

    document.getElementById('cancelReview').addEventListener('click', () => {
        reviewModal.classList.add('hidden');
        if (navbar) navbar.classList.remove('hidden');

        reviewForm.reset();
        currentRating = 0;
        ratingInput.value = '';
        stars.forEach(s => s.classList.remove('selected'));
        imagePreviewContainer.classList.add('hidden');
        imagePreview.src = '#';
        submitBtn.disabled = true;
        imageRequired.classList.add('hidden');
    });
});
</script>