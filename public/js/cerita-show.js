document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.querySelector(".bi-list");
    const sidebar = document.querySelector(".sidebar");

    if (toggleBtn) {
        toggleBtn.addEventListener("click", function () {
            sidebar.classList.toggle("show");
        });
    }
});

document.getElementById("menu-toggle")?.addEventListener("click", () => {
    document.querySelector(".sidebar").classList.toggle("show");
});

// Placeholder jika nanti mau ditambah fitur interaktif
console.log("Cerita page loaded");
