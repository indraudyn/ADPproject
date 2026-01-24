document.getElementById("photo")?.addEventListener("change", function (e) {
    const reader = new FileReader();
    reader.onload = () => {
        const img = document.getElementById("preview");
        if (img) {
            img.src = reader.result;
        }
    };
    reader.readAsDataURL(e.target.files[0]);
});

document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.getElementById("menu-toggle");
    const sidebar = document.getElementById("sidebar");

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener("click", function () {
            sidebar.classList.toggle("closed");
        });
    }
});
