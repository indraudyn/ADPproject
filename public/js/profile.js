// Profile JS - Enhanced with Cropper.js and UI fixes
let cropper;
const photoInput = document.getElementById('photo');
const imageToCrop = document.getElementById('imageToCrop');
const cropModalElement = document.getElementById('cropModal');
const cropModal = new bootstrap.Modal(cropModalElement);
const cropButton = document.getElementById('cropButton');
const photoDataInput = document.getElementById('photoData');
const previewImg = document.getElementById('preview');
const placeholder = document.getElementById('placeholder');

// Sidebar toggle (global)
document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.getElementById("menu-toggle");
    const sidebar = document.getElementById("sidebar");

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener("click", function () {
            sidebar.classList.toggle("closed");
        });
    }
});

// Photo selection -> Open Cropper
photoInput?.addEventListener('change', function (e) {
    const files = e.target.files;
    if (files && files.length > 0) {
        const reader = new FileReader();
        reader.onload = function (event) {
            imageToCrop.src = event.target.result;
            cropModal.show();
        };
        reader.readAsDataURL(files[0]);
    }
});

// Initialize Cropper when modal shows
cropModalElement?.addEventListener('shown.bs.modal', function () {
    cropper = new Cropper(imageToCrop, {
        aspectRatio: 1,
        viewMode: 2,
        autoCropArea: 1,
        ready() {
            // Optional: UI tweaks when ready
        }
    });
});

// Clean up Cropper when modal hides
cropModalElement?.addEventListener('hidden.bs.modal', function () {
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
    // Clear input so same file can be selected again
    photoInput.value = '';
});

// Handle Crop Button Click
cropButton?.addEventListener('click', function () {
    if (cropper) {
        const canvas = cropper.getCroppedCanvas({
            width: 500,
            height: 500
        });

        // Set base64 data to hidden input
        const croppedData = canvas.toDataURL('image/jpeg', 0.9);
        photoDataInput.value = croppedData;

        // Update preview
        if (previewImg) {
            previewImg.src = croppedData;
            previewImg.style.display = 'block';
        }
        if (placeholder) {
            placeholder.style.display = 'none';
        }

        cropModal.hide();
    }
});
