document.addEventListener("DOMContentLoaded", () => {
    const carousels = document.querySelectorAll("[data-showcase-carousel]");

    carousels.forEach((carousel) => {
        const track = carousel.querySelector(".faculty-carousel-track");
        const viewport = carousel.querySelector(".faculty-carousel-viewport");
        const previousButton = carousel.querySelector(
            "[data-carousel-previous]",
        );
        const nextButton = carousel.querySelector("[data-carousel-next]");
        const currentLabel = carousel.querySelector("[data-carousel-current]");
        const total = Number(carousel.dataset.total);
        const reduceMotion = window.matchMedia(
            "(prefers-reduced-motion: reduce)",
        ).matches;
        let current = 0;
        let isAnimating = false;
        let autoplayTimer = null;
        let isPointerPaused = false;
        let isFocusPaused = false;

        const transitionDuration = reduceMotion ? 0 : 560;
        const autoplayDelay = 4000;

        function scheduleAutoplay() {
            window.clearTimeout(autoplayTimer);

            if (
                total < 2 ||
                isPointerPaused ||
                isFocusPaused ||
                document.hidden
            ) {
                return;
            }

            autoplayTimer = window.setTimeout(() => {
                showNextItem();
                scheduleAutoplay();
            }, autoplayDelay);
        }

        function updateStatus(direction) {
            current = (current + direction + total) % total;
            currentLabel.textContent = String(current + 1);
        }

        function stepWidth() {
            const firstSlide = track.firstElementChild;
            const gap =
                Number.parseFloat(window.getComputedStyle(track).columnGap) ||
                0;

            return firstSlide.getBoundingClientRect().width + gap;
        }

        function finishMove(callback) {
            window.setTimeout(() => {
                track.style.transition = "none";
                callback();
                track.style.transform = "translate3d(0, 0, 0)";
                track.getBoundingClientRect();
                isAnimating = false;
            }, transitionDuration);
        }

        function showNextItem() {
            if (isAnimating || total < 2) {
                return;
            }

            isAnimating = true;
            const outgoingSlide = track.firstElementChild;
            outgoingSlide.classList.add("is-page-turning-forward");
            track.style.transition = `transform ${transitionDuration}ms cubic-bezier(0.4, 0, 0.2, 1)`;
            track.style.transform = `translate3d(-${stepWidth()}px, 0, 0)`;

            finishMove(() => {
                outgoingSlide.classList.remove("is-page-turning-forward");
                track.append(outgoingSlide);
                updateStatus(1);
            });
        }

        function showPreviousItem() {
            if (isAnimating || total < 2) {
                return;
            }

            isAnimating = true;
            const incomingSlide = track.lastElementChild;
            track.prepend(incomingSlide);
            track.style.transition = "none";
            track.style.transform = `translate3d(-${stepWidth()}px, 0, 0)`;
            track.getBoundingClientRect();
            incomingSlide.classList.add("is-page-turning-backward");
            track.style.transition = `transform ${transitionDuration}ms cubic-bezier(0.4, 0, 0.2, 1)`;
            track.style.transform = "translate3d(0, 0, 0)";

            finishMove(() => {
                incomingSlide.classList.remove("is-page-turning-backward");
                updateStatus(-1);
            });
        }

        nextButton.addEventListener("click", () => {
            showNextItem();
            scheduleAutoplay();
        });
        previousButton.addEventListener("click", () => {
            showPreviousItem();
            scheduleAutoplay();
        });
        viewport.addEventListener("keydown", (event) => {
            if (event.key === "ArrowRight") {
                event.preventDefault();
                showNextItem();
                scheduleAutoplay();
            }

            if (event.key === "ArrowLeft") {
                event.preventDefault();
                showPreviousItem();
                scheduleAutoplay();
            }
        });

        carousel.addEventListener("mouseenter", () => {
            isPointerPaused = true;
            scheduleAutoplay();
        });
        carousel.addEventListener("mouseleave", () => {
            isPointerPaused = false;
            scheduleAutoplay();
        });
        carousel.addEventListener("focusin", () => {
            isFocusPaused = true;
            scheduleAutoplay();
        });
        carousel.addEventListener("focusout", (event) => {
            if (carousel.contains(event.relatedTarget)) {
                return;
            }

            isFocusPaused = false;
            scheduleAutoplay();
        });
        document.addEventListener("visibilitychange", scheduleAutoplay);

        scheduleAutoplay();
    });
});
