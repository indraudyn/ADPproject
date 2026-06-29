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

    // ============================================
    // STORY PAGINATION SYSTEM
    // ============================================
    const storyTextBody = document.querySelector(".story-text-body");
    const btnModeSplit = document.getElementById("btn-mode-split");
    const btnModeFull = document.getElementById("btn-mode-full");
    const paginationContainer = document.getElementById("story-pagination");
    const pagePrevLi = document.getElementById("page-prev-li");
    const pageNextLi = document.getElementById("page-next-li");
    const btnPagePrev = document.getElementById("btn-page-prev");
    const btnPageNext = document.getElementById("btn-page-next");
    const pageIndicator = document.getElementById("page-indicator");

    if (storyTextBody && btnModeSplit && btnModeFull && paginationContainer) {
        // Collect all block elements
        const allChildren = Array.from(storyTextBody.children);
        
        // If there are no block elements (raw text), split by paragraphs manually
        let paragraphs = [];
        if (allChildren.length === 0) {
            // Text is raw, let's split by double newline
            const rawText = storyTextBody.innerHTML;
            const segments = rawText.split(/\n\s*\n|<br\s*\/?>\s*<br\s*\/?>/i);
            storyTextBody.innerHTML = "";
            segments.forEach(seg => {
                if (seg.trim()) {
                    const p = document.createElement("p");
                    p.innerHTML = seg;
                    storyTextBody.appendChild(p);
                    paragraphs.push(p);
                }
            });
        } else {
            paragraphs = allChildren;
        }

        // Detect page breaks or split automatically
        let pages = [];
        let currentPage = [];

        function isPageBreakElement(el) {
            if (el.tagName === "HR") return true;
            const text = el.textContent || "";
            return text.includes("[pagebreak]") || text.includes("[halaman]");
        }

        paragraphs.forEach(child => {
            if (isPageBreakElement(child)) {
                if (currentPage.length > 0) {
                    pages.push(currentPage);
                    currentPage = [];
                }
            } else {
                currentPage.push(child);
            }
        });
        if (currentPage.length > 0) {
            pages.push(currentPage);
        }

        // If no explicit page breaks and enough content, auto split by 5 paragraphs
        const autoSplitLimit = 5;
        if (pages.length <= 1 && paragraphs.length > autoSplitLimit) {
            pages = [];
            currentPage = [];
            let count = 0;
            paragraphs.forEach(child => {
                currentPage.push(child);
                count++;
                if (count >= autoSplitLimit) {
                    pages.push(currentPage);
                    currentPage = [];
                    count = 0;
                }
            });
            if (currentPage.length > 0) {
                pages.push(currentPage);
            }
        }

        let currentPageIndex = 0;
        let readingMode = localStorage.getItem("reading_mode") || "split"; // default to split

        // Create page wrapper div elements to easily toggle visibility & apply transition styles
        const pageElements = [];
        storyTextBody.innerHTML = ""; // clear original body

        pages.forEach((pageNodes, idx) => {
            const pageDiv = document.createElement("div");
            pageDiv.className = "story-page";
            pageDiv.id = `story-page-${idx}`;
            pageNodes.forEach(node => {
                // clone node or append directly
                pageDiv.appendChild(node.cloneNode(true));
            });
            storyTextBody.appendChild(pageDiv);
            pageElements.push(pageDiv);
        });

        function renderPaginationControls() {
            // Clear dynamic pages (keep prev and next items)
            const listItems = Array.from(paginationContainer.querySelectorAll(".page-item"));
            listItems.forEach(item => {
                if (item.id !== "page-prev-li" && item.id !== "page-next-li") {
                    item.remove();
                }
            });

            // Add numbered pages
            pages.forEach((_, idx) => {
                const li = document.createElement("li");
                li.className = `page-item ${idx === currentPageIndex ? 'active' : ''}`;
                
                const btn = document.createElement("button");
                btn.className = "page-link";
                btn.innerText = idx + 1;
                btn.addEventListener("click", () => showPage(idx));
                
                li.appendChild(btn);
                // Insert before Next button
                pageNextLi.parentNode.insertBefore(li, pageNextLi);
            });

            // Update prev/next disabled state
            if (currentPageIndex === 0) {
                pagePrevLi.classList.add("disabled");
            } else {
                pagePrevLi.classList.remove("disabled");
            }

            if (currentPageIndex === pages.length - 1) {
                pageNextLi.classList.add("disabled");
            } else {
                pageNextLi.classList.remove("disabled");
            }

            // Update page indicator text
            pageIndicator.innerText = `Halaman ${currentPageIndex + 1} dari ${pages.length}`;
        }

        function showPage(pageIdx, scroll = true) {
            if (pageIdx < 0 || pageIdx >= pages.length) return;

            const currentVisiblePage = pageElements[currentPageIndex];
            const targetPage = pageElements[pageIdx];

            if (scroll && currentPageIndex !== pageIdx) {
                // Scroll smoothly to top of the content card
                const contentCard = document.querySelector(".story-content-card");
                if (contentCard) {
                    contentCard.scrollIntoView({ behavior: "smooth", block: "start" });
                }
            }

            if (currentVisiblePage && currentVisiblePage !== targetPage) {
                currentVisiblePage.classList.add("fade-out");
                setTimeout(() => {
                    pageElements.forEach((el, index) => {
                        el.style.display = index === pageIdx ? "block" : "none";
                    });
                    currentPageIndex = pageIdx;
                    targetPage.classList.add("fade-out");
                    // Force reflow
                    targetPage.offsetHeight;
                    targetPage.classList.remove("fade-out");
                    renderPaginationControls();
                }, 200); // matches half of transition duration
            } else {
                pageElements.forEach((el, index) => {
                    el.style.display = index === pageIdx ? "block" : "none";
                });
                currentPageIndex = pageIdx;
                if (targetPage) targetPage.classList.remove("fade-out");
                renderPaginationControls();
            }
        }

        function setReadingMode(mode) {
            readingMode = mode;
            localStorage.setItem("reading_mode", mode);

            if (mode === "split" && pages.length > 1) {
                btnModeSplit.classList.add("active");
                btnModeFull.classList.remove("active");
                paginationContainer.style.display = "flex";
                
                // Show current active page, hide others
                showPage(currentPageIndex, false);
            } else {
                btnModeSplit.classList.remove("active");
                btnModeFull.classList.add("active");
                paginationContainer.style.display = "none";

                // Show all pages as standard reading
                pageElements.forEach(el => {
                    el.style.display = "block";
                    el.classList.remove("fade-out");
                });
            }
        }

        // Add event listeners to static pagination controls
        btnPagePrev.addEventListener("click", (e) => {
            e.preventDefault();
            showPage(currentPageIndex - 1);
        });

        btnPageNext.addEventListener("click", (e) => {
            e.preventDefault();
            showPage(currentPageIndex + 1);
        });

        btnModeSplit.addEventListener("click", () => setReadingMode("split"));
        btnModeFull.addEventListener("click", () => setReadingMode("full"));

        // Initialize view based on stored preference
        // If there's only 1 page, hide reading mode selector & pagination completely
        if (pages.length <= 1) {
            document.querySelector(".reading-mode-selector").style.display = "none";
            paginationContainer.style.display = "none";
            pageElements.forEach(el => el.style.display = "block");
        } else {
            setReadingMode(readingMode);
        }
    }
});

document.getElementById("menu-toggle")?.addEventListener("click", () => {
    document.querySelector(".sidebar").classList.toggle("show");
});

// Placeholder jika nanti mau ditambah fitur interaktif
console.log("Cerita page loaded");
