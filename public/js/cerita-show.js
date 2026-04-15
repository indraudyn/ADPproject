document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.querySelector(".bi-list");
    const sidebar = document.querySelector(".sidebar");

    if (toggleBtn) {
        toggleBtn.addEventListener("click", function () {
            sidebar.classList.toggle("show");
        });
    }

    // Related Stories Carousel Navigation
    const scrollContainer = document.querySelector(".related-carousel-scroll");
    const prevBtn = document.querySelector(".carousel-nav.prev");
    const nextBtn = document.querySelector(".carousel-nav.next");

    if (scrollContainer && prevBtn && nextBtn) {
        const itemWidth = 370; // card width + gap

        prevBtn.addEventListener("click", () => {
            scrollContainer.scrollBy({ left: -itemWidth, behavior: "smooth" });
        });

        nextBtn.addEventListener("click", () => {
            scrollContainer.scrollBy({ left: itemWidth, behavior: "smooth" });
        });

        const checkScroll = () => {
            if (scrollContainer.scrollLeft <= 5) {
                prevBtn.style.opacity = "0.3";
                prevBtn.style.pointerEvents = "none";
            } else {
                prevBtn.style.opacity = "1";
                prevBtn.style.pointerEvents = "auto";
            }

            if (scrollContainer.scrollLeft >= (scrollContainer.scrollWidth - scrollContainer.clientWidth - 5)) {
                nextBtn.style.opacity = "0.3";
                nextBtn.style.pointerEvents = "none";
            } else {
                nextBtn.style.opacity = "1";
                nextBtn.style.pointerEvents = "auto";
            }
        };

        scrollContainer.addEventListener("scroll", checkScroll);
        window.addEventListener("resize", checkScroll);
        setTimeout(checkScroll, 100); // Small delay for rendering
    }
});

document.getElementById("menu-toggle")?.addEventListener("click", () => {
    document.querySelector(".sidebar").classList.toggle("show");
});

// Placeholder jika nanti mau ditambah fitur interaktif
console.log("Cerita page loaded");
