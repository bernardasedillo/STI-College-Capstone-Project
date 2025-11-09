document.addEventListener("DOMContentLoaded", () => {
    const galleryItems = document.querySelectorAll(".gallery-item");
    const filterButtons = document.querySelectorAll(".filter-btn");

    // CATEGORY FILTER
    filterButtons.forEach(btn => {
        btn.addEventListener("click", () => {
            document.querySelector(".filter-btn.active").classList.remove("active");
            btn.classList.add("active");
            const category = btn.getAttribute("data-category");

            galleryItems.forEach(item => {
                if (category === "all" || item.dataset.category === category) {
                    item.style.display = "block";
                } else {
                    item.style.display = "none";
                }
            });
        });
    });

    // LIGHTBOX FUNCTIONALITY
    const lightbox = document.getElementById("lightbox");
    const lightboxImage = document.getElementById("lightbox-image");
    const lightboxCaption = document.getElementById("lightbox-caption");
    const closeBtn = document.querySelector(".lightbox__close");
    const prevBtn = document.querySelector(".lightbox__prev");
    const nextBtn = document.querySelector(".lightbox__next");

    let currentIndex = 0;
    const visibleItems = () => [...galleryItems].filter(item => item.style.display !== "none");

    function openLightbox(index) {
        const items = visibleItems();
        if (items.length === 0) return;
        currentIndex = index;
        const item = items[currentIndex];
        const highResSrc = item.dataset.highres || item.querySelector("img").src;
        lightboxImage.src = highResSrc;
        lightboxCaption.textContent = item.dataset.caption;
        lightbox.classList.add("active");
        lightbox.setAttribute("aria-hidden", "false");
        lightboxImage.classList.remove("zoomed"); // Reset zoom on new image
    }

    galleryItems.forEach((item, index) => {
        item.addEventListener("click", () => {
            const items = visibleItems();
            const visibleIndex = items.indexOf(item);
            openLightbox(visibleIndex);
        });
    });

    closeBtn.addEventListener("click", () => {
        lightbox.classList.remove("active");
        lightbox.setAttribute("aria-hidden", "true");
    });

    lightbox.addEventListener("click", (e) => {
        if (e.target === lightbox) {
            lightbox.classList.remove("active");
            lightbox.setAttribute("aria-hidden", "true");
        }
    });

    lightboxImage.addEventListener("click", (e) => {
        e.stopPropagation(); // Prevent closing lightbox when clicking image
        lightboxImage.classList.toggle("zoomed");
    });

    prevBtn.addEventListener("click", () => {
        const items = visibleItems();
        currentIndex = (currentIndex - 1 + items.length) % items.length;
        openLightbox(currentIndex);
    });

    nextBtn.addEventListener("click", () => {
        const items = visibleItems();
        currentIndex = (currentIndex + 1) % items.length;
        openLightbox(currentIndex);
    });
});
